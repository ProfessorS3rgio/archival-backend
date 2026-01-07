<template>
  <AdminLayout :title="props.draft ? 'Edit Draft Resolution' : 'Create Draft Resolution'">
    <div class="max-w-6xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ props.draft ? 'Edit Draft Resolution' : 'Create New Draft Resolution' }}</h1>
          <p class="text-gray-600 dark:text-gray-400">{{ props.draft ? 'Update draft resolution information' : 'Add a new draft resolution to the system' }}</p>
        </div>
        <Link href="/draft-resolutions" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          Back to Draft Resolutions
        </Link>
      </div>

      <!-- Form -->
      <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-6">
        <!-- Basic Information -->
        <div class="space-y-4">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
              Basic Information
            </h3>

            <!-- Auto-fill Toggle -->
            <div class="flex items-center space-x-3">
              <label for="auto_fill_toggle" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                Auto-fill from documents
              </label>
              <button
                type="button"
                @click.prevent.stop="toggleAutoFill"
                :class="[
                  'relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2',
                  autoFillEnabled ? 'bg-purple-600' : 'bg-gray-200 dark:bg-gray-700'
                ]"
              >
                <span
                  :class="[
                    'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                    autoFillEnabled ? 'translate-x-6' : 'translate-x-1'
                  ]"
                />
              </button>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Author -->
            <div>
              <label for="author" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Author
              </label>
              <input
                id="author"
                v-model="form.author"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                placeholder="Enter author name"
                :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.author }"
              />
              <p v-if="errors.author" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.author }}
              </p>
            </div>

            <!-- Session -->
            <div>
              <label for="session" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Session
              </label>
              <input
                id="session"
                v-model="form.session"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                placeholder="e.g., Regular Session 1"
                :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.session }"
              />
              <p v-if="errors.session" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.session }}
              </p>
            </div>

            <!-- Status -->
            <div>
              <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Status
              </label>
              <select
                id="status"
                v-model="form.status"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.status }"
              >
                <option value="draft">Draft</option>
                <option value="in_review">In Review</option>
                <option value="pending_approval">Pending Approval</option>
              </select>
              <p v-if="errors.status" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.status }}
              </p>
            </div>
          </div>

          <!-- Title -->
          <div>
            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Title <span class="text-red-500">*</span>
            </label>
            <input
              id="title"
              v-model="form.title"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
              placeholder="Enter draft resolution title"
              :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.title }"
            />
            <p v-if="errors.title" class="mt-1 text-sm text-red-600 dark:text-red-400">
              {{ errors.title }}
            </p>
          </div>

          <!-- Subject -->
          <div>
            <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Subject
            </label>
            <select
              id="subject"
              disabled
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 cursor-not-allowed"
              :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.subject }"
            >
              <option value="">Not applicable for draft resolutions</option>
            </select>
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Subject categorization is not required for draft resolutions.</p>
            <p v-if="errors.subject" class="mt-1 text-sm text-red-600 dark:text-red-400">
              {{ errors.subject }}
            </p>
          </div>
        </div>

        <!-- File Upload Section -->
        <div class="space-y-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
            Document Files
          </h3>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Upload Files (PDF, DOC, DOCX)
            </label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
              <div class="space-y-1 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                  <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
                <div class="flex text-sm text-gray-600 dark:text-gray-400">
                  <label for="files" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-purple-600 dark:text-purple-400 hover:text-purple-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-purple-500">
                    <span>Upload files</span>
                    <input
                      id="files"
                      name="files"
                      type="file"
                      multiple
                      accept=".pdf,.doc,.docx"
                      @change="handleFileSelect($event, form)"
                      class="sr-only"
                    />
                  </label>
                  <p class="pl-1">or drag and drop</p>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                  PDF, DOC, DOCX up to 10MB each
                </p>
              </div>
            </div>
          </div>

          <!-- Selected Files Preview -->
          <div v-if="selectedFiles.length > 0" class="space-y-2">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Selected Files:</h4>
            <div class="space-y-2">
              <div v-for="(file, index) in selectedFiles" :key="index" class="border border-purple-200 dark:border-purple-600 rounded-lg p-4 bg-purple-50 dark:bg-purple-900/10">
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                      <svg v-if="getFileType(file)" class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                      </svg>
                      <svg v-else class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-gray-900 dark:text-white">{{ file.name }}</p>
                      <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatFileSize(file.size) }}</p>
                      <p class="text-xs text-purple-600 dark:text-purple-400">Ready to upload</p>
                    </div>
                  </div>
                  <div class="flex items-center space-x-2">
                    <button
                      type="button"
                      @click="removeFile(index)"
                      class="p-1 rounded-full text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                      title="Remove file"
                    >
                      <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                      </svg>
                    </button>
                  </div>
                </div>

                <!-- File Preview -->
                <div v-if="previewObjects[index] || processingFiles[index]" class="mt-3 border-t border-purple-200 dark:border-purple-600 pt-3">
                  <h5 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Preview</h5>
                  <div v-if="processingFiles[index] || (previewObjects[index] && previewObjects[index].type === 'loading')" class="bg-gray-100 dark:bg-gray-700 rounded p-4 flex items-center justify-center">
                    <div class="flex items-center space-x-3">
                      <svg class="animate-spin h-5 w-5 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      <span class="text-sm text-gray-600 dark:text-gray-400">Converting document to PDF...</span>
                    </div>
                  </div>
                  <div v-else-if="previewObjects[index] && previewObjects[index].type === 'pdf' && previewObjects[index].url" class="bg-gray-100 dark:bg-gray-700 rounded p-2">
                    <iframe
                      :src="previewObjects[index].url"
                      class="w-full h-[600px] border border-gray-300 dark:border-gray-600 rounded"
                      title="PDF Preview"
                    ></iframe>
                  </div>
                  <div v-else-if="previewObjects[index] && previewObjects[index].type === 'docx' && previewObjects[index].content" class="bg-gray-100 dark:bg-gray-700 rounded p-3 max-h-64 overflow-y-auto">
                    <pre class="text-xs text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ previewObjects[index].content }}</pre>
                  </div>
                  <div v-else-if="previewObjects[index] && previewObjects[index].type === 'doc' && previewObjects[index].content" class="bg-yellow-50 dark:bg-yellow-900/20 rounded p-3">
                    <p class="text-xs text-yellow-600 dark:text-yellow-400">{{ previewObjects[index].content }}</p>
                  </div>
                  <div v-else-if="previewObjects[index] && previewObjects[index].type === 'error'" class="bg-red-50 dark:bg-red-900/20 rounded p-3">
                    <p class="text-xs text-red-600 dark:text-red-400">{{ previewObjects[index].content }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Existing Files (for edit mode) -->
          <div v-if="props.draft && props.draft.files && props.draft.files.length > 0" class="space-y-2">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Existing Files:</h4>
            <div class="space-y-2">
              <div v-for="file in props.draft.files" :key="file.file_id" class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
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
                      View
                    </a>
                    <button
                      type="button"
                      @click="removeExistingFile(file.file_id)"
                      class="p-1 rounded-full transition-colors"
                      :class="form.remove_files.includes(file.file_id)
                        ? 'text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20'
                        : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                    >
                      <svg v-if="form.remove_files.includes(file.file_id)" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                      </svg>
                      <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                      </svg>
                    </button>
                  </div>
                </div>

                <!-- File Preview -->
                <div v-if="!form.remove_files.includes(file.file_id)" class="mt-3 border-t border-gray-200 dark:border-gray-600 pt-3">
                  <h5 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Preview</h5>
                  <div v-if="getFileTypeFromName(file.file_name) === 'pdf'" class="bg-gray-100 dark:bg-gray-700 rounded p-2">
                    <iframe
                      :src="`/serve-file/${file.file_url}`"
                      class="w-full h-[600px] border border-gray-300 dark:border-gray-600 rounded"
                      title="PDF Preview"
                    ></iframe>
                  </div>
                  <div v-else class="bg-gray-100 dark:bg-gray-700 rounded p-4 text-center">
                    <svg class="w-8 h-8 text-blue-500 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ file.file_name }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
          <Link
            href="/draft-resolutions"
            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          >
            Cancel
          </Link>
          <button
            type="submit"
            :disabled="processing"
            class="px-4 py-2 bg-purple-600 hover:bg-purple-700 disabled:bg-purple-400 text-white text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2"
          >
            <span v-if="processing">Saving...</span>
            <span v-else>{{ props.draft ? 'Update Draft Resolution' : 'Create Draft Resolution' }}</span>
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>

  <!-- Floating Developer Footer -->
  <FloatingFooter />
</template>

<script setup>
import { ref, computed, onUnmounted } from 'vue'
import FloatingFooter from '@/Components/FloatingFooter.vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useFileUpload } from '@/composables/useFileUpload'
import axios from 'axios'

const page = usePage()

const props = defineProps({
  errors: {
    type: Object,
    default: () => ({})
  },
  autoFillEnabled: {
    type: Boolean,
    default: true
  },
  draft: {
    type: Object,
    default: null
  }
})

const form = useForm({
  author: props.draft?.author || '',
  session: props.draft?.session || '',
  title: props.draft?.title || '',
  status: props.draft?.status || 'draft',
  added_by: props.draft?.added_by || '',
  files: [],
  extracted_text: [],
  remove_files: [] // Track files to be removed
})

// Use the file upload composable
const {
  selectedFiles,
  previewObjects,
  processingFiles,
  autoFillEnabled,
  handleFileSelect,
  removeFile,
  getFileType,
  formatFileSize,
  toggleAutoFill,
  cleanupAllPreviews
} = useFileUpload(props.autoFillEnabled)

const processing = computed(() => form.processing)
const errors = computed(() => form.errors)

const getFileTypeFromName = (fileName) => {
  if (!fileName) return 'unknown'
  const extension = fileName.split('.').pop().toLowerCase()
  if (extension === 'pdf') return 'pdf'
  if (['doc', 'docx'].includes(extension)) return 'docx'
  return 'unknown'
}

const removeExistingFile = (fileId) => {
  const index = form.remove_files.indexOf(fileId)
  if (index > -1) {
    // File is already marked for removal, unmark it
    form.remove_files.splice(index, 1)
  } else {
    // Mark file for removal (ensure no duplicates)
    if (!form.remove_files.includes(fileId)) {
      form.remove_files.push(fileId)
    }
  }
}

const submit = () => {
  form.added_by = page.props.auth?.user?.id || 1 // Get current user ID from Inertia page props

  console.log('Submitting form with data:', form.data())
  console.log('Selected files:', selectedFiles.value)
  console.log('Form files:', form.files)

  if (props.draft) {
    // Update existing draft resolution
    console.log('Submitting form with data:', form.data())
    console.log('Files marked for removal:', form.remove_files)

    // Build a complete custom form data object to ensure arrays are properly handled
    const formData = new FormData();

    // Add all standard fields
    Object.entries(form.data()).forEach(([key, value]) => {
      // Skip files and remove_files, we'll handle those separately
      if (key !== 'files' && key !== 'remove_files') {
        formData.append(key, value);
      }
    });

    // Add files if present
    if (form.files && form.files.length > 0) {
      for (let i = 0; i < form.files.length; i++) {
  formData.append('files[]', form.files[i]);
  const extractedText = previewObjects.value[i]?.extractedText ?? '';
  formData.append(`extracted_text[${i}]`, extractedText);
      }
    }

    // Add remove_files as an indexed array
    if (form.remove_files && form.remove_files.length > 0) {
      for (let i = 0; i < form.remove_files.length; i++) {
        formData.append(`remove_files[${i}]`, form.remove_files[i]);
      }
      console.log('Added remove_files to form data:', form.remove_files);
    }

    // Add method spoofing for PUT
    formData.append('_method', 'PUT');

    // Submit using Axios directly
    axios.post(`/draft-resolutions/${props.draft.resolution_draft_id}`, formData)
      .then(response => {
        console.log('Form submitted successfully:', response.data);
        window.location.href = '/draft-resolutions';
      })
      .catch(error => {
        console.error('Form submission error:', error);
      });
  } else {
    // Create new draft resolution
    form.extracted_text = form.files && form.files.length > 0
      ? form.files.map((_, index) => previewObjects.value[index]?.extractedText ?? '')
      : []

    form.post('/draft-resolutions', {
      forceFormData: true,
      onSuccess: () => {
        // Reset form on success
        form.reset()
        selectedFiles.value = []
        // Clean up all preview objects
        cleanupAllPreviews()
        previewObjects.value = []
      },
      onError: (errors) => {
        console.error('Form submission errors:', errors)
      }
    })
  }
}

// Cleanup preview objects when component is unmounted
onUnmounted(() => {
  cleanupAllPreviews()
})

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}
</script>
