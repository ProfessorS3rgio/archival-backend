<template>
  <AdminLayout title="Projects">
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
        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Projects</span>
      </div>
    </li>
  </ol>
</nav>

    <div class="space-y-6">
      <!-- Header Section -->
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Projects</h1>
          <p class="text-gray-600 dark:text-gray-400">Manage and view all projects in the system</p>
        </div>
        <div class="flex gap-3">
          <Link
            href="/projects/create"
            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors"
          >
            <PlusIcon class="w-4 h-4 mr-2" />
            New Project
          </Link>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] hover:shadow-lg transition-shadow duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total Projects</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.total }}</p>
            </div>
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                <FolderIcon class="w-6 h-6 text-green-600 dark:text-green-400" />
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
              <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                <CalenderIcon class="w-6 h-6 text-blue-600 dark:text-blue-400" />
              </div>
            </div>
          </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] hover:shadow-lg transition-shadow duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">This Year</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.thisYear }}</p>
            </div>
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-purple-50 dark:bg-purple-900/20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] hover:shadow-lg transition-shadow duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total Amount</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(stats.totalAmount) }}</p>
            </div>
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"/>
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Projects Table -->
      <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
          <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">All Projects</h3>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Showing {{ filteredProjects.length }} of {{ projects.length }} project{{ projects.length !== 1 ? 's' : '' }}
              </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
              <!-- Search Bar -->
              <div class="w-full sm:w-auto sm:min-w-[280px]">
                <SearchBar
                  v-model="searchInput"
                  placeholder="Search by project name, district, or location..."
                  :results-count="searchQuery ? filteredProjects.length : null"
                  :is-loading="isSearching"
                />
              </div>

              <!-- Filters -->
              <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                <div class="flex gap-2 w-full sm:w-auto">
                  <!-- District Filter -->
                  <select
                    v-model="selectedDistrict"
                    class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 flex-1 sm:w-32"
                  >
                    <option value="">All Districts</option>
                    <option v-for="district in filters.districts" :key="district" :value="district">
                      {{ district }}
                    </option>
                  </select>

                  <!-- Source Fund Filter -->
                  <select
                    v-model="selectedSourceFund"
                    class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 flex-1 sm:w-36"
                  >
                    <option value="">All Sources</option>
                    <option v-for="fund in filters.sourceFunds" :key="fund" :value="fund">
                      {{ fund }}
                    </option>
                  </select>

                  <!-- Source Type Filter -->
                  <select
                    v-model="selectedSourceType"
                    class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 flex-1 sm:w-32"
                  >
                    <option value="">All Types</option>
                    <option v-for="type in filters.sourceTypes" :key="type" :value="type">
                      {{ type }}
                    </option>
                  </select>

                  <!-- Year Filter -->
                  <select
                    v-model="selectedYear"
                    class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 flex-1 sm:w-28"
                  >
                    <option value="">All Years</option>
                    <option v-for="year in filters.years" :key="year" :value="year">
                      {{ year }}
                    </option>
                  </select>
                </div>

                <!-- Clear Filters Button -->
                <button
                  v-if="searchQuery || selectedDistrict || selectedSourceFund || selectedSourceType || selectedYear"
                  @click="searchInput = ''; searchQuery = ''; selectedDistrict = ''; selectedSourceFund = ''; selectedSourceType = ''; selectedYear = ''"
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
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Project Name</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Code</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Year</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">District</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Location</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Source</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">File</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
              <tr
                v-for="project in filteredProjects"
                :key="project.project_id"
                class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors"
              >
                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white max-w-xs">
                  <div class="truncate" :title="project.project_name">
                    <span v-html="highlightMatch(project.project_name, searchQuery)"></span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  {{ project.code_no || 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  {{ project.year || 'N/A' }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                  <span v-html="highlightMatch(project.district || 'N/A', searchQuery)"></span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">
                  <span v-html="highlightMatch(project.location || 'N/A', searchQuery)"></span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                  <div class="text-xs">
                    <div v-if="project.source_fund">{{ project.source_fund }}</div>
                    <div v-if="project.source_type" class="text-gray-400">{{ project.source_type }}</div>
                    <div v-else class="text-gray-400">N/A</div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  {{ project.amount ? formatCurrency(project.amount) : 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    :class="[
                      'inline-flex px-2 py-1 text-xs font-medium rounded-full',
                      project.is_visible ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' :
                      'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
                    ]"
                  >
                    {{ project.is_visible ? 'visible' : 'hidden' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  <div v-if="project.file_name" class="flex items-center space-x-2">
                    <a
                      :href="'/serve-file/' + project.file_url"
                      target="_blank"
                      class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 rounded-full hover:bg-blue-200 dark:hover:bg-blue-800/30 transition-colors"
                      :title="project.file_name"
                    >
                      <svg v-if="project.file_type?.includes('pdf')" class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                      </svg>
                      <svg v-else-if="project.file_type?.includes('word') || project.file_type?.includes('docx') || project.file_type?.includes('doc')" class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                      </svg>
                      <svg v-else class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm0 2h12v10H4V5z" clip-rule="evenodd"></path>
                      </svg>
                      {{ truncateFileName(project.file_name) }}
                    </a>
                  </div>
                  <span v-else class="text-gray-400 dark:text-gray-600">No file</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex items-center space-x-2">
                    <Link
                      :href="`/projects/${project.project_id}`"
                      class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                    >
                      View
                    </Link>
                    <Link
                      v-if="currentUser.role === 'admin' || project.added_by === currentUser.id"
                      :href="`/projects/${project.project_id}/edit`"
                      class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300"
                    >
                      Edit
                    </Link>
                    <button
                      v-if="currentUser.role === 'admin'"
                      @click="deleteProject(project.project_id)"
                      class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                      title="Delete Project"
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
        <div v-if="filteredProjects.length === 0" class="text-center py-12">
          <FolderIcon class="w-16 h-16 text-gray-300 mx-auto mb-4" />
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
            {{ (searchQuery || selectedDistrict || selectedSourceFund || selectedSourceType || selectedYear) ? 'No projects match your filters' : 'No projects found' }}
          </h3>
          <p class="text-gray-500 dark:text-gray-400 mb-4">
            {{ (searchQuery || selectedDistrict || selectedSourceFund || selectedSourceType || selectedYear) ? 'Try adjusting your search terms or filters.' : 'Get started by creating your first project.' }}
          </p>
          <div v-if="!searchQuery" class="flex justify-center">
            <Link
              href="/projects/create"
              class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors"
            >
              <PlusIcon class="w-4 h-4 mr-2" />
              Create Project
            </Link>
          </div>
          <div v-else class="flex justify-center">
            <button
              @click="searchInput = ''; searchQuery = ''; selectedDistrict = ''; selectedSourceFund = ''; selectedSourceType = ''; selectedYear = ''"
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
import { Link, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import SearchBar from '@/Components/SearchBar.vue'
import { FolderIcon, PlusIcon, CalenderIcon } from '@/icons'
import Swal from 'sweetalert2'

const page = usePage()
const currentUser = computed(() => page.props.auth.user)

const props = defineProps({
  projects: Array,
  stats: Object,
  filters: Object,
  searchQuery: String,
})

// Search functionality
const searchQuery = ref(props.searchQuery || '')
const searchInput = ref(props.searchQuery || '')
const isSearching = ref(false)

// Filter functionality
const selectedDistrict = ref('')
const selectedSourceFund = ref('')
const selectedSourceType = ref('')
const selectedYear = ref('')

// Debounce search input and trigger server-side search
let searchTimeout = null
watch(searchInput, (newValue) => {
  isSearching.value = true
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    searchQuery.value = newValue
    isSearching.value = false

    // Trigger server-side search
    router.get('/projects',
      {
        search: newValue || undefined,
        district: selectedDistrict.value || undefined,
        source_fund: selectedSourceFund.value || undefined,
        source_type: selectedSourceType.value || undefined,
        year: selectedYear.value || undefined
      },
      {
        preserveState: true,
        preserveScroll: true,
        only: ['projects', 'searchQuery']
      }
    )
  }, 500)
})

const filteredProjects = computed(() => {
  let filtered = props.projects

  // Server-side search is already applied, but we still need client-side filters
  // Apply district filter
  if (selectedDistrict.value) {
    filtered = filtered.filter(project => project.district === selectedDistrict.value)
  }

  // Apply source fund filter
  if (selectedSourceFund.value) {
    filtered = filtered.filter(project => project.source_fund === selectedSourceFund.value)
  }

  // Apply source type filter
  if (selectedSourceType.value) {
    filtered = filtered.filter(project => project.source_type === selectedSourceType.value)
  }

  // Apply year filter
  if (selectedYear.value) {
    filtered = filtered.filter(project => project.year == selectedYear.value)
  }

  return filtered
})

const formatCurrency = (amount) => {
  if (!amount) return 'â‚±0.00'
  return new Intl.NumberFormat('en-PH', {
    style: 'currency',
    currency: 'PHP',
    minimumFractionDigits: 2
  }).format(amount)
}

const highlightMatch = (text, query) => {
  if (!query || !text) return text

  const regex = new RegExp(`(${query})`, 'gi')
  return text.replace(regex, '<mark class="bg-yellow-200 dark:bg-yellow-500/30 text-gray-900 dark:text-white px-0.5 rounded">$1</mark>')
}

const deleteProject = (projectId) => {
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
      router.delete(`/projects/${projectId}`, {
        onSuccess: (page) => {
          // Success message will be shown via flash message in AdminLayout
        },
        onError: () => {
          Swal.fire({
            title: 'Error!',
            text: 'Failed to delete project. Please try again.',
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

const truncateFileName = (fileName) => {
  if (!fileName) return ''

  // Remove file extension
  const nameWithoutExt = fileName.replace(/\.[^/.]+$/, '')

  // If name is short, return as is
  if (nameWithoutExt.length <= 15) return nameWithoutExt

  // Truncate and add ellipsis
  return nameWithoutExt.substring(0, 12) + '...'
}
</script>
