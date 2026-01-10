<template>
  <AdminLayout :title="formText.title">
    <div class="max-w-4xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ formText.title }}</h1>
          <p class="text-gray-600 dark:text-gray-400">{{ formText.subtitle }}</p>
        </div>
        <Link href="/committee-reports" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          Back to Committee Reports
        </Link>
      </div>

      <!-- Form -->
      <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-6">
        <!-- Basic Information -->
        <div class="space-y-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
            Committee Report Information
          </h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Report Number -->
            <div>
              <label for="committee_report_no" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Report Number
              </label>
              <input
                id="committee_report_no"
                v-model="form.committee_report_no"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                placeholder="Enter report number"
                :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.committee_report_no }"
              />
              <p v-if="errors.committee_report_no" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.committee_report_no }}
              </p>
            </div>

            <!-- Subject -->
            <div>
              <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Subject <span class="text-red-500">*</span>
              </label>
              <select
                id="subject"
                v-model="form.subject"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.subject }"
              >
                <option value="">Select Subject</option>
                <option v-for="option in subjectOptions" :key="option" :value="option">
                  {{ option }}
                </option>
                <option value="Others (Please specify)">Others (Please specify)</option>
              </select>
              <p v-if="errors.subject" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.subject }}
              </p>
            </div>

            <!-- Custom Subject Input (when Others is selected) -->
            <div v-if="form.subject === 'Others (Please specify)'">
              <label for="custom_subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Specify Subject <span class="text-red-500">*</span>
              </label>
              <input
                id="custom_subject"
                v-model="customSubject"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                placeholder="Enter custom subject"
                :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.custom_subject }"
              />
              <p v-if="errors.custom_subject" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.custom_subject }}
              </p>
            </div>

            <!-- Year -->
            <div>
              <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Year
              </label>
              <input
                id="year"
                v-model="form.year"
                type="number"
                :min="1900"
                :max="new Date().getFullYear() + 1"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                placeholder="Enter year"
                :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.year }"
              />
              <p v-if="errors.year" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.year }}
              </p>
            </div>
          </div>

          <!-- Title -->
          <div>
            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Title <span class="text-red-500">*</span>
            </label>
            <textarea
              id="title"
              v-model="form.title"
              required
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-vertical"
              placeholder="Enter committee report title"
              :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.title }"
            ></textarea>
            <p v-if="errors.title" class="mt-1 text-sm text-red-600 dark:text-red-400">
              {{ errors.title }}
            </p>
          </div>
        </div>

        <!-- File Upload Section -->
        <div class="space-y-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
            Document Upload
          </h3>

          <!-- File Input -->
          <div>
            <label for="files" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Attach Document
            </label>
            <div class="mt-1 relative">
              <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
                <div class="space-y-1 text-center">
                  <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                  </svg>
                  <div class="flex text-sm text-gray-600 dark:text-gray-400">
                    <label for="files" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                      <span>Upload a file</span>
                      <input
                        id="files"
                        name="files"
                        type="file"
                        class="sr-only"
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png"
                        @change="handleFileSelect($event, form)"
                        multiple
                      />
                    </label>
                    <p class="pl-1">or drag and drop</p>
                  </div>
                  <p class="text-xs text-gray-500 dark:text-gray-400">
                    PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG up to 10MB
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Existing File (for edit) -->
          <div v-if="props.report && props.report.file_name" class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                  <svg v-if="getFileTypeFromName(props.report.file_name) === 'pdf'" class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                  </svg>
                  <svg v-else class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900 dark:text-white">{{ props.report.file_name }}</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatFileSize(props.report.file_size) }}</p>
                </div>
              </div>
              <div class="flex items-center space-x-2">
                <a
                  :href="'/serve-file/' + props.report.file_url"
                  target="_blank"
                  class="inline-flex items-center px-3 py-1 border border-gray-300 dark:border-gray-600 text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                  View
                </a>
                <button
                  type="button"
                  @click="removeExistingFile"
                  class="inline-flex items-center px-3 py-1 border border-red-300 dark:border-red-600 text-sm leading-4 font-medium rounded-md text-red-700 dark:text-red-300 bg-white dark:bg-gray-800 hover:bg-red-50 dark:hover:bg-red-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                >
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                  Remove
                </button>
              </div>
            </div>

            <!-- File Preview for existing file -->
            <div class="mt-3 border-t border-gray-200 dark:border-gray-600 pt-3">
              <h5 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Preview</h5>
              <div v-if="getFileTypeFromName(props.report.file_name) === 'pdf'" class="bg-gray-100 dark:bg-gray-700 rounded p-2">
                <iframe
                  :src="'/serve-file/' + props.report.file_url"
                  class="w-full h-[600px] border border-gray-300 dark:border-gray-600 rounded"
                  type="application/pdf"
                ></iframe>
              </div>
              <div v-else-if="getFileTypeFromName(props.report.file_name) === 'office' && props.report.public_preview_url" class="bg-gray-100 dark:bg-gray-700 rounded p-2">
                <iframe
                  :src="getOfficeEmbedUrl(props.report.public_preview_url)"
                  width="100%"
                  height="600"
                  frameborder="0"
                  class="border border-gray-300 dark:border-gray-600 rounded"
                ></iframe>
              </div>
              <div v-else class="bg-yellow-50 dark:bg-yellow-900/20 rounded p-3">
                <p class="text-xs text-yellow-600 dark:text-yellow-400">Preview not available for this file type. Click "View" to open the file.</p>
              </div>
            </div>
          </div>

          <!-- Selected Files Preview -->
          <div v-if="selectedFiles.length > 0" class="space-y-2">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Selected Files:</h4>
            <div class="space-y-2">
              <div v-for="(file, index) in selectedFiles" :key="index" class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 relative">
                      <svg v-if="getFileType(file) === 'pdf'" class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                      </svg>
                      <svg v-else class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                      </svg>
                      <!-- Individual file processing spinner -->
                      <div v-if="processingFiles[index]" class="absolute inset-0 flex items-center justify-center bg-white dark:bg-gray-800 bg-opacity-75 rounded">
                        <svg class="animate-spin h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                      </div>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-gray-900 dark:text-white">{{ file.name }}</p>
                      <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatFileSize(file.size) }}</p>
                      <p v-if="processingFiles[index]" class="text-xs text-blue-600 dark:text-blue-400">Processing...</p>
                    </div>
                  </div>
                  <button
                    type="button"
                    @click="removeFile(index, form)"
                    :disabled="processingFiles[index]"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                  </button>
                </div>
                
                <!-- File Preview -->
                <div v-if="previewObjects[index] || processingFiles[index]" class="mt-3 border-t border-gray-200 dark:border-gray-600 pt-3">
                  <h5 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Preview</h5>
                  <div v-if="processingFiles[index] || (previewObjects[index] && previewObjects[index].type === 'loading')" class="bg-gray-100 dark:bg-gray-700 rounded p-4 flex items-center justify-center">
                    <div class="flex items-center space-x-3">
                      <svg class="animate-spin h-5 w-5 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      <span class="text-sm text-gray-600 dark:text-gray-400">Processing document...</span>
                    </div>
                  </div>
                  <div v-else-if="previewObjects[index].type === 'pdf' && previewObjects[index].url" class="bg-gray-100 dark:bg-gray-700 rounded p-2">
                    <iframe 
                      :src="previewObjects[index].url" 
                      class="w-full h-[600px] border border-gray-300 dark:border-gray-600 rounded"
                      type="application/pdf"
                    ></iframe>
                  </div>
                  <div v-else-if="previewObjects[index].type === 'office' && previewObjects[index].url" class="bg-gray-100 dark:bg-gray-700 rounded p-2">
                    <iframe
                      :src="getOfficeEmbedUrl(previewObjects[index].url)"
                      width="100%"
                      height="600"
                      frameborder="0"
                      class="border border-gray-300 dark:border-gray-600 rounded"
                    ></iframe>
                  </div>
                  <div v-else-if="previewObjects[index].type === 'error'" class="bg-red-50 dark:bg-red-900/20 rounded p-3">
                    <p class="text-xs text-red-600 dark:text-red-400">{{ previewObjects[index].content }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
          <button
            type="submit"
            :disabled="processing"
            class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed"
          >
            <svg v-if="processing" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            {{ processing ? (props.report ? 'Updating...' : 'Creating...') : formText.button }}
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

const page = usePage()

const props = defineProps({
  errors: {
    type: Object,
    default: () => ({})
  },
  report: {
    type: Object,
    default: null
  },
  committees: {
    type: Array,
    default: () => []
  },
  subjectOptions: {
    type: Array,
    default: () => []
  }
})

const form = useForm({
  committee_report_no: props.report?.committee_report_no || '',
  subject: props.report?.subject || '',
  title: props.report?.title || '',
  year: props.report?.year || new Date().getFullYear(),
  files: [],
  extracted_text: '',
})

const processing = computed(() => form.processing)
const errors = computed(() => form.errors)

const customSubject = ref('')

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
  cleanupAllPreviews,
  getOfficeEmbedUrl
} = useFileUpload(false) // Disable auto-fill for committee reports

