import { ref, watch } from 'vue'
import axios from 'axios'
import { useAutoFillPreference } from './useAutoFillPreference.js'

export function useFileUpload(autoFillEnabled = true, { extractPDFText = null, extractScannedPDFText = null } = {}) {
  const isEditPage = typeof window !== 'undefined' && /\/edit$/i.test(window.location.pathname)
  const selectedFiles = ref([])
  const previewObjects = ref([])
  const fileMetadata = ref([])
  const processingFiles = ref([]) // Track which files are being processed
  const autoFillBusy = ref(false)
  const mergingFiles = ref(false)
  let formWatcherStop = null
  let activeForm = null
  const {
    autoFillEnabled: autoFillEnabledRef,
    autoFillSaving,
    autoFillError,
    toggleAutoFill,
  } = useAutoFillPreference(autoFillEnabled, {
    initiallyDisabled: isEditPage,
    onEnable: async () => {
      if (activeForm && selectedFiles.value.length) {
        await autoFillForm(selectedFiles.value[0], activeForm, 0)
      }
    },
  })

  const extractTextFromPDF = async (file) => {
    console.log('Extracting text from PDF:', file.name)
    let text = ''
    if (extractPDFText) {
      text = await extractPDFText(file)
    } else {
      try {
        const pdfjsLib = await import('pdfjs-dist')
        pdfjsLib.GlobalWorkerOptions.workerSrc = new URL('pdfjs-dist/build/pdf.worker.min.mjs', import.meta.url).toString() + '?module=1'
        const arrayBuffer = await file.arrayBuffer()
        const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise
        for (let i = 1; i <= pdf.numPages; i++) {
          const page = await pdf.getPage(i)
          const textContent = await page.getTextContent()
          text += textContent.items.map(item => item.str).join(' ') + '\n'
        }
      } catch (error) {
        console.error('PDF.js could not read the text layer; trying server OCR for auto-fill:', error)
      }
    }
    // PDF.js cannot read scanned pages. OCR here is only for form autofill;
    // the queued job independently owns the saved extracted_text.
    if (!text.trim()) {
      if (extractScannedPDFText) {
        text = await extractScannedPDFText(file)
      } else {
        const data = new FormData()
        data.append('file', file)
        const response = await axios.post('/api/convert-file', data, {
          headers: { 'Content-Type': 'multipart/form-data' },
          withCredentials: true,
          timeout: 180000,
        })
        if (response.data?.error) throw new Error(response.data.error)
        text = typeof response.data?.text === 'string' ? response.data.text : ''
      }
    }
    console.log('Extracted PDF text:', text.substring(0, 500) + '...')
    return text
  }

  // Office text extraction is intentionally server-side. Windows uses
  // comtypes/Word and Linux uses LibreOffice through /api/convert-file.
  const extractTextFromOffice = async (file) => {
    const data = new FormData()
    data.append('file', file)
    const response = await axios.post('/api/convert-file', data, {
      headers: { 'Content-Type': 'multipart/form-data' },
      withCredentials: true,
      timeout: 120000,
    })

    if (response.data?.error) {
      throw new Error(response.data.error)
    }

    return typeof response.data?.text === 'string' ? response.data.text : ''
  }

  const cleanTitleCandidate = (value) => value
    .replace(/^[^A-Za-z]+/, '')
    .replace(/\b(?:EXPLANATORY\s+NOTE|WHEREAS|BE\s+IT)\b[\s\S]*$/i, '')
    .replace(/\s+/g, ' ')
    .replace(/[,:;.-]+$/, '')
    .trim()

  const parseDocumentText = (text, fileName = '') => {
    console.log('Parsing document text:', text.substring(0, 500) + '...')
    const data = {}

    const patterns = {
      ordinance_no: /(?:ORDINANCE\s+NO\.?|NO\.?|NUMBER)\s*:?\s*(\d+(?:-\d+)?)/i,
      resolution_no: /(?:RESOLUTION\s+NO\.?)\s*:?\s*([\d\s-]+)/i,
      subject: /(?:WHEREAS\s*,?\s*)+([^.]+)\./i,
      session: /(\d{1,3})\s*(?:st|nd|rd|th|["”'′`]*)\s+REGULAR\s+SESSION/i,
      date_approved: /(?:ADOPTED|APPROVED|APPROVE|ENACTED)\s+on\s+this\s+(\d+)\s*(?:st|nd|rd|th)?\s+day\s+of\s+(\w+)\s+(\d{4})/i
    }

    Object.keys(patterns).forEach(key => {
      const match = text.match(patterns[key])
      console.log(`Pattern ${key}: match =`, match)
      if (!match) return

      if (key === 'date_approved' && match[2] && match[3]) {
        const day = parseInt(match[1])
        const month = match[2]
        const year = parseInt(match[3])
        const months = { january: 0, february: 1, march: 2, april: 3, may: 4, june: 5, july: 6, august: 7, september: 8, october: 9, november: 10, december: 11 }
        const monthIndex = months[month.toLowerCase()]
        if (monthIndex !== undefined) {
          data[key] = new Date(Date.UTC(year, monthIndex, day)).toISOString().split('T')[0]
        }
      } else if (key === 'session' && match[1]) {
        const number = parseInt(match[1], 10)
        const ordinal = number % 100 >= 11 && number % 100 <= 13
          ? 'th'
          : ({ 1: 'st', 2: 'nd', 3: 'rd' }[number % 10] || 'th')
        data[key] = `${number}${ordinal} REGULAR SESSION`
      } else if (match[1]) {
        data[key] = match[1].trim()
      }
    })

    // Resolutions and ordinances commonly use “ENACTED ON October 13, 2026”
    // instead of “approved on this 13th day of October 2026”.
    if (!data.date_approved) {
      const enactedMatch = text.match(/(?:ENACTED|APPROVED|ADOPTED)\s+ON\s+(?:THIS\s+)?(January|February|March|April|May|June|July|August|September|October|November|December)\s+(\d{1,2})(?:st|nd|rd|th)?\s*,?\s*((?:19|20)\d{1,2})/i)
      if (enactedMatch) {
        const months = { january: 0, february: 1, march: 2, april: 3, may: 4, june: 5, july: 6, august: 7, september: 8, october: 9, november: 10, december: 11 }
        const monthIndex = months[enactedMatch[1].toLowerCase()]
        let year = enactedMatch[3]
        if (year.length === 3 && /^(19|20)\d{2}/.test(fileName)) {
          year = fileName.match(/^(?:19|20)\d{2}/)[0]
        }
        if (monthIndex !== undefined && year.length === 4) {
          data.date_approved = new Date(Date.UTC(parseInt(year, 10), monthIndex, parseInt(enactedMatch[2], 10))).toISOString().split('T')[0]
        }
      }
    }

    // Prefer the actual heading. This avoids treating “A Ordinance No.” as
    // a title and returning only “No”.
    const headingMatch = text.match(/\b(?:AN\s+ORDINANCE|A\s+RESOLUTION)\s+(?:NO\.?\s*[\d-]+\s*)?([^\r\n]+)/i)
    const headingTitle = headingMatch ? cleanTitleCandidate(headingMatch[1]) : ''
    if (headingTitle && headingTitle.split(/\s+/).length >= 2) {
      data.title = headingTitle.split(/\s+/).slice(0, 12).join(' ')
    }

    // OCR often interleaves the cover heading with names in a sidebar. The
    // ordinance's formal title clause is a steadier source in that case.
    if (!data.title) {
      const titleClause = text.match(/Section\s*1\.?\s*Title\.?\s*This\s+Ordinance\s+shall\s+be\s+known[\s\S]{0,120}?["“]([^"”]{10,250})["”]/i)
      if (titleClause) {
        data.title = titleClause[1].replace(/\s+/g, ' ').replace(/[.\s]+$/, '').trim().split(/\s+/).slice(0, 12).join(' ')
      }
    }

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
    if (!autoFillEnabledRef.value) {
      return
    }

    ensureFormWatcher(form)
    autoFillBusy.value = true

    console.log('Starting auto-fill for file:', file.name, 'Type:', file.type)
    try {
      let text = ''
      if (file.type === 'application/pdf') {
        console.log('Processing as PDF')
        const index = selectedFiles.value.indexOf(file)
        const preview = index !== -1 ? previewObjects.value[index] : null
        if (preview?.autoFillText) {
          text = preview.autoFillText
        } else {
          text = await extractTextFromPDF(file)
          // The file may have been removed or replaced while PDF.js ran.
          if (index === -1 || previewObjects.value[index] !== preview) return
          // Keep PDF.js text for form autofill only. The server job owns the
          // saved PDF's extracted_text, including scanned-file OCR.
          preview.autoFillText = text
        }
      } else if (file.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
        console.log('Extracting DOCX text through the server conversion environment')
        const index = selectedFiles.value.indexOf(file)
        if (index !== -1 && previewObjects.value[index] && previewObjects.value[index].extractedText) {
          text = previewObjects.value[index].extractedText
        } else {
          text = await extractTextFromOffice(file)
        }
        console.log('Extracted text from DOCX:', text.substring(0, 500) + '...')
      } else if (file.type === 'application/msword') {
        console.log('Extracting DOC text through the server conversion environment')
        text = await extractTextFromOffice(file)
        console.log('Extracted text from DOC via backend:', text.substring(0, 500) + '...')
      } else {
        console.log('Unsupported file type:', file.type)
        return
      }

      if (text) {
        const extractedData = parseDocumentText(text, file.name)

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
      autoFillBusy.value = false
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
        // Preview the selected PDF immediately. Search text is indexed from
        // the stored file by the queue after Create.
        const objectUrl = URL.createObjectURL(file)
        previewObjects.value[index] = {
          type: 'pdf',
          url: objectUrl,
          content: null,
          extractedText: '',
          autoFillText: ''
        }
      } else if (
        file.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ||
        file.type === 'application/msword'
      ) {
        // DOCX/DOC: keep the original file and preview it through Office
        // Online. The conversion endpoint is used separately for extracted_text.
        let extractedText = null
        if (file.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
          try {
            extractedText = await extractTextFromOffice(file)
            console.log('DOCX text extracted successfully, length:', extractedText?.length)
          } catch (err) {
            console.error('Failed to extract DOCX text:', err)
          }
        } else {
          // Legacy DOC files cannot be parsed in the browser. Use the same
          // server conversion endpoint used by auto-fill.
          try {
            extractedText = await extractTextFromOffice(file)
            console.log('DOC text extracted successfully, length:', extractedText.length)
          } catch (err) {
            console.error('Failed to extract DOC text:', err)
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
    activeForm = form
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

  const mergeSelectedFiles = async (form) => {
    if (selectedFiles.value.length < 2 || selectedFiles.value.some(file => file.type !== 'application/pdf')) {
      throw new Error('Select at least two PDF files to merge.')
    }

    mergingFiles.value = true
    try {
      const data = new FormData()
      selectedFiles.value.forEach(file => data.append('files[]', file))
      const response = await axios.post('/merge-pdfs', data, {
        headers: { 'Content-Type': 'multipart/form-data', Accept: 'application/pdf' },
        responseType: 'blob',
        withCredentials: true,
        timeout: 180000,
      })
      const mergedText = typeof form.extracted_text === 'string' ? form.extracted_text : ''
      const disposition = response.headers?.['content-disposition'] || ''
      const encodedName = disposition.match(/filename\*=UTF-8''([^;]+)/i)?.[1]
      const plainName = disposition.match(/filename="?([^";]+)"?/i)?.[1]
      const mergedName = encodedName
        ? decodeURIComponent(encodedName)
        : (plainName || `${selectedFiles.value[0]?.name?.replace(/\.pdf$/i, '') || 'merged-document'}.pdf`)
      const merged = new File([response.data], mergedName, { type: 'application/pdf' })
      cleanupAllPreviews()
      selectedFiles.value = [merged]
      previewObjects.value = [{
        type: 'pdf',
        url: URL.createObjectURL(merged),
        content: null,
        // Preserve text already extracted from the source PDFs. Do not parse
        // the merged file again; merging does not change its text content.
        extractedText: mergedText,
      }]
      fileMetadata.value = []
      processingFiles.value = [false]
      form.files = [merged]
      form.file_extracted_texts = [mergedText]
    } catch (error) {
      form.setError?.('files', error?.message || 'The PDFs could not be merged. Please try again.')
    } finally {
      mergingFiles.value = false
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

  const getOfficeEmbedUrl = (fileUrl) => {
    if (!fileUrl) return ''
    return `https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(fileUrl)}`
  }

  return {
    selectedFiles,
    previewObjects,
    processingFiles,
    autoFillBusy,
    mergingFiles,
    autoFillEnabled: autoFillEnabledRef,
    autoFillSaving,
    autoFillError,
    handleFileSelect,
    removeFile,
    mergeSelectedFiles,
    getFileType,
    formatFileSize,
    toggleAutoFill,
    cleanupAllPreviews,
    getOfficeEmbedUrl
  }
}
