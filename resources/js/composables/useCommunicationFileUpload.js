import { ref } from 'vue'
import axios from 'axios'
import { useAutoFillPreference } from './useAutoFillPreference.js'
import { parseMunicipalCommunication, fillMunicipalCommunication } from '../utils/municipalCommunicationAutoFill.js'

const officeExtensions = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']
const allowedExtensions = ['pdf', ...officeExtensions, 'jpg', 'jpeg', 'png']

// Optional text extraction must never prevent previewing or saving the original file.
async function extractPDF(file) {
  const pdfjs = await import('pdfjs-dist')
  pdfjs.GlobalWorkerOptions.workerSrc = new URL('pdfjs-dist/build/pdf.worker.min.mjs', import.meta.url).toString() + '?module=1'
  const task = pdfjs.getDocument({ data: await file.arrayBuffer() })
  try {
    const pdf = await task.promise
    const pages = []
    for (let i = 1; i <= pdf.numPages; i++) {
      const page = await pdf.getPage(i)
      pages.push((await page.getTextContent()).items.map(item => item.str + (item.hasEOL ? '\n' : ' ')).join(''))
    }
    return pages.join('\n')
  } finally {
    await task.destroy()
  }
}

async function extractDOCX(file) {
  const mammoth = await import('mammoth')
  return (await mammoth.extractRawText({ arrayBuffer: await file.arrayBuffer() })).value
}

export function useCommunicationFileUpload({ request = axios.post, extractPDFText = extractPDF, extractDOCXText = extractDOCX, objectUrls = URL, autoFillEnabled: initialAutoFill = true, editing = false, savePreference } = {}) {
  const selectedFiles = ref([])
  const previewObjects = ref([])
  const processingFiles = ref([])
  let generation = 0
  let activeForm = null
  let selectionBaseline = null
  const autoFillMessage = ref('')
  const baseline = form => Object.fromEntries(['title', 'subject', 'year', 'date_received'].map(field => [field, form[field]]))
  const applyAutoFill = () => {
    if (!activeForm || !selectedFiles.value.length || !autoFillEnabled.value || processingFiles.value.some(Boolean)) return
    const text = previewObjects.value[0]?.extractedText || ''
    const details = parseMunicipalCommunication(text, selectedFiles.value[0].name)
    const fields = fillMunicipalCommunication(activeForm, details, selectionBaseline, { editing })
    const labels = { title: 'title', subject: 'municipality', year: 'year', date_received: 'received date' }
    autoFillMessage.value = !text.trim()
      ? 'No readable text was extracted. Suggestions use the filename only. Enter the remaining details manually; scanned documents need OCR to read their text.'
      : fields.length ? `Filled ${fields.map(field => labels[field]).join(', ')}. Review the details before saving.`
        : 'No additional blank details could be identified. Existing values have been kept.'
  }
  const { autoFillEnabled, autoFillSaving, autoFillError, toggleAutoFill } = useAutoFillPreference(initialAutoFill, {
    initiallyDisabled: editing,
    ...(savePreference ? { request: savePreference } : {}),
    onEnable: applyAutoFill,
  })

  const getFileType = file => (file.name || '').split('.').pop().toLowerCase()
  const syncText = form => { form.extracted_text = previewObjects.value[0]?.extractedText || '' }
  const revoke = preview => {
    if (preview?.url?.startsWith('blob:')) objectUrls.revokeObjectURL(preview.url)
  }

  const cleanupAllPreviews = () => {
    generation++
    previewObjects.value.forEach(revoke)
    selectedFiles.value = []
    previewObjects.value = []
    processingFiles.value = []
    autoFillMessage.value = ''
  }

  const handleFileSelect = async (event, form) => {
    const files = Array.from(event.target.files || [])
    event.target.value = '' // Allow choosing the same file again after removal/failure.
    form.clearErrors?.('files', 'files.0')
    if (!files.length) return
    if (files.length !== 1) {
      form.setError('files', 'Choose one attachment per municipal communication.')
      return
    }
    const file = files[0]
    const type = getFileType(file)
    if (!allowedExtensions.includes(type)) {
      form.setError('files', 'Choose a PDF, Word, Excel, PowerPoint, JPG, or PNG file.')
      return
    }
    if (file.size === 0 || file.size > 10 * 1024 * 1024) {
      form.setError('files', 'Choose a non-empty file no larger than 10 MB.')
      return
    }

    cleanupAllPreviews()
    const current = generation
    activeForm = form
    selectionBaseline = baseline(form)
    selectedFiles.value = [file]
    form.files = [file]
    form.extracted_text = ''
    previewObjects.value = [{ type: 'loading', url: null, extractedText: '' }]
    processingFiles.value = [true]
    let preview = { type: 'error', url: null, extractedText: '', content: '' }
    try {
      if (type === 'pdf' || ['jpg', 'jpeg', 'png'].includes(type)) {
        preview = { type: type === 'pdf' ? 'pdf' : 'image', url: objectUrls.createObjectURL(file), extractedText: '' }
        if (type === 'pdf') {
          try { preview.extractedText = await extractPDFText(file) } catch {
            preview.warning = 'Search text could not be extracted. You can still preview and upload this file.'
          }
        }
      } else {
        // Mammoth supports DOCX only. Legacy DOC and other Office formats use the viewer directly.
        if (type === 'docx') {
          try { preview.extractedText = await extractDOCXText(file) } catch {
            preview.warning = 'Search text could not be extracted. You can still upload the original file.'
          }
        }
        const data = new FormData()
        data.append('file', file)
        const response = await request('/api/preview-upload', data, { headers: { Accept: 'application/json' }, timeout: 60000 })
        if (!response.data?.url) throw new Error('Missing preview URL')
        preview = { ...preview, type: 'office', url: response.data.url }
      }
    } catch (error) {
      const status = error.response?.status
      preview = { ...preview, type: 'error', content: status === 413
        ? 'The server rejected the preview size. The attachment is still selected.'
        : 'The preview could not be prepared. You can still upload the original file, or choose it again to retry.' }
    } finally {
      // Ignore a preview that finishes after its file was removed or replaced.
      if (current === generation) {
        preview.extractedText = typeof preview.extractedText === 'string' ? preview.extractedText.replace(/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/g, '') : ''
        previewObjects.value = [preview]
        processingFiles.value = [false]
        syncText(form)
        applyAutoFill()
      } else {
        revoke(preview)
      }
    }
  }

  const removeFile = (_, form) => {
    cleanupAllPreviews()
    form.files = []
    form.extracted_text = ''
  }

  const formatFileSize = bytes => bytes < 1024 ? `${bytes} Bytes` : bytes < 1024 * 1024 ? `${(bytes / 1024).toFixed(1)} KB` : `${(bytes / (1024 * 1024)).toFixed(1)} MB`
  const getOfficeEmbedUrl = url => url ? `https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(url)}` : ''

  return { selectedFiles, previewObjects, processingFiles, handleFileSelect, removeFile, getFileType, formatFileSize, getOfficeEmbedUrl, cleanupAllPreviews, autoFillEnabled, autoFillSaving, autoFillError, autoFillMessage, toggleAutoFill }
}
