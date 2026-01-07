import { ref, computed, onMounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { DocsIcon, DraftIcon, PlusIcon, PieChartIcon, InfoCircleIcon } from '@/icons'
import { useDocumentFilter } from '@/composables/useDocumentSearch'

export function useDashboard(props) {
  // State
  const activeTab = ref('ordinances')
  const searchQuery = ref('')
  const selectedType = ref('')
  const selectedYear = ref('')
  const selectedAuthor = ref('')

  // Read search query from URL parameters on mount and when page changes
  onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search)
    const searchParam = urlParams.get('search')
    if (searchParam) {
      searchQuery.value = searchParam
    }
  })

  // Watch for Inertia page changes to restore search
  watch(() => router.page?.url, (newUrl) => {
    if (!newUrl) return
    const url = new URL(newUrl, window.location.origin)
    const searchParam = url.searchParams.get('search')
    if (searchParam) {
      searchQuery.value = searchParam
    }
  }, { immediate: true })

  // Utility functions
  const formatFileSize = (bytes) => {
    if (!bytes || bytes === 0) return '0 B'
    const k = 1024
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
  }

  // System Log helpers
  const getActionLabel = (action) => {
    const labels = {
      'create': 'Created',
      'update': 'Updated',
      'delete': 'Deleted',
      'view': 'Viewed',
      'rollback': 'Restored'
    }
    return labels[action] || action.charAt(0).toUpperCase() + action.slice(1)
  }

  const getModelTypeLabel = (modelType) => {
    if (!modelType) return 'Unknown'
    
    // Map table names to user-friendly labels
    const labels = {
      'ordinance_lists': 'Ordinance',
      'resolution_lists': 'Resolution',
      'ordinance_draft': 'Ordinance Draft',
      'resolution_draft': 'Resolution Draft',
      'users': 'User',
      'settings': 'Setting'
    }
    
    return labels[modelType] || modelType.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
  }

  const getLogIcon = (action) => {
    const icons = {
      'create': PlusIcon,
      'update': 'update-icon',
      'delete': 'delete-icon',
      'view': InfoCircleIcon,
      'rollback': 'rollback-icon'
    }
    return icons[action] || InfoCircleIcon
  }

  const getLogIconBg = (action) => {
    const backgrounds = {
      'create': 'bg-green-100 dark:bg-green-900/30',
      'update': 'bg-blue-100 dark:bg-blue-900/30',
      'delete': 'bg-red-100 dark:bg-red-900/30',
      'view': 'bg-gray-100 dark:bg-gray-900/30',
      'rollback': 'bg-purple-100 dark:bg-purple-900/30'
    }
    return backgrounds[action] || 'bg-gray-100 dark:bg-gray-900/30'
  }

  const getLogIconColor = (action) => {
    const colors = {
      'create': 'text-green-600 dark:text-green-400',
      'update': 'text-blue-600 dark:text-blue-400',
      'delete': 'text-red-600 dark:text-red-400',
      'view': 'text-gray-600 dark:text-gray-400',
      'rollback': 'text-purple-600 dark:text-purple-400'
    }
    return colors[action] || 'text-gray-600 dark:text-gray-400'
  }

  const getActionBadgeClass = (action) => {
    const classes = {
      'create': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
      'update': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
      'delete': 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
      'view': 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
      'rollback': 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400'
    }
    return classes[action] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400'
  }

  // Document type helpers
  const getTypeIcon = (type) => {
    const icons = {
      'ordinance': DocsIcon,
      'resolution': DocsIcon,
      'draft-ordinance': DraftIcon,
      'draft-resolution': DraftIcon,
      'accomplishment': PieChartIcon,
      'project': PlusIcon,
      'moa': DocsIcon,
      'municipal-communication': DocsIcon,
      'national-office': DocsIcon,
      'provincial-office': DocsIcon,
      'committee-report': DocsIcon
    }
    return icons[type] || DocsIcon
  }

  const getTypeIconClass = (type) => {
    const classes = {
      'ordinance': 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
      'resolution': 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
      'draft-ordinance': 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400',
      'draft-resolution': 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
      'accomplishment': 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400',
      'project': 'bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400',
      'moa': 'bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400',
      'municipal-communication': 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400',
      'national-office': 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400',
      'provincial-office': 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400',
      'committee-report': 'bg-violet-100 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400'
    }
    return classes[type] || 'bg-gray-100 dark:bg-gray-900/30 text-gray-600 dark:text-gray-400'
  }

  // Document Activity helpers
  const documentsSource = computed(() => {
    const source = {}

    Object.keys(props.allDocuments || {}).forEach(type => {
      const documents = props.allDocuments[type] || []

      source[type] = documents.map(doc => ({
        ...doc,
        type: doc.type || type
      }))
    })

    return source
  })

  const { filteredDocuments } = useDocumentFilter(documentsSource, searchQuery, selectedType, selectedYear, selectedAuthor)

  return {
    // State
    activeTab,
    searchQuery,
    selectedType,
    selectedYear,
    selectedAuthor,
    
    // Computed
    documentsSource,
    filteredDocuments,
    
    // Utility functions
    formatFileSize,
    
    // System log helpers
    getActionLabel,
    getModelTypeLabel,
    getLogIcon,
    getLogIconBg,
    getLogIconColor,
    getActionBadgeClass,
    
    // Document type helpers
    getTypeIcon,
    getTypeIconClass
  }
}
