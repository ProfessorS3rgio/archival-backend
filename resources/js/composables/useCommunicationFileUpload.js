import { ref } from 'vue'
import axios from 'axios'
import mammoth from 'mammoth'
import * as pdfjsLib from 'pdfjs-dist'

// Configure PDF.js worker
pdfjsLib.GlobalWorkerOptions.workerSrc = new URL('pdfjs-dist/build/pdf.worker.min.mjs', import.meta.url).toString()

export function useCommunicationFileUpload() {
  const selectedFiles = ref([])
  const previewObjects = ref([])
  const fileMetadata = ref([])
  const processingFiles = ref([])

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
        const extractedText = await extractTextFromPDF(file)
        previewObjects.value[index] = {
          type: 'pdf',
          url: objectUrl,
          content: null,
          extractedText: extractedText
        }
      } else if (file.type.includes('word') || file.type.includes('docx') || file.type.includes('doc')) {
        // Convert to PDF on backend and use for preview
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
        // Create blob from base64 PDF
        const pdfBlob = new Blob([Uint8Array.from(atob(response.data.pdf), c => c.charCodeAt(0))], { type: 'application/pdf' })
        const objectUrl = URL.createObjectURL(pdfBlob)
        // Create PDF file to replace the original
        const pdfName = file.name.replace(/\.(doc|docx?)$/i, '.pdf')
        const pdfFile = new File([pdfBlob], pdfName, { type: 'application/pdf' })
        selectedFiles.value[index] = pdfFile
        previewObjects.value[index] = {
          type: 'pdf',
          url: objectUrl,
          content: null,
          extractedText: response.data.text
        }
      }

      fileMetadata.value[index] = fileMetadata.value[index] || {}
      syncExtractedTextField(form)
    } catch (error) {
      console.error('Error generating preview for file:', file.name, error)
      previewObjects.value[index] = {
        type: 'error',
        url: null,
        content: 'Preview not available',
        extractedText: null
      }
    } finally {
      // Mark file as no longer processing
      processingFiles.value[index] = false
    }
  }

  const cleanupPreview = (index) => {
    if (previewObjects.value[index] && previewObjects.value[index].url) {
      URL.revokeObjectURL(previewObjects.value[index].url)
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

    // Generate previews for new files
    for (const [i, file] of validFiles.entries()) {
      const fileIndex = startingIndex + i
      await generateFilePreview(file, fileIndex, form)
    }
  }

  const removeFile = (index, form) => {
    // Clean up preview for this file
    cleanupPreview(index)

    selectedFiles.value.splice(index, 1)
    previewObjects.value.splice(index, 1)
    fileMetadata.value.splice(index, 1)
    processingFiles.value.splice(index, 1) // Remove processing state

    syncExtractedTextField(form)
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

  const syncExtractedTextField = (form) => {
    if (!form) return

    const texts = selectedFiles.value.map((_, idx) => {
      const preview = previewObjects.value[idx]
      if (preview && typeof preview.extractedText === 'string') {
        return preview.extractedText
      }
      return ''
    }).filter(text => text.trim())

    // Join all extracted texts with double newlines for comprehensive full-text search
    form.extracted_text = texts.join('\n\n')
  }

  const getOfficeEmbedUrl = (fileUrl) => {
    if (!fileUrl) return ''
    return `https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(fileUrl)}`
  }

  const cleanupAllPreviews = () => {
    previewObjects.value.forEach((preview, index) => {
      if (preview && preview.url) {
        URL.revokeObjectURL(preview.url)
      }
    })
    previewObjects.value = []
    selectedFiles.value = []
    fileMetadata.value = []
    processingFiles.value = []
  }

  return {
    selectedFiles,
    previewObjects,
    processingFiles,
    handleFileSelect,
    removeFile,
    getFileType,
    formatFileSize,
    getOfficeEmbedUrl,
    cleanupAllPreviews
  }
}