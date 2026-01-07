<template>
  <AdminLayout title="View Committee Report">
    <div class="max-w-4xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Committee Report Details</h1>
          <p class="text-gray-600 dark:text-gray-400">View committee report information</p>
        </div>
        <div class="flex gap-3">
          <Link v-if="currentUser.role === 'admin' || report.added_by === currentUser.id" :href="`/committee-reports/${report.report_id}/edit`" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit Report
          </Link>
          <Link href="/committee-reports" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Committee Reports
          </Link>
        </div>
      </div>

      <!-- Committee Report Details -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Report Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Report Number</label>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ report.committee_report_no || 'N/A' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Year</label>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ report.year || 'N/A' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subject</label>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ report.subject || 'N/A' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Created Date</label>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ formatDate(report.created_at) }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Added By</label>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ report.addedBy?.name || 'N/A' }}</p>
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ report.title || 'N/A' }}</p>
          </div>
        </div>
      </div>

      <!-- Attached Files -->
      <div v-if="report.file_url" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Attached Document</h2>
        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
              <div class="flex-shrink-0">
                <svg v-if="report.file_type.includes('pdf')" class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                </svg>
                <svg v-else class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ report.file_name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatFileSize(report.file_size) }} • {{ report.file_type }}</p>
              </div>
            </div>
            <a
              :href="'/download/committee-report/' + report.report_id + '/' + encodeURIComponent(report.file_url)"
              target="_blank"
              download
              class="inline-flex items-center px-3 py-1 border border-gray-300 dark:border-gray-600 text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              Download
            </a>
          </div>
          <!-- Preview content -->
          <div v-if="report.file_type.includes('pdf')" class="mt-4">
            <embed :src="`${baseUrl}/serve-file/${report.file_url}`" type="application/pdf" width="100%" height="600px" class="border border-gray-300 dark:border-gray-600 rounded" />
          </div>
          <div v-else-if="report.file_type.includes('word') || report.file_type.includes('docx') || report.file_type.includes('doc')" class="mt-4">
            <div v-if="fileContent" v-html="fileContent" class="border border-gray-300 dark:border-gray-600 rounded p-4 max-h-96 overflow-y-auto bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></div>
            <div v-else class="border border-gray-300 dark:border-gray-600 rounded p-4 bg-gray-50 dark:bg-gray-800 text-center">
              <p>Loading document...</p>
            </div>
          </div>
          <div v-else class="mt-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">This file type cannot be previewed. Click "Download" to view the file.</p>
          </div>
        </div>
      </div>

      <!-- No Files Message -->
      <div v-else class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Documents Attached</h3>
        <p class="text-gray-500 dark:text-gray-400">This committee report has no attached documents.</p>
      </div>
    </div>
  </AdminLayout>

  <!-- Floating Developer Footer -->
  <FloatingFooter />
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import FloatingFooter from '@/Components/FloatingFooter.vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { computed, ref, onMounted } from 'vue'
import mammoth from 'mammoth'

const props = defineProps({
  report: {
    type: Object,
    required: true
  },
  auth: {
    type: Object,
    default: () => ({})
  }
})

const currentUser = computed(() => props.auth.user || {})

// Load DOCX content when component mounts
onMounted(() => {
  if (props.report.file_url && (props.report.file_type.includes('word') || props.report.file_type.includes('docx') || props.report.file_type.includes('doc'))) {
    loadDocxContent()
  }
})

const baseUrl = computed(() => typeof window !== 'undefined' ? window.location.origin : '')
const fileContent = ref('')

const loadDocxContent = async () => {
  if (props.report.file_type.includes('word') || props.report.file_type.includes('docx') || props.report.file_type.includes('doc')) {
    try {
      const response = await fetch(`${baseUrl.value}/serve-file/${props.report.file_url}`)
      const arrayBuffer = await response.arrayBuffer()
      const result = await mammoth.convertToHtml({ arrayBuffer })
      fileContent.value = result.value
    } catch (error) {
      console.error('Error loading DOCX:', error)
      fileContent.value = '<p>Error loading document</p>'
    }
  }
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}
</script>