<template>
  <AdminLayout title="Resolutions">
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
        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">{{ tabText.title }}</span>
      </div>
    </li>
  </ol>
</nav>

    <div class="space-y-6">
      <!-- Header Section -->
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ tabText.title }}</h1>
          <p class="text-gray-600 dark:text-gray-400">Manage and view {{ currentTab === 'overall' ? 'all' : currentTab }} resolutions in the system</p>
        </div>
        <div class="flex gap-3">
          <Link
            :href="tabText.createRoute"
            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors"
          >
            <PlusIcon class="w-4 h-4 mr-2" />
            {{ tabText.buttonText }}
          </Link>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8">
          <Link
            href="/resolutions"
            :class="[
              page.url === '/resolutions' || page.url.startsWith('/resolutions?')
                ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300',
              'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm'
            ]"
          >
            Overall
          </Link>
          <Link
            href="/resolutions/provinces"
            :class="[
              page.url === '/resolutions/provinces' || page.url.startsWith('/resolutions/provinces?')
                ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300',
              'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm'
            ]"
          >
            Provinces
          </Link>
          <Link
            href="/municipal-resolutions"
            :class="[
              page.url === '/municipal-resolutions' || page.url.startsWith('/municipal-resolutions?')
                ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300',
              'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm'
            ]"
          >
            Municipalities
          </Link>
        </nav>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] hover:shadow-lg transition-shadow duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ tabText.statsLabel }}</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.total }}</p>
            </div>
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                <DocsIcon class="w-6 h-6 text-green-600 dark:text-green-400" />
              </div>
            </div>
          </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] hover:shadow-lg transition-shadow duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Approved</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.approved }}</p>
            </div>
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                <CheckIcon class="w-6 h-6 text-green-600 dark:text-green-400" />
              </div>
            </div>
          </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] hover:shadow-lg transition-shadow duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Pending</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.pending }}</p>
            </div>
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
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
              <div class="w-12 h-12 bg-purple-50 dark:bg-purple-900/20 rounded-xl flex items-center justify-center">
                <CalenderIcon class="w-6 h-6 text-purple-600 dark:text-purple-400" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Resolutions Table -->
      <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
          <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">{{ tabText.tableTitle }}</h3>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Showing {{ filteredResolutions.length }} of {{ resolutions.length }} resolution{{ resolutions.length !== 1 ? 's' : '' }}
              </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
              <!-- Search Bar -->
              <div class="w-full sm:w-auto sm:min-w-[280px]">
                <SearchBar
                  v-model="searchInput"
                  placeholder="Search by keyword or content..."
                  :results-count="searchQuery ? filteredResolutions.length : null"
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

                  <!-- Subject Filter -->
                  <select
                    v-model="selectedSubject"
                    class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 flex-1 sm:w-32"
                  >
                    <option value="">All Subjects</option>
                    <option v-for="subject in filters.subjects" :key="subject" :value="subject">
                      {{ subject }}
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
                  v-if="searchQuery || selectedAuthor || selectedSubject || selectedYear"
                  @click="searchInput = ''; searchQuery = ''; selectedAuthor = ''; selectedSubject = ''; selectedYear = ''"
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
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Resolution No.</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Title</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Author</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date Approved</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Files</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="resolution in filteredResolutions" :key="resolution.resolution_id" class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                  <span v-html="highlightText(resolution.resolution_no, searchQuery)"></span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white max-w-xs truncate">
                  <span v-html="highlightText(resolution.title || 'Untitled Resolution', searchQuery)"></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  <div v-if="resolution.author" class="space-y-1">
                    <div v-for="author in resolution.author.split(';')" :key="author" class="text-xs">
                      <span v-html="highlightText(author.trim(), searchQuery)"></span>
                    </div>
                  </div>
                  <span v-else class="text-gray-400">No author</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  {{ formatDate(resolution.date_approved) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    :class="[
                      'inline-flex px-2 py-1 text-xs font-medium rounded-full',
                      resolution.date_approved ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' :
                      'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400'
                    ]"
                  >
                    {{ resolution.date_approved ? 'approved' : 'pending' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  <div v-if="resolution.files && resolution.files.length > 0" class="flex flex-wrap gap-1">
                    <a
                      v-for="file in resolution.files"
                      :key="file.resolution_file_id"
                      :href="'/serve-file/' + file.file_url"
                      target="_blank"
                      class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400 rounded-full hover:bg-green-200 dark:hover:bg-green-800/30 transition-colors"
                    >
                      <svg v-if="file.file_type.includes('pdf')" class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
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
                      :href="`/resolutions/${resolution.resolution_id}`"
                      class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                    >
                      View
                    </Link>
                    <Link
                      v-if="currentUser.role === 'admin' || resolution.added_by === currentUser.id"
                      :href="`/resolutions/${resolution.resolution_id}/edit`"
                      class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300"
                    >
                      Edit
                    </Link>
                    <button
                      v-if="currentUser.role === 'admin'"
                      @click="deleteResolution(resolution.resolution_id)"
                      class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                      title="Delete Resolution"
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
        <div v-if="filteredResolutions.length === 0" class="text-center py-12">
          <DocsIcon class="w-16 h-16 text-gray-300 mx-auto mb-4" />
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
            {{ resolutions.length === 0 ? 'No resolutions found' : 'No matching resolutions' }}
          </h3>
          <p class="text-gray-500 dark:text-gray-400 mb-4">
            {{ resolutions.length === 0 ? 'Get started by creating your first resolution.' : 'Try adjusting your search or filter criteria.' }}
          </p>
          <div v-if="resolutions.length === 0">
            <Link
              href="/resolutions/create"
              class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors"
            >
              <PlusIcon class="w-4 h-4 mr-2" />
              Create Resolution
            </Link>
          </div>
          <div v-else>
            <button
              @click="searchInput = ''; searchQuery = ''; selectedAuthor = ''; selectedSubject = ''; selectedYear = ''"
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
import FloatingFooter from '@/Components/FloatingFooter.vue'
import { Link, usePage } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import SearchBar from '@/Components/SearchBar.vue'
import { DocsIcon, PlusIcon, CheckIcon, CalenderIcon } from '@/icons'
import Swal from 'sweetalert2'

const page = usePage()
const currentUser = computed(() => page.props.auth.user)

const props = defineProps({
  resolutions: Array,
  stats: Object,
  filters: Object,
  searchQuery: String,
})

// Search functionality
const searchQuery = ref(props.searchQuery || '')
const searchInput = ref(props.searchQuery || '')
const isSearching = ref(false)

// Filter functionality
const selectedAuthor = ref('')
const selectedSubject = ref('')
const selectedYear = ref('')

// Current tab type based on URL
const currentTab = computed(() => {
  const url = page.url
  if (url.startsWith('/municipal-resolutions')) return 'municipal'
  if (url.startsWith('/resolutions/provinces')) return 'provincial'
  return 'overall'
})

// Dynamic text based on current tab
const tabText = computed(() => {
  switch (currentTab.value) {
    case 'provincial':
      return {
        title: 'Provincial Resolutions',
        statsLabel: 'Provincial Resolutions',
        tableTitle: 'Provincial Resolutions',
        buttonText: 'New Provincial Resolution',
        createRoute: '/resolutions/create'
      }
    case 'municipal':
      return {
        title: 'Municipal Resolutions',
        statsLabel: 'Municipal Resolutions',
        tableTitle: 'Municipal Resolutions',
        buttonText: 'New Municipal Resolution',
        createRoute: '/municipal-resolutions/create'
      }
    default:
      return {
        title: 'All Resolutions',
        statsLabel: 'All Resolutions',
        tableTitle: 'All Resolutions',
        buttonText: 'New Resolution',
        createRoute: '/resolutions/create'
      }
  }
})

// Debounce search input and trigger server-side search
let searchTimeout = null
watch(searchInput, (newValue) => {
  isSearching.value = true
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    searchQuery.value = newValue
    isSearching.value = false
    
    // Trigger server-side search
    router.get('/resolutions', 
      { 
        search: newValue || undefined,
        author: selectedAuthor.value || undefined,
        subject: selectedSubject.value || undefined,
        year: selectedYear.value || undefined
      },
      {
        preserveState: true,
        preserveScroll: true,
        only: ['resolutions', 'searchQuery']
      }
    )
  }, 500)
})

const filteredResolutions = computed(() => {
  let filtered = props.resolutions

  // Server-side search is already applied, but we still need client-side filters
  // Apply author filter
  if (selectedAuthor.value) {
    filtered = filtered.filter(resolution => {
      if (!resolution.author) return false
      const authors = resolution.author.split(';').map(a => a.trim())
      return authors.some(a => a.toLowerCase().includes(selectedAuthor.value.toLowerCase()))
    })
  }

  // Apply subject filter
  if (selectedSubject.value) {
    filtered = filtered.filter(resolution => resolution.subject === selectedSubject.value)
  }

  // Apply year filter
  if (selectedYear.value) {
    filtered = filtered.filter(resolution => {
      if (!resolution.date_approved) return false
      const year = new Date(resolution.date_approved).getFullYear()
      return year.toString() === selectedYear.value
    })
  }

  return filtered
})

const highlightText = (text, query) => {
  if (!query || !text) return text
  const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi')
  return text.replace(regex, '<mark class="bg-yellow-200 dark:bg-yellow-600 px-1 rounded">$1</mark>')
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

const truncateFileName = (fileName) => {
  if (!fileName) return ''
  
  // Remove file extension
  const nameWithoutExt = fileName.replace(/\.[^/.]+$/, '')
  
  // Split by common separators (hyphens, underscores, spaces)
  const words = nameWithoutExt.split(/[-_\s]+/)
  
  // Take first 3 words and join them back
  const truncated = words.slice(0, 3).join('-')
  
  // Add ellipsis if there were more words
  return words.length > 3 ? truncated + '...' : truncated
}

const deleteResolution = (resolutionId) => {
  Swal.fire({
    title: 'Are you sure?',
    text: 'You won\'t be able to revert this!',
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
      router.delete(`/resolutions/${resolutionId}`, {
        onSuccess: (page) => {
          // Success message will be shown via flash message in AdminLayout
        },
        onError: () => {
          Swal.fire({
            title: 'Error!',
            text: 'Failed to delete resolution. Please try again.',
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
