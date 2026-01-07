<template>
  <div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between" v-if="resolution">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Resolution Details</h1>
        <p class="text-gray-600 dark:text-gray-400">View resolution information and attached documents</p>
      </div>
      <div class="flex gap-3" v-if="showEditButton || showBackButton">
        <Link
          v-if="showEditButton"
          :href="editHref"
          class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
          </svg>
          Edit Resolution
        </Link>
        <Link
          v-if="showBackButton"
          :href="backHref"
          preserve-state
          class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          {{ backButtonLabel }}
        </Link>
      </div>
    </div>

    <!-- Resolution Details -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
      <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Resolution Information</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Resolution Number</label>
          <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ resolution.resolution_no || 'N/A' }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Author</label>
          <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ resolution.author || 'N/A' }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Session</label>
          <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ resolution.session || 'N/A' }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date Approved</label>
          <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ formatDate(resolution.date_approved) }}</p>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
          <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ resolution.title || 'N/A' }}</p>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subject</label>
          <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ resolution.subject || 'N/A' }}</p>
        </div>
      </div>
    </div>

    <!-- Attached Files -->
    <div v-if="normalizedFiles.length > 0" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
      <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Attached Documents</h2>
      <div class="space-y-4">
        <div v-for="file in normalizedFiles" :key="file.id" class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
              <div class="flex-shrink-0">
                <svg v-if="file.isPdf" class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                </svg>
                <svg v-else class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                </svg>
              </div>
            <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ file.file_name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ file.file_size ? formatFileSize(file.file_size) : 'Size not available' }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500">Uploaded {{ formatDate(file.uploadDate) }}</p>
              </div>
            </div>
            <a
              :href="file.download_url"
              target="_blank"
              rel="noopener"
              class="inline-flex items-center px-3 py-1 border border-gray-300 dark:border-gray-600 text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              Download
            </a>
          </div>

          <!-- Preview content -->
          <div v-if="file.isPdf" class="mt-4">
            <embed :src="file.preview_url" type="application/pdf" width="100%" height="600px" class="border border-gray-300 dark:border-gray-600 rounded" />
          </div>
          <div v-else-if="file.isDoc" class="mt-4">
            <div v-if="fileContents[file.id]" v-html="fileContents[file.id]" class="border border-gray-300 dark:border-gray-600 rounded p-4 max-h-96 overflow-y-auto bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></div>
            <div v-else class="border border-gray-300 dark:border-gray-600 rounded p-4 bg-gray-50 dark:bg-gray-800 text-center">
              <p>Loading document...</p>
            </div>
          </div>
          <div v-else class="mt-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">This file type cannot be previewed. Click "Download" to view the file.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- No Files Message -->
    <div v-else class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 text-center">
      <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
      </svg>
      <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Documents Attached</h3>
      <p class="text-gray-500 dark:text-gray-400">This resolution has no attached documents.</p>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import FloatingFooter from '@/Components/FloatingFooter.vue'
import { useDocumentViewer } from '@/composables/useDocumentViewer'

const props = defineProps({
  record: {
    type: Object,
    required: true
  },
  files: {
    type: Array,
    default: () => []
  },
  showEditButton: {
    type: Boolean,
    default: true
  },
  showBackButton: {
    type: Boolean,
    default: true
  },
  editUrl: {
    type: String,
    default: ''
  },
  backUrl: {
    type: String,
    default: '/resolutions'
  }
})

const {
  document: resolution,
  fileContents,
  formatDate,
  formatFileSize,
  normalizedFiles,
  backHref,
  backButtonLabel,
  getEditHref
} = useDocumentViewer(props, 'resolution')

const editHref = getEditHref('resolutions')
</script>
