import { ref } from 'vue'
import axios from 'axios'

export function useUploadSubmission(form, { request = axios.post, successDelay = 1400 } = {}) {
  const uploadState = ref('idle')
  const uploadProgress = ref(null)
  const uploadMessage = ref('')
  let pending = false

  const submitUpload = async (url, data) => {
    // A second submission must never create a duplicate request.
    if (pending) throw new Error('A submission is already in progress.')
    pending = true
    form.processing = true
    form.clearErrors?.()
    uploadState.value = 'uploading'
    uploadProgress.value = null
    uploadMessage.value = 'Uploading files. Keep this page open.'

    try {
      const response = await request(url, data, {
        headers: { Accept: 'application/json' },
        timeout: 180000,
        onUploadProgress: ({ loaded, total }) => {
          if (!total) return
          uploadProgress.value = Math.min(100, Math.round(loaded * 100 / total))
          if (loaded >= total) {
            uploadState.value = 'saving'
            uploadMessage.value = 'Transfer complete. Waiting for the server to finish saving.'
          }
        },
      })

      if (/\/login(?:[?#]|$)/.test(response.request?.responseURL || '')) {
        const error = new Error('Your session expired. Sign in again before uploading.')
        error.response = { status: 401 }
        throw error
      }

      if (typeof response.data === 'string' && /^\s*</.test(response.data)) {
        const error = new Error('The server returned a page instead of confirming the save.')
        error.response = { status: 502 }
        throw error
      }

      uploadProgress.value = 100
      uploadState.value = 'success'
      uploadMessage.value = 'Upload complete. Your document has been saved.'
      // Let the confirmation appear before the form's existing navigation runs.
      if (successDelay) await new Promise(resolve => setTimeout(resolve, successDelay))
      return response
    } catch (error) {
      const status = error.response?.status
      if (!error.response) {
        uploadState.value = 'network-error'
        uploadMessage.value = 'Connection lost or timed out. Your files are still selected. The server may have saved the document; check the document list before retrying to avoid duplicates.'
      } else {
        uploadState.value = 'error'
        if (status === 422) {
          Object.entries(error.response.data?.errors || {}).forEach(([field, messages]) => {
            form.setError?.(field, Array.isArray(messages) ? messages[0] : String(messages))
          })
          uploadMessage.value = 'The document was not saved. Check the highlighted fields and try again.'
        } else if (status === 413) {
          uploadMessage.value = 'The upload is too large for the server. Choose fewer or smaller files and try again.'
        } else if (status === 401 || status === 419) {
          uploadMessage.value = 'Your session expired. Sign in again before uploading.'
        } else {
          uploadMessage.value = 'The server could not confirm the upload. Your files are still selected. Check the document list before retrying.'
        }
      }
      throw error
    } finally {
      pending = false
      form.processing = false
    }
  }

  return { submitUpload, uploadState, uploadProgress, uploadMessage }
}
