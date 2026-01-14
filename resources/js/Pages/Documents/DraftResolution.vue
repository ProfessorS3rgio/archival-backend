<template>
  <AdminLayout title="Draft Resolutions">
    <nav class="flex" aria-label="Breadcrumb">
  <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
    <li class="inline-flex items-center">
      <Link href="/dashboard" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
          <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
        </svg>
        Home
      </Link>
    </li>
    <li aria-current="page">
      <div class="flex items-center">
        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
        </svg>
        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Draft Resolutions</span>
      </div>
    </li>
  </ol>
</nav>

    <div class="space-y-6">
      <!-- Header Section -->
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Draft Resolutions</h1>
          <p class="text-gray-600 dark:text-gray-400">Manage and view all draft resolutions in the system</p>
        </div>
        <div class="flex gap-3" v-if="!isLegislator">
          <Link
            href="/draft-resolutions/create"
            class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors"
          >
            <DraftIcon class="w-4 h-4 mr-2" />
            New Draft Resolution
          </Link>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] hover:shadow-lg transition-shadow duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total Draft Resolutions</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.total }}</p>
            </div>
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-purple-50 dark:bg-purple-900/20 rounded-xl flex items-center justify-center">
                <DraftIcon class="w-6 h-6 text-purple-600 dark:text-purple-400" />
              </div>
            </div>
          </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] hover:shadow-lg transition-shadow duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">In Review</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.inReview }}</p>
            </div>
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] hover:shadow-lg transition-shadow duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Pending Approval</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.pendingApproval }}</p>
            </div>
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-orange-50 dark:bg-orange-900/20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] hover:shadow-lg transition-shadow duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">This Month</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.thisMonth }}</p>
            </div>
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                <CalenderIcon class="w-6 h-6 text-green-600 dark:text-green-400" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Draft Resolutions Table -->
      <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
          <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">All Draft Resolutions</h3>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Showing {{ filteredDrafts.length }} of {{ drafts.length }} draft{{ drafts.length !== 1 ? 's' : '' }}
              </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
              <!-- Search Bar -->
              <div class="w-full sm:w-auto sm:min-w-[280px]">
                <SearchBar
                  v-model="searchInput"
                  placeholder="Search title, author, files, text..."
                  :results-count="searchQuery ? filteredDrafts.length : null"
                  :is-loading="isSearching"
                />
              </div>

              <!-- Filters -->
              <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                <div class="flex gap-2 w-full sm:w-auto">
                  <!-- Author Filter -->
                  <select
                    v-model="selectedAuthor"
                    class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 flex-1 sm:w-32"
                  >
                    <option value="">All Authors</option>
                    <option v-for="author in filters.authors" :key="author" :value="author">
                      {{ author }}
                    </option>
                  </select>

                  <!-- Year Filter -->
                  <select
                    v-model="selectedYear"
                    class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 flex-1 sm:w-24"
                  >
                    <option value="">All Years</option>
                    <option v-for="year in filters.years" :key="year" :value="year.toString()">
                      {{ year }}
                    </option>
                  </select>
                </div>

                <!-- Clear Filters Button -->
                <button
                  v-if="searchQuery || selectedAuthor || selectedYear"
                  @click="searchInput = ''; searchQuery = ''; selectedAuthor = ''; selectedYear = ''"
                  class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300 rounded-lg transition-colors whitespace-nowrap"
                  title="Clear all filters"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Draft No.</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Title</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Author</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Files</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="draft in filteredDrafts" :key="draft.resolution_draft_id" class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                  <span v-html="highlightText(draft.resolution_draft_id, searchQuery)"></span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white max-w-xs truncate">
                  <span v-html="highlightText(draft.title || 'Untitled Draft Resolution', searchQuery)"></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  <span v-html="highlightText(draft.author, searchQuery)"></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  {{ formatDate(draft.created_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    :class="[
                      'inline-flex px-2 py-1 text-xs font-medium rounded-full',
                      draft.status === 'in_review' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' :
                      draft.status === 'pending_approval' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400' :
                      'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400'
                    ]"
                  >
                    {{ formatStatus(draft.status) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  <div v-if="draft.files && draft.files.length > 0" class="flex flex-wrap gap-1">
                    <a
                      v-for="file in draft.files"
                      :key="file.draft_resolution_file_id"
                      :href="`/serve-file/${encodeURIComponent(file.file_url)}`"
                      target="_blank"
                      class="inline-flex items-center px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400 rounded-full hover:bg-purple-200 dark:hover:bg-purple-800/30 transition-colors"
                      :title="file.file_name"
                    >
                      <svg v-if="file.file_type?.includes('pdf')" class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                      </svg>
                      <svg v-else class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                      </svg>
                      {{ truncateFileName(file.file_name) }}
                    </a>
                  </div>
                  <span v-else class="text-gray-400">No files</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex items-center space-x-2">
                    <Link
                      :href="`/draft-resolutions/${draft.resolution_draft_id}`"
                      class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                    >
                      View
                    </Link>
                    <Link
                      v-if="currentUser.role === 'admin' || draft.added_by === currentUser.id"
                      :href="`/draft-resolutions/${draft.resolution_draft_id}/edit`"
                      class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300"
                    >
                      Edit
                    </Link>
                    <button
                      v-if="currentUser.role === 'admin'"
                      @click="deleteDraft(draft.resolution_draft_id)"
                      class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                      title="Delete Draft Resolution"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-if="filteredDrafts.length === 0" class="text-center py-12">
          <DraftIcon class="w-16 h-16 text-gray-300 mx-auto mb-4" />
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
            {{ drafts.length === 0 ? 'No draft resolutions found' : 'No matching draft resolutions' }}
          </h3>
          <p class="text-gray-500 dark:text-gray-400 mb-4">
            {{ drafts.length === 0 ? 'Get started by creating your first draft resolution.' : 'Try adjusting your search or filter criteria.' }}
          </p>
          <div v-if="drafts.length === 0 && !isLegislator">
            <Link
              href="/draft-resolutions/create"
              class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors"
            >
              <DraftIcon class="w-4 h-4 mr-2" />
              Create Draft Resolution
            </Link>
          </div>
          <div v-else>
            <button
              @click="searchInput = ''; searchQuery = ''; selectedAuthor = ''; selectedYear = ''"
              class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors"
            >
              Clear Filters
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>

  <!-- Floating Developer Footer -->
  <FloatingFooter />
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import SearchBar from '@/Components/SearchBar.vue'
import FloatingFooter from '@/Components/FloatingFooter.vue'
import { DraftIcon, CalenderIcon } from '@/icons'
import Swal from 'sweetalert2'

const page = usePage()
const currentUser = computed(() => page.props.auth.user)
const isLegislator = computed(() => (currentUser.value?.role ?? '').toString().trim().toLowerCase() === 'legislator')

const props = defineProps({
  drafts: Array,
  stats: Object,
  filters: Object,
})

// Search functionality
const searchQuery = ref('')
const searchInput = ref('')
const isSearching = ref(false)

// Filter functionality
const selectedAuthor = ref('')
const selectedYear = ref('')

// Debounce search input
let searchTimeout = null
watch(searchInput, (newValue) => {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
  
  if (newValue === '') {
    // Immediate update for clearing search
    searchQuery.value = ''
    isSearching.value = false
  } else {
    isSearching.value = true
    searchTimeout = setTimeout(() => {
      searchQuery.value = newValue
      isSearching.value = false
      searchTimeout = null
    }, 300)
  }
})

const filteredDrafts = computed(() => {
  let filtered = [...props.drafts]

  // Apply search filter
  if (searchQuery.value && searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase().trim()
    filtered = filtered.filter(draft => {
      try {
        // Search in basic fields
        const basicMatch = (
          draft.title?.toLowerCase().includes(query) ||
          draft.resolution_draft_id?.toString().toLowerCase().includes(query) ||
          draft.author?.toLowerCase().includes(query) ||
          draft.subject?.toLowerCase().includes(query)
        )
        
        // Search in file names and extracted text
        const fileMatch = draft.files?.some(file => 
          file.file_name?.toLowerCase().includes(query) ||
          file.extracted_text?.toLowerCase().includes(query)
        ) || false
        
        return basicMatch || fileMatch
      } catch (error) {
        console.error('Search filter error:', error)
        return false
      }
    })
  }

  // Apply author filter
  if (selectedAuthor.value) {
    filtered = filtered.filter(draft => draft.author === selectedAuthor.value)
  }

  // Apply year filter
  if (selectedYear.value) {
    filtered = filtered.filter(draft => {
      if (!draft.created_at) return false
      const year = new Date(draft.created_at).getFullYear()
      return year.toString() === selectedYear.value
    })
  }

  return filtered
})
const highlightText = (text, query) => {
  if (!query || !text) return text
  // Ensure text is a string
  const textStr = String(text)
  const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi')
  return textStr.replace(regex, '<mark class="bg-yellow-200 dark:bg-yellow-600 px-1 rounded">$1</mark>')
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

const formatStatus = (status) => {
  if (!status) return 'Draft'
  return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
}

const truncateFileName = (fileName) => {
  if (!fileName) return ''
  // Remove file extension
  const nameWithoutExt = fileName.replace(/\.[^/.]+$/, '')
  // If name is short, return as is
  if (nameWithoutExt.length <= 15) return nameWithoutExt
  // Truncate and add ellipsis
  return nameWithoutExt.substring(0, 12) + '...'
}

const deleteDraft = (draftId) => {
  Swal.fire({
    title: 'Are you sure?',
    text: 'You won\'t be able to revert this! This will permanently delete the draft resolution.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Yes, delete it!',
    background: 'var(--swal-background, #ffffff)',
    color: 'var(--swal-text-color, #374151)',
    customClass: {
      popup: 'dark:bg-gray-800 dark:text-white',
      confirmButton: 'dark:bg-red-600 dark:hover:bg-red-700',
      cancelButton: 'dark:bg-gray-600 dark:hover:bg-gray-700'
    }
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(`/draft-resolutions/${draftId}`, {
        onSuccess: (page) => {
          // Success message will be shown via flash message in AdminLayout
        },
        onError: () => {
          Swal.fire({
            title: 'Error!',
            text: 'Failed to delete draft resolution. Please try again.',
            icon: 'error',
            background: 'var(--swal-background, #ffffff)',
            color: 'var(--swal-text-color, #374151)',
            customClass: {
              popup: 'dark:bg-gray-800 dark:text-white'
            }
          })
        }
      })
    }
  })
}
</script>