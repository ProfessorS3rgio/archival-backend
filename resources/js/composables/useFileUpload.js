import { ref, watch } from 'vue'
import axios from 'axios'

export function useFileUpload() {
  const selectedFiles = ref([])
  const previewObjects = ref([])
  const fileMetadata = ref([])
  const processingFiles = ref([])
  const mergingFiles = ref(false)
  let formWatcherStop = null

  const sanitizeSegment = (value, maxWords = Infinity) => (value || '')
    .toString().replace(/[_.,]+/g, ' ').split(/\s+/).filter(Boolean).slice(0, maxWords)
    .map(word => word.replace(/[^a-z0-9-]/gi, '').toLowerCase()).filter(Boolean).join('-')

  const extractYear = value => {
    if (!value) return ''
    if (value instanceof Date && !isNaN(value)) return value.getUTCFullYear().toString()
    const year = value.toString().match(/(19|20)\d{2}/)
    if (year) return year[0]
    const date = new Date(value)
    return isNaN(date) ? '' : date.getUTCFullYear().toString()
  }

  const buildFileNameFromForm = (form, metadata, extension, index = 0, total = 1) => {
    const parts = [
      extractYear(form?.date_approved || metadata?.extractedData?.date_approved),
      sanitizeSegment(form?.title?.trim() || metadata?.extractedData?.title, 4),
      sanitizeSegment(form?.author?.trim() || metadata?.extractedData?.author),
    ].filter(Boolean)
    if (!parts.length) return null
    if (total > 1) parts[parts.length - 1] += `-${index + 1}`
    return `${parts.join(',')}${extension}`
  }

  const renameFileWithFormData = (index, form, metadata = {}) => {
    const current = selectedFiles.value[index]
    if (!current) return
    const extension = current.name.includes('.') ? current.name.slice(current.name.lastIndexOf('.')) : ''
    const name = buildFileNameFromForm(form, metadata, extension, index, selectedFiles.value.length)
    if (!name || name === current.name) return
    const renamed = new File([current], name, { type: current.type, lastModified: current.lastModified })
    selectedFiles.value.splice(index, 1, renamed)
    fileMetadata.value.splice(index, 1, { ...metadata, renamed: true })
    if (Array.isArray(form.files)) form.files = [...selectedFiles.value]
    return renamed
  }

  const ensureFormWatcher = form => {
    if (!form || formWatcherStop) return
    formWatcherStop = watch(() => [form.title, form.author, form.date_approved], () => {
      selectedFiles.value.forEach((_, index) => renameFileWithFormData(index, form, fileMetadata.value[index] || {}))
    })
  }

  const syncExtractedTextField = form => {
    if (!form) return
    form.extracted_text = ''
    form.file_extracted_texts = selectedFiles.value.map(() => '')
  }

  const generateFilePreview = async (file, index) => {
    try {
      processingFiles.value[index] = true
      if (file.type === 'application/pdf') {
        previewObjects.value[index] = { type: 'pdf', url: URL.createObjectURL(file), content: null, extractedText: '' }
      } else {
        const data = new FormData()
        data.append('file', file)
        const response = await axios.post('/api/preview-upload', data, {
          headers: { 'Content-Type': 'multipart/form-data' }, withCredentials: true,
        })
        if (response.data?.error) throw new Error(response.data.error)
        previewObjects.value[index] = { type: 'office', url: response.data?.url || null, content: null, extractedText: '' }
      }
      fileMetadata.value[index] ||= {}
    } catch (error) {
      previewObjects.value[index] = { type: 'error', url: null, content: error.response?.data?.error || error.message || 'Preview not available', extractedText: '' }
    } finally {
      processingFiles.value[index] = false
    }
  }

  const cleanupPreview = index => {
    const url = previewObjects.value[index]?.url
    if (typeof url === 'string' && url.startsWith('blob:')) URL.revokeObjectURL(url)
    previewObjects.value[index] = null
  }

  const handleFileSelect = async (event, form) => {
    const files = Array.from(event.target.files || [])
    event.target.value = ''
    const valid = files.filter(file => {
      const types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
      if (!types.includes(file.type)) { alert(`${file.name} is not a valid file type. Please upload PDF, DOC, or DOCX files only.`); return false }
      if (file.size > 10 * 1024 * 1024) { alert(`${file.name} is too large. Maximum file size is 10MB.`); return false }
      return true
    })
    const start = selectedFiles.value.length
    valid.forEach(file => { selectedFiles.value.push(file); fileMetadata.value.push({}); processingFiles.value.push(false) })
    form.files = [...selectedFiles.value]
    ensureFormWatcher(form)
    for (const [offset, file] of valid.entries()) await generateFilePreview(file, start + offset)
    syncExtractedTextField(form)
  }

  const removeFile = (index, form) => {
    cleanupPreview(index)
    selectedFiles.value.splice(index, 1)
    previewObjects.value.splice(index, 1)
    fileMetadata.value.splice(index, 1)
    processingFiles.value.splice(index, 1)
    form.files = [...selectedFiles.value]
    syncExtractedTextField(form)
    if (!selectedFiles.value.length && formWatcherStop) { formWatcherStop(); formWatcherStop = null }
  }

  const mergeSelectedFiles = async form => {
    if (selectedFiles.value.length < 2 || selectedFiles.value.some(file => file.type !== 'application/pdf')) throw new Error('Select at least two PDF files to merge.')
    mergingFiles.value = true
    try {
      const data = new FormData()
      selectedFiles.value.forEach(file => data.append('files[]', file))
      const response = await axios.post('/merge-pdfs', data, {
        headers: { 'Content-Type': 'multipart/form-data', Accept: 'application/pdf' }, responseType: 'blob', withCredentials: true, timeout: 180000,
      })
      const disposition = response.headers?.['content-disposition'] || ''
      const encoded = disposition.match(/filename\*=UTF-8''([^;]+)/i)?.[1]
      const plain = disposition.match(/filename="?([^";]+)"?/i)?.[1]
      const name = encoded ? decodeURIComponent(encoded) : (plain || 'merged-document.pdf')
      const merged = new File([response.data], name, { type: 'application/pdf' })
      cleanupAllPreviews()
      selectedFiles.value = [merged]
      previewObjects.value = [{ type: 'pdf', url: URL.createObjectURL(merged), content: null, extractedText: '' }]
      fileMetadata.value = [{}]
      processingFiles.value = [false]
      form.files = [merged]
      syncExtractedTextField(form)
    } catch (error) {
      form.setError?.('files', error?.message || 'The PDFs could not be merged. Please try again.')
    } finally { mergingFiles.value = false }
  }

  const cleanupAllPreviews = () => {
    previewObjects.value.forEach(preview => { if (preview?.url?.startsWith('blob:')) URL.revokeObjectURL(preview.url) })
    previewObjects.value = []
    processingFiles.value = []
    if (formWatcherStop) { formWatcherStop(); formWatcherStop = null }
  }

  const getFileType = file => file.type === 'application/pdf' ? 'pdf' : file.type === 'application/msword' ? 'doc' : 'docx'
  const formatFileSize = bytes => bytes === 0 ? '0 Bytes' : `${parseFloat((bytes / 1024 ** Math.floor(Math.log(bytes) / Math.log(1024))).toFixed(2))} ${['Bytes', 'KB', 'MB', 'GB'][Math.floor(Math.log(bytes) / Math.log(1024))]}`
  const getOfficeEmbedUrl = url => url ? `https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(url)}` : ''

  return { selectedFiles, previewObjects, processingFiles, mergingFiles, handleFileSelect, removeFile, mergeSelectedFiles, getFileType, formatFileSize, cleanupAllPreviews, getOfficeEmbedUrl }
}
