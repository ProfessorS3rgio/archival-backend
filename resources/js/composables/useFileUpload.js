import { ref, watch } from 'vue'
import axios from 'axios'
import mammoth from 'mammoth'
import * as pdfjsLib from 'pdfjs-dist'

// Configure PDF.js worker
pdfjsLib.GlobalWorkerOptions.workerSrc = new URL('pdfjs-dist/build/pdf.worker.min.mjs', import.meta.url).toString()

export function useFileUpload(autoFillEnabled = true) {
  const isEditPage = typeof window !== 'undefined' && /\/edit$/i.test(window.location.pathname)
  const selectedFiles = ref([])
  const previewObjects = ref([])
  const fileMetadata = ref([])
  const processingFiles = ref([]) // Track which files are being processed
  const autoFillEnabledRef = ref(autoFillEnabled && !isEditPage)
  let formWatcherStop = null

  const SETTINGS_URL = 'http://127.0.0.1:8000/settings/auto-fill'

  const saveAutoFillSetting = async (enabled) => {
    console.log('[FileUpload] saving auto-fill ->', enabled, 'URL:', SETTINGS_URL)
    try {
      const resp = await axios.patch(SETTINGS_URL, { auto_fill: enabled })
      console.log('[FileUpload] save response:', resp && resp.data)
      return resp
    } catch (error) {
      console.error('[FileUpload] Error saving auto-fill setting:', error)
      try {
        console.error('[FileUpload] error.config.url:', error.config && error.config.url)
        console.error('[FileUpload] error.request.responseURL:', error.request && error.request.responseURL)
      } catch (e) {
        // ignore
      }
      if (error.response) {
        console.error('[FileUpload] response data:', error.response.data)
        console.error('[FileUpload] response status:', error.response.status)
      }
      // don't rethrow to avoid breaking UI toggle
    }
  }

  const toggleAutoFill = async (e) => {
    if (e && e.preventDefault) e.preventDefault()
    if (isEditPage) {
      console.log('[FileUpload] Auto-fill is disabled on edit pages')
      autoFillEnabledRef.value = false
      return
    }
    const newValue = !autoFillEnabledRef.value
    autoFillEnabledRef.value = newValue
    await saveAutoFillSetting(newValue)
  }

  const extractTextFromPDF = async (file) => {
    console.log('Extracting text from PDF:', file.name)
    const arrayBuffer = await file.arrayBuffer()
    const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise
    let text = ''
    for (let i = 1; i <= pdf.numPages; i++) {
      const page = await pdf.getPage(i)
      const textContent = await page.getTextContent()
      text += textContent.items.map(item => item.str).join(' ') + '\n'
    }
    console.log('Extracted PDF text:', text.substring(0, 500) + '...')
    return text
  }

  const extractTextFromDOCX = async (file) => {
    console.log('Extracting text from DOCX:', file.name)
    const arrayBuffer = await file.arrayBuffer()
    const result = await mammoth.extractRawText({ arrayBuffer })
    console.log('Extracted DOCX text:', result.value.substring(0, 500) + '...')
    return result.value
  }

  const parseDocumentText = (text) => {
    console.log('Parsing document text:', text.substring(0, 500) + '...')
    const data = {}

    // Updated patterns for ordinance/resolution documents
    const patterns = {
      ordinance_no: /(?:ORDINANCE\s+NO\.?|NO\.?|NUMBER)\s*:?\s*(\d+(?:-\d+)?)/i,
      resolution_no: /(?:RESOLUTION\s+NO\.?)\s*:?\s*([\d\s-]+)/i,
      author: /(?:Provincial\s+)?Governor\s+([^,\n]+)|Author:\s*([^,\n]+)/i,
      title: /A\s+(?:RESOLUTION|ORDINANCE)\s+([^.]+)/i,
      subject: /(?:WHEREAS\s*,?\s*)+([^.]+)\./i,
      session: /(\d+\s*(?:st|nd|rd|th)\s+REGULAR\s+SESSION)/i,
      date_approved: /(?:ADOPTED|APPROVED|APPROVE|ENACTED)\s+on\s+this\s+(\d+)\s*(?:st|nd|rd|th)?\s+day\s+of\s+(\w+)\s+(\d{4})/i
    }

    Object.keys(patterns).forEach(key => {
      const match = text.match(patterns[key])
      console.log(`Pattern ${key}: match =`, match)
      if (match) {
        if (key === 'author') {
          const name = match[1] || match[2]
          if (name) data[key] = name.trim()
        } else if (key === 'title') {
          // Limit title to 6 words
          const fullTitle = match[1].trim()
          const words = fullTitle.split(/\s+/).slice(0, 6)
          data[key] = words.join(' ')
        } else if (key === 'date_approved' && match[2] && match[3]) {
          // Combine date parts
          const day = parseInt(match[1])
          const month = match[2]
          const year = parseInt(match[3])
          const months = { 'January': 0, 'February': 1, 'March': 2, 'April': 3, 'May': 4, 'June': 5, 'July': 6, 'August': 7, 'September': 8, 'October': 9, 'November': 10, 'December': 11 }
          const monthIndex = months[month]
          if (monthIndex !== undefined) {
            const date = new Date(Date.UTC(year, monthIndex, day))
            data[key] = date.toISOString().split('T')[0]
          }
        } else if (match[1]) {
          data[key] = match[1].trim()
        }
      }
    })

    console.log('Parsed data:', data)
    return data
  }

  const sanitizeSegment = (value, maxWords = Infinity) => {
    if (!value) return ''

    return value
      .toString()
      .replace(/[_.,]+/g, ' ')
      .split(/\s+/)
      .filter(Boolean)
      .slice(0, maxWords)
      .map(word => word.replace(/[^a-z0-9-]/gi, '').toLowerCase())
      .filter(Boolean)
      .join('-')
  }

  const extractYear = (value) => {
    if (!value) return ''

    if (value instanceof Date && !isNaN(value)) {
      return value.getUTCFullYear().toString()
    }

    const stringValue = value.toString().trim()
    const yearMatch = stringValue.match(/(19|20)\d{2}/)
    if (yearMatch) {
      return yearMatch[0]
    }

    const parsed = new Date(stringValue)
    if (!isNaN(parsed)) {
      return parsed.getUTCFullYear().toString()
    }

    return ''
  }

  const buildFileNameFromForm = (form, metadata, originalExtension, index = 0, totalFiles = 1) => {
    const titleSource = form?.title?.trim() || metadata?.extractedData?.title
    const authorSource = form?.author?.trim() || metadata?.extractedData?.author
    const dateSource = form?.date_approved || metadata?.extractedData?.date_approved

    const year = extractYear(dateSource)
    const titleSegment = sanitizeSegment(titleSource, 4)
    const authorSegment = sanitizeSegment(authorSource)

    const segments = [year, titleSegment, authorSegment].filter(Boolean)
    if (!segments.length) return null

    const numberedSegments = [...segments]
    if (totalFiles > 1) {
      const suffix = (index ?? 0) + 1
      if (numberedSegments.length > 0) {
        const lastSegment = numberedSegments.pop()
        numberedSegments.push(`${lastSegment}-${suffix}`)
      } else {
        numberedSegments.push(`${suffix}`)
      }
    }

    const extension = originalExtension
      ? (originalExtension.startsWith('.') ? originalExtension : `.${originalExtension}`)
      : ''

    return `${numberedSegments.join(',')}${extension}`
  }

  const renameFileWithFormData = (index, form, metadata = {}) => {
    if (index === undefined || index === null) return

    const currentFile = selectedFiles.value[index]
    if (!currentFile) return

    const originalExtension = currentFile.name.includes('.')
      ? currentFile.name.slice(currentFile.name.lastIndexOf('.'))
      : ''

    const newName = buildFileNameFromForm(
      form,
      metadata,
      originalExtension,
      index,
      selectedFiles.value.length
    )
    if (!newName || newName === currentFile.name) return

    const renamedFile = new File([currentFile], newName, {
      type: currentFile.type,
      lastModified: currentFile.lastModified
    })

    selectedFiles.value.splice(index, 1, renamedFile)
    fileMetadata.value.splice(index, 1, {
      ...metadata,
      renamed: true
    })

    if (Array.isArray(form.files)) {
      form.files = [...selectedFiles.value]
    }

    return renamedFile
  }

  const ensureFormWatcher = (form) => {
    if (!form || formWatcherStop) return

    formWatcherStop = watch(
      () => [form.title, form.author, form.date_approved],
      () => {
        selectedFiles.value.forEach((_, index) => {
          const metadata = fileMetadata.value[index] || {}
          renameFileWithFormData(index, form, metadata)
        })
      }
    )
  }

  const syncExtractedTextField = (form) => {
    if (!form) return

    const texts = selectedFiles.value.map((_, idx) => {
      const preview = previewObjects.value[idx]
      if (preview && typeof preview.extractedText === 'string') {
        return preview.extractedText
      }
      return ''
    })

    // Join all extracted texts with double newlines for the main record (comprehensive full-text search)
    const filteredTexts = texts.filter(text => text.trim())
    form.extracted_text = filteredTexts.join('\n\n')

    // Also store per-file extracted text as an array for individual file records
    form.file_extracted_texts = texts
  }

  const autoFillForm = async (file, form, fileIndex) => {
    if (!autoFillEnabledRef.value || isEditPage) {
      console.log('Auto-fill is disabled')
      return
    }

    ensureFormWatcher(form)

    console.log('Starting auto-fill for file:', file.name, 'Type:', file.type)
    try {
      let text = ''
      if (file.type === 'application/pdf') {
        console.log('Processing as PDF')
        const index = selectedFiles.value.indexOf(file)
        if (index !== -1 && previewObjects.value[index] && previewObjects.value[index].extractedText) {
          text = previewObjects.value[index].extractedText
        } else {
          text = await extractTextFromPDF(file)
          // Store the extracted text for later use
          if (index !== -1 && previewObjects.value[index]) {
            previewObjects.value[index].extractedText = text
          }
        }
      } else if (file.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
        console.log('Using extracted text from preview for DOCX')
        const index = selectedFiles.value.indexOf(file)
        if (index !== -1 && previewObjects.value[index] && previewObjects.value[index].extractedText) {
          text = previewObjects.value[index].extractedText
        } else {
          // Fallback: extract again if not available
          text = await extractTextFromDOCX(file)
        }
        console.log('Extracted text from DOCX:', text.substring(0, 500) + '...')
      } else if (file.type === 'application/msword') {
        console.log('DOC file - extracting text via backend conversion')
        const formData = new FormData()
        formData.append('file', file)
        const response = await axios.post('/api/convert-file', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
          withCredentials: true
        })
        if (response.data.error) {
          throw new Error(response.data.error)
        }
        text = response.data.text
        console.log('Extracted text from DOC via backend:', text.substring(0, 500) + '...')
      } else {
        console.log('Unsupported file type:', file.type)
        return
      }

      if (text) {
        const extractedData = parseDocumentText(text)

        if (fileIndex !== undefined && fileIndex !== null) {
          fileMetadata.value[fileIndex] = {
            ...(fileMetadata.value[fileIndex] || {}),
            extractedData
          }

          renameFileWithFormData(fileIndex, form, fileMetadata.value[fileIndex])
        }

        // Auto-fill form fields
        if (extractedData.ordinance_no || extractedData.resolution_no) {
          form.ordinance_no = extractedData.ordinance_no
          form.resolution_no = extractedData.resolution_no
          console.log('Set ordinance_no/resolution_no:', extractedData.ordinance_no || extractedData.resolution_no)
        }
        if (extractedData.author) {
          form.author = extractedData.author
          console.log('Set author:', extractedData.author)
        }
        if (extractedData.title) {
          form.title = extractedData.title
          console.log('Set title:', extractedData.title)
        }
        // Subject is intentionally not auto-filled to allow manual selection
        if (extractedData.session) {
          form.session = extractedData.session
          console.log('Set session:', extractedData.session)
        }
        if (extractedData.date_approved) {
          // Try to parse date
          const date = new Date(extractedData.date_approved)
          if (!isNaN(date)) {
            form.date_approved = date.toISOString().split('T')[0]
            console.log('Set date_approved:', form.date_approved)
          } else {
            console.log('Could not parse date:', extractedData.date_approved)
          }
        }
        console.log('Auto-fill completed successfully')
      } else {
        console.log('No text extracted from document')
      }
    } catch (error) {
      console.error('Error extracting data from document:', error)
    } finally {
      syncExtractedTextField(form)
    }
  }

  const generateFilePreview = async (file, index, form) => {
    try {
      // Mark file as processing and initialize preview object with loading state
      processingFiles.value[index] = true
      previewObjects.value[index] = {
        type: 'loading',
        url: null,
        content: null,
        extractedText: null
      }

      if (file.type === 'application/pdf') {
        // Create object URL for PDF preview
        const objectUrl = URL.createObjectURL(file)
        
        // Extract text from PDF for search functionality
        let extractedText = null
        try {
          extractedText = await extractTextFromPDF(file)
          console.log('PDF text extracted successfully, length:', extractedText?.length)
        } catch (err) {
          console.error('Failed to extract PDF text:', err)
        }
        
        previewObjects.value[index] = {
          type: 'pdf',
          url: objectUrl,
          content: null,
          extractedText: extractedText
        }
      } else if (
        file.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ||
        file.type === 'application/msword'
      ) {
        // DOCX/DOC: upload temporarily and preview via Office Online embed (requires public signed URL)
        let extractedText = null
        if (file.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
          try {
            extractedText = await extractTextFromDOCX(file)
            console.log('DOCX text extracted successfully, length:', extractedText?.length)
          } catch (err) {
            console.error('Failed to extract DOCX text:', err)
          }
        }

        const uploadForm = new FormData()
        uploadForm.append('file', file)
        const resp = await axios.post('/api/preview-upload', uploadForm, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
          withCredentials: true
        })

        if (resp.data?.error) {
          throw new Error(resp.data.error)
        }

        previewObjects.value[index] = {
          type: 'office',
          url: resp.data?.url || null,
          content: null,
          extractedText: extractedText
        }
      }

      fileMetadata.value[index] = fileMetadata.value[index] || {}
      renameFileWithFormData(index, form, fileMetadata.value[index])
      syncExtractedTextField(form)
    } catch (error) {
      console.error('Error generating preview for file:', file.name, error)

      // Extract error message from response if available
      let errorMessage = 'Preview not available'
      if (error.response && error.response.data && error.response.data.error) {
        errorMessage = error.response.data.error
      } else if (error.message) {
        errorMessage = error.message
      }

      previewObjects.value[index] = {
        type: 'error',
        url: null,
        content: errorMessage,
        extractedText: null
      }
    } finally {
      // Mark file as no longer processing
      processingFiles.value[index] = false
    }
  }

  const cleanupPreview = (index) => {
    const url = previewObjects.value[index] && previewObjects.value[index].url
    if (url && typeof url === 'string' && url.startsWith('blob:')) {
      URL.revokeObjectURL(url)
    }
    previewObjects.value[index] = null
  }

  const handleFileSelect = async (event, form) => {
    const files = Array.from(event.target.files)
    const validFiles = files.filter(file => {
      const validTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
      const maxSize = 10 * 1024 * 1024 // 10MB

      if (!validTypes.includes(file.type)) {
        alert(`${file.name} is not a valid file type. Please upload PDF, DOC, or DOCX files only.`)
        return false
      }

      if (file.size > maxSize) {
        alert(`${file.name} is too large. Maximum file size is 10MB.`)
        return false
      }

      return true
    })

    const startingIndex = selectedFiles.value.length

    validFiles.forEach(() => {
      fileMetadata.value.push({})
      processingFiles.value.push(false) // Initialize processing state for each file
    })

    validFiles.forEach(file => {
      selectedFiles.value.push(file)
    })

    // Assign a plain array so Inertia can detect File objects properly
    form.files = [...selectedFiles.value]

    ensureFormWatcher(form)

    // Generate previews for new files
    for (const [i, file] of validFiles.entries()) {
      const fileIndex = startingIndex + i
      await generateFilePreview(file, fileIndex, form)
    }

    // Auto-fill form from the first valid file
    if (validFiles.length > 0) {
      await autoFillForm(selectedFiles.value[startingIndex], form, startingIndex)
    }
  }

  const removeFile = (index, form) => {
    // Clean up preview for this file
    cleanupPreview(index)

  selectedFiles.value.splice(index, 1)
  previewObjects.value.splice(index, 1)
  fileMetadata.value.splice(index, 1)
  processingFiles.value.splice(index, 1) // Remove processing state
  // Keep form.files as a plain array
  form.files = [...selectedFiles.value]

  syncExtractedTextField(form)

  selectedFiles.value.forEach((_, idx) => {
    const metadata = fileMetadata.value[idx] || {}
    renameFileWithFormData(idx, form, metadata)
  })

  if (selectedFiles.value.length === 0 && formWatcherStop) {
    formWatcherStop()
    formWatcherStop = null
  }
  }

  const getFileType = (file) => {
    if (file.type === 'application/pdf') return 'pdf'
    if (file.type === 'application/msword') return 'doc'
    return 'docx'
  }

  const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes'
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
  }

  const cleanupAllPreviews = () => {
    previewObjects.value.forEach(preview => {
      const url = preview && preview.url
      if (url && typeof url === 'string' && url.startsWith('blob:')) {
        URL.revokeObjectURL(url)
      }
    })

    // Reset processing states
    processingFiles.value = []

    if (formWatcherStop) {
      formWatcherStop()
      formWatcherStop = null
    }
  }

  return {
    selectedFiles,
    previewObjects,
    processingFiles,
    autoFillEnabled: autoFillEnabledRef,
    handleFileSelect,
    removeFile,
    getFileType,
    formatFileSize,
    toggleAutoFill,
    cleanupAllPreviews
  }
}