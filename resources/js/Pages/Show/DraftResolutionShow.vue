<template>
  <AdminLayout :title="`Draft Resolution #${draft.resolution_draft_id || 'Untitled'}`">
    <div class="space-y-6">
      <!-- Header Section -->
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Draft Resolution Details</h1>
          <p class="text-gray-600 dark:text-gray-400">View and manage draft resolution information</p>
        </div>
        <div class="flex gap-3">
          <Link
            v-if="currentUser.role === 'admin' || draft.added_by === currentUser.id"
            :href="`/draft-resolutions/${draft.resolution_draft_id}/edit`"
            class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit Draft
          </Link>
          <Link
            href="/draft-resolutions"
            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Drafts
          </Link>
        </div>
      </div>

      <!-- Draft Information Card -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Draft Information</h2>
          <span
            :class="[
              'inline-flex px-3 py-1 text-sm font-medium rounded-full',
              draft.status === 'in_review' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' :
              draft.status === 'pending_approval' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400' :
              'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400'
            ]"
          >
            {{ formatStatus(draft.status) }}
          </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Draft Resolution Number</label>
            <p class="mt-1 text-lg font-medium text-gray-900 dark:text-white">N/A</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Author</label>
            <p class="mt-1 text-lg font-medium text-gray-900 dark:text-white">{{ draft.author || 'Not specified' }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Session</label>
            <p class="mt-1 text-lg font-medium text-gray-900 dark:text-white">{{ draft.session || 'Not specified' }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Created Date</label>
            <p class="mt-1 text-lg font-medium text-gray-900 dark:text-white">{{ formatDate(draft.created_at) }}</p>
          </div>

          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Title</label>
            <p class="mt-1 text-lg font-medium text-gray-900 dark:text-white">{{ draft.title || 'Untitled Draft Resolution' }}</p>
          </div>

          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Subject</label>
            <p class="mt-1 text-gray-700 dark:text-gray-300">{{ draft.subject || 'No subject provided' }}</p>
          </div>
        </div>
      </div>

      <!-- Files Section -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Attached Files</h2>

        <div v-if="draft.files && draft.files.length > 0" class="space-y-4">
          <div v-for="file in draft.files" :key="file.file_id" class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                  <svg v-if="getFileTypeFromName(file.file_name) === 'pdf'" class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                  </svg>
                  <svg v-else class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900 dark:text-white">{{ file.file_name }}</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">{{ file.file_size ? formatFileSize(file.file_size) : 'Size not available' }}</p>
                  <p class="text-xs text-gray-400 dark:text-gray-500">Uploaded {{ formatDate(file.created_at) }}</p>
                </div>
              </div>
              <div class="flex items-center space-x-2">
                <a
                  :href="`/serve-file/${file.file_url}`"
                  target="_blank"
                  class="text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300 text-sm font-medium"
                >
                  View File
                </a>
                <a
                  :href="'/download/draft-resolution/' + file.file_id + '/' + encodeURIComponent(file.file_url)"
                  target="_blank"
                  download
                  class="inline-flex items-center px-3 py-1 border border-gray-300 dark:border-gray-600 text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                >
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  Download
                </a>
              </div>
            </div>

            <!-- File Preview -->
            <div v-if="getFileTypeFromName(file.file_name) === 'pdf'" class="mt-4">
              <embed :src="`/serve-file/${file.file_url}`" type="application/pdf" width="100%" height="600px" class="border border-gray-300 dark:border-gray-600 rounded" />
            </div>
            <div v-else-if="getFileTypeFromName(file.file_name) === 'docx' || getFileTypeFromName(file.file_name) === 'doc'" class="mt-4">
              <iframe
                :src="getOfficeEmbedUrl(file.public_preview_url)"
                width="100%"
                height="600"
                frameborder="0"
                class="border border-gray-300 dark:border-gray-600 rounded"
              ></iframe>
            </div>
            <div v-else class="mt-4">
              <p class="text-sm text-gray-600 dark:text-gray-400">This file type cannot be previewed inline. Click "View File" to open it.</p>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-8">
          <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No files attached</h3>
          <p class="text-gray-500 dark:text-gray-400">This draft resolution doesn't have any attached files yet.</p>
        </div>
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
import { computed } from 'vue'

const props = defineProps({
  draft: Object,
  auth: {
    type: Object,
    default: () => ({})
  }
})

const currentUser = computed(() => props.auth.user || {})

const baseUrl = computed(() => typeof window !== 'undefined' ? window.location.origin : '')

const getOfficeEmbedUrl = (fileUrl) => {
  if (!fileUrl) return ''
  return `https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(fileUrl)}`
}

const getFileTypeFromName = (fileName) => {
  if (!fileName) return 'unknown'
  const extension = fileName.split('.').pop().toLowerCase()
  if (extension === 'pdf') return 'pdf'
  if (extension === 'docx' || extension === 'doc') return 'docx'
  return 'unknown'
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', {
    month: 'long',
    day: 'numeric',
    year: 'numeric'
  })
}

const formatStatus = (status) => {
  if (!status) return 'Draft'
  return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
}

const formatFileSize = (bytes) => {
  if (!bytes && bytes !== 0) return 'Size not available'
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}
</script>
