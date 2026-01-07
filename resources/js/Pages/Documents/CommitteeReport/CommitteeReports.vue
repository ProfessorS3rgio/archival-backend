<template>
  <AdminLayout title="Committee Reports">
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
            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Committee Reports</span>
          </div>
        </li>
      </ol>
    </nav>

    <div class="space-y-6">
      <!-- Header Section -->
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Committee Reports</h1>
          <p class="text-gray-600 dark:text-gray-400">Manage and view all committee reports in the system</p>
        </div>
        <div class="flex gap-3">
          <Link
            href="/committee-reports/create"
            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            New Committee Report
          </Link>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] hover:shadow-lg transition-shadow duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total Reports</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.total }}</p>
            </div>
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
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
              <div class="w-12 h-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
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
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Committee Reports Table -->
      <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
          <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">All Committee Reports</h3>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Showing {{ filteredReports.length }} of {{ reports.length }} report{{ reports.length !== 1 ? 's' : '' }}
              </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
              <!-- Search Bar -->
              <div class="w-full sm:w-auto sm:min-w-[280px]">
                <SearchBar
                  v-model="searchInput"
                  placeholder="Search by title, number, subject..."
                  :results-count="searchQuery ? filteredReports.length : null"
                  :is-loading="isSearching"
                />
              </div>

              <!-- Filters -->
              <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                <div class="flex gap-2 w-full sm:w-auto">
                  <!-- Subject Filter -->
                  <select
                    v-model="selectedSubject"
                    class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 flex-1 sm:w-40"
                  >
                    <option value="">All Subjects</option>
                    <option v-for="subject in filters?.subjects" :key="subject" :value="subject">
                      {{ subject }}
                    </option>
                  </select>

                  <!-- Year Filter -->
                  <select
                    v-model="selectedYear"
                    class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 flex-1 sm:w-24"
                  >
                    <option value="">All Years</option>
                    <option v-for="year in filters?.years" :key="year" :value="year.toString()">
                      {{ year }}
                    </option>
                  </select>
                </div>

                <!-- Clear Filters Button -->
                <button
                  v-if="searchQuery || selectedSubject || selectedYear"
                  @click="searchInput = ''; searchQuery = ''; selectedSubject = ''; selectedYear = ''"
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
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Report No.</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Title</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Subject</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Year</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Files</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="report in filteredReports" :key="report.report_id" class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                  <span v-html="highlightText(report.committee_report_no || 'N/A', searchQuery)"></span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white max-w-xs truncate">
                  <span v-html="highlightText(report.title || 'Untitled Report', searchQuery)"></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  <span v-html="highlightText(report.subject || 'N/A', searchQuery)"></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  {{ report.year || 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  {{ formatDate(report.created_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  <div v-if="report.file_name" class="flex flex-wrap gap-1">
                    <a
                      :href="'/serve-file/' + report.file_url"
                      target="_blank"
                      class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 rounded-full hover:bg-blue-200 dark:hover:bg-blue-800/30 transition-colors"
                    >
                      <svg v-if="report.file_type && report.file_type.includes('pdf')" class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                      </svg>
                      <svg v-else class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                      </svg>
                      {{ report.file_name }}
                    </a>
                  </div>
                  <span v-else class="text-gray-400">No file</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex items-center space-x-2">
                    <Link
                      :href="`/committee-reports/${report.report_id}`"
                      class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                    >
                      View
                    </Link>
                    <Link
                      v-if="currentUser.role === 'admin' || report.added_by === currentUser.id"
                      :href="`/committee-reports/${report.report_id}/edit`"
                      class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300"
                    >
                      Edit
                    </Link>
                    <button
                      v-if="currentUser.role === 'admin'"
                      @click="deleteReport(report.report_id)"
                      class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                      title="Delete Committee Report"
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
        <div v-if="filteredReports.length === 0" class="text-center py-12">
          <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
            {{ searchQuery || selectedSubject || selectedYear ? 'No matching committee reports' : 'No committee reports found' }}
          </h3>
          <p class="text-gray-500 dark:text-gray-400 mb-4">
            {{ searchQuery || selectedSubject || selectedYear ? 'Try adjusting your search or filters.' : 'Get started by creating your first committee report.' }}
          </p>
          <div v-if="searchQuery || selectedSubject || selectedYear">
            <button
              @click="searchInput = ''; searchQuery = ''; selectedSubject = ''; selectedYear = ''"
              class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors"
            >
              Clear Filters
            </button>
          </div>
          <div v-else>
            <Link
              href="/committee-reports/create"
              class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              Create Committee Report
            </Link>
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
import { Link } from '@inertiajs/vue3'
import { router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import SearchBar from '@/Components/SearchBar.vue'
import FloatingFooter from '@/Components/FloatingFooter.vue'
import Swal from 'sweetalert2'

const page = usePage()
const currentUser = computed(() => page.props.auth.user)

const props = defineProps({
  reports: Array,
  stats: Object,
  filters: Object,
})

// Search functionality
const searchQuery = ref('')
const searchInput = ref('')
const isSearching = ref(false)

// Filter functionality
const selectedSubject = ref('')
const selectedYear = ref('')

// Debounce search input
let searchTimeout = null
watch(searchInput, (newValue) => {
  isSearching.value = true
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    searchQuery.value = newValue
    isSearching.value = false
  }, 300)
})

const filteredReports = computed(() => {
  let filtered = props.reports

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(report => {
      return (
        report.title?.toLowerCase().includes(query) ||
        report.committee_report_no?.toString().toLowerCase().includes(query) ||
        report.subject?.toLowerCase().includes(query) ||
        report.extracted_text?.toLowerCase().includes(query)
      )
    })
  }

  // Apply subject filter
  if (selectedSubject.value) {
    filtered = filtered.filter(report => report.subject === selectedSubject.value)
  }

  // Apply year filter
  if (selectedYear.value) {
    filtered = filtered.filter(report => report.year?.toString() === selectedYear.value)
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

const deleteReport = (reportId) => {
  Swal.fire({
    title: 'Are you sure?',
    text: 'You won\'t be able to revert this! This will permanently delete the committee report.',
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
      router.delete(`/committee-reports/${reportId}`, {
        onSuccess: (page) => {
          // Success message will be shown via flash message in AdminLayout
        },
        onError: () => {
          Swal.fire({
            title: 'Error!',
            text: 'Failed to delete committee report. Please try again.',
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