import { ref } from 'vue'
import axios from 'axios'

const officeExtensions = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']
const allowedExtensions = ['pdf', ...officeExtensions, 'jpg', 'jpeg', 'png']

export function useCommunicationFileUpload({ request = axios.post, objectUrls = URL } = {}) {
  const selectedFiles = ref([])
  const previewObjects = ref([])
  const processingFiles = ref([])
  let generation = 0

  const getFileType = file => (file.name || '').split('.').pop().toLowerCase()
  const revoke = preview => { if (preview?.url?.startsWith('blob:')) objectUrls.revokeObjectURL(preview.url) }

  const cleanupAllPreviews = () => {
    generation++
    previewObjects.value.forEach(revoke)
    selectedFiles.value = []
    previewObjects.value = []
    processingFiles.value = []
  }

  const handleFileSelect = async (event, form) => {
    const files = Array.from(event.target.files || [])
    event.target.value = ''
    form.clearErrors?.('files', 'files.0')
    if (!files.length) return
    if (files.length !== 1) { form.setError('files', 'Choose one attachment per municipal communication.'); return }
    const file = files[0]
    const type = getFileType(file)
    if (!allowedExtensions.includes(type)) { form.setError('files', 'Choose a PDF, Word, Excel, PowerPoint, JPG, or PNG file.'); return }
    if (!file.size || file.size > 10 * 1024 * 1024) { form.setError('files', 'Choose a non-empty file no larger than 10 MB.'); return }

    cleanupAllPreviews()
    const current = generation
    selectedFiles.value = [file]
    form.files = [file]
    form.extracted_text = ''
    processingFiles.value = [false]
    try {
      if (type === 'pdf' || ['jpg', 'jpeg', 'png'].includes(type)) {
        previewObjects.value = [{ type: type === 'pdf' ? 'pdf' : 'image', url: objectUrls.createObjectURL(file), extractedText: '' }]
        return
      }
      processingFiles.value = [true]
      const data = new FormData()
      data.append('file', file)
      const response = await request('/api/preview-upload', data, { headers: { Accept: 'application/json' }, timeout: 60000 })
      if (!response.data?.url) throw new Error('Missing preview URL')
      if (current === generation) previewObjects.value = [{ type: 'office', url: response.data.url, extractedText: '' }]
    } catch (error) {
      const status = error.response?.status
      if (current === generation) previewObjects.value = [{ type: 'error', url: null, extractedText: '', content: status === 413
        ? 'The server rejected the preview size. The attachment is still selected.'
        : 'The preview could not be prepared. You can still upload the original file, or choose it again to retry.' }]
    } finally {
      if (current === generation) processingFiles.value = [false]
    }
  }

  const removeFile = (_, form) => {
    cleanupAllPreviews()
    form.files = []
    form.extracted_text = ''
  }

  const formatFileSize = bytes => bytes < 1024 ? `${bytes} Bytes` : bytes < 1024 * 1024 ? `${(bytes / 1024).toFixed(1)} KB` : `${(bytes / (1024 * 1024)).toFixed(1)} MB`
  const getOfficeEmbedUrl = url => url ? `https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(url)}` : ''

  return { selectedFiles, previewObjects, processingFiles, handleFileSelect, removeFile, getFileType, formatFileSize, getOfficeEmbedUrl, cleanupAllPreviews }
}
