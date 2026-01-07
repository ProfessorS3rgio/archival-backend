import { computed, ref, watch, onMounted } from 'vue'
import mammoth from 'mammoth'

/**
 * Composable for document viewer functionality
 * Provides shared logic for displaying and previewing documents
 */
export function useDocumentViewer(props, documentType = 'resolution') {
  const fileContents = ref({})
  const baseUrl = computed(() => (typeof window !== 'undefined' ? window.location.origin : ''))
  
  // Get the document record based on type
  const document = computed(() => props.record || {})

  /**
   * Format date for display
   */
  const formatDate = (date) => {
    if (!date) return 'Not Approved'
    const parsed = new Date(date)
    if (Number.isNaN(parsed.getTime())) return date
    return parsed.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  }

  /**
   * Format file size to human readable format
   */
  const formatFileSize = (bytes) => {
    if (!bytes && bytes !== 0) return 'Unknown size'
    if (bytes === 0) return '0 Bytes'
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return `${parseFloat((bytes / Math.pow(k, i)).toFixed(2))} ${sizes[i]}`
  }

  /**
   * Normalize files from different sources into consistent format
   */
  const normalizedFiles = computed(() => {
    const sourceFiles = props.files.length ? props.files : document.value.files || []
    const fileIdKey = `${documentType}_file_id`
    
    return sourceFiles.map((file) => {
      const fileId = file.file_id || file.id || file[fileIdKey]
      const previewUrl = file.preview_url || `${baseUrl.value}/serve-file/${file.file_url}`
      const downloadUrl = file.download_url || `${baseUrl.value}/storage/${file.file_url}`
      const fileName = file.file_name || file.name
      const fileType = file.file_type || ''
      const sizeBytes = file.file_size ?? file.size
      const uploadDate = file.created_at || file.uploaded_at

      return {
        ...file,
        id: fileId,
        file_name: fileName,
        file_type: fileType,
        file_size: sizeBytes,
        preview_url: previewUrl,
        download_url: downloadUrl,
        created_at: uploadDate,
        displaySize: formatFileSize(sizeBytes),
        uploadDate: uploadDate ? formatDate(uploadDate) : 'Unknown date',
        isPdf: fileType.includes('pdf') || fileName?.toLowerCase().endsWith('.pdf'),
        isDoc: /(word|docx|doc)/i.test(fileType) || /\.(docx|doc)$/i.test(fileName || '')
      }
    })
  })

  /**
   * Load DOCX content for preview
   */
  const loadDocxContent = async (file) => {
    if (!file.isDoc || fileContents.value[file.id]) return

    try {
      const response = await fetch(file.preview_url)
      if (!response.ok) throw new Error('Failed to fetch document preview')
      const arrayBuffer = await response.arrayBuffer()
      const result = await mammoth.convertToHtml({ arrayBuffer })
      fileContents.value[file.id] = result.value
    } catch (error) {
      console.error('Error loading DOCX:', error)
      fileContents.value[file.id] = '<p>Error loading document</p>'
    }
  }

  /**
   * Preload all DOCX files on component mount
   */
  const preloadDocxFiles = () => {
    normalizedFiles.value.forEach((file) => {
      if (file.isDoc) {
        loadDocxContent(file)
      }
    })
  }

  /**
   * Get edit URL based on document type
   */
  const getEditHref = (defaultRoute) => {
    return computed(() => {
      if (props.editUrl) return props.editUrl
      const idKey = `${documentType}_id`
      const id = document.value?.[idKey]
      return id ? `/${defaultRoute}/${id}/edit` : '#'
    })
  }

  /**
   * Get back URL
   */
  const backHref = computed(() => props.backUrl || `/${documentType}s`)

  /**
   * Get back button label based on context
   */
  const backButtonLabel = computed(() => {
    if (props.backUrl === '/' || props.backUrl === '/dashboard' || props.backUrl?.startsWith('/dashboard?')) {
      return 'Back to Home'
    }
    
    // Capitalize first letter of document type
    const typeName = documentType.charAt(0).toUpperCase() + documentType.slice(1)
    return `Back to ${typeName}s`
  })

  // Watch for changes in normalized files and load DOCX content
  watch(
    () => normalizedFiles.value,
    (newFiles, oldFiles) => {
      if (newFiles === oldFiles) return
      newFiles.forEach((file) => {
        if (file.isDoc && !fileContents.value[file.id]) {
          loadDocxContent(file)
        }
      })
    },
    { deep: true }
  )

  // Preload DOCX files on mount
  onMounted(preloadDocxFiles)

  return {
    document,
    baseUrl,
    fileContents,
    formatDate,
    formatFileSize,
    normalizedFiles,
    loadDocxContent,
    preloadDocxFiles,
    getEditHref,
    backHref,
    backButtonLabel
  }
}