// Dynamic text based on form type
const formText = computed(() => {
  return {
    title: props.report ? 'Edit Committee Report' : 'Create New Committee Report',
    subtitle: props.report ? 'Update committee report information' : 'Add a new committee report to the system',
    button: props.report ? 'Update Committee Report' : 'Create Committee Report'
  }
})

const getFileTypeFromName = (fileName) => {
  if (!fileName) return 'unknown'
  const extension = fileName.split('.').pop().toLowerCase()
  if (extension === 'pdf') return 'pdf'
  if (['doc', 'docx'].includes(extension)) return 'office'
  return 'unknown'
}

const removeExistingFile = () => {
  // For now, just note that existing file will be replaced
  // In a full implementation, you might want to mark for deletion
}

const submit = () => {
  // Handle custom subject
  if (form.subject === 'Others (Please specify)' && customSubject.value) {
    form.subject = customSubject.value
  }

  // Prepare extracted text data
  const fileExtractedTexts = selectedFiles.value.map((_, index) => previewObjects.value[index]?.extractedText ?? '')
  const concatenatedExtractedText = fileExtractedTexts.filter(text => text.trim()).join('\n\n')

  // Build FormData
  const formData = new FormData();
  
  // Add all standard fields
  Object.entries(form.data()).forEach(([key, value]) => {
    if (key !== 'files' && key !== 'extracted_text' && key !== 'file_extracted_texts') {
      formData.append(key, value);
    }
  });

  // Add concatenated extracted_text
  formData.append('extracted_text', concatenatedExtractedText);
  
  // Add files if present
  if (form.files && form.files.length > 0) {
    for (let i = 0; i < form.files.length; i++) {
      formData.append('files[]', form.files[i]);
      formData.append(`file_extracted_texts[${i}]`, fileExtractedTexts[i] || '');
    }
  }

  if (props.report) {
    // Update existing report
    console.log('Submitting UPDATE with extracted_text:', concatenatedExtractedText.substring(0, 100) + '...')
    formData.append('_method', 'PUT');
    
    axios.post(`/committee-reports/${props.report.report_id}`, formData)
      .then(response => {
        console.log('Form submitted successfully:', response.data);
        window.location.href = '/committee-reports';
      })
      .catch(error => {
        console.error('Form submission error:', error.response?.data || error);
      });
  } else {
    // Create new report
    console.log('Submitting CREATE with extracted_text:', concatenatedExtractedText.substring(0, 100) + '...')
    
    axios.post('/committee-reports', formData)
      .then(response => {
        console.log('Form submitted successfully:', response.data);
        window.location.href = '/committee-reports';
      })
      .catch(error => {
        console.error('Form submission error:', error.response?.data || error);
      });
  }
}

// Cleanup preview objects when component is unmounted
onUnmounted(() => {
  cleanupAllPreviews()
})
</script>