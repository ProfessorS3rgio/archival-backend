<template>
  <AdminLayout :title="formText.title">
    <div class="max-w-6xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ formText.title }}</h1>
          <p class="text-gray-600 dark:text-gray-400">{{ formText.subtitle }}</p>
        </div>
        <Link href="/memorandum-of-agreements" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          Back to MOAs
        </Link>
      </div>

      <!-- Form -->
      <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-6">
        <!-- Basic Information -->
        <div class="space-y-4">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
              Memorandum of Agreement Details
            </h3>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Title -->
            <div class="md:col-span-2">
              <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Title <span class="text-red-500">*</span>
              </label>
              <input
                id="title"
                v-model="form.title"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.title }"
                placeholder="Enter MOA title"
                required
              />
              <p v-if="errors.title" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.title }}
              </p>
            </div>

            <!-- Resolution No -->
            <div>
              <label for="resolution_no" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Resolution Number
              </label>
              <input
                id="resolution_no"
                v-model="form.resolution_no"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.resolution_no }"
                placeholder="e.g., 001-2024"
              />
              <p v-if="errors.resolution_no" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.resolution_no }}
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
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.year }"
                placeholder="e.g., 2024"
                min="1900"
                :max="currentYear + 10"
              />
              <p v-if="errors.year" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.year }}
              </p>
            </div>

            <!-- Date Received -->
            <div>
              <label for="date_received" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Date Received
              </label>
              <input
                id="date_received"
                v-model="form.date_received"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.date_received }"
              />
              <p v-if="errors.date_received" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.date_received }}
              </p>
            </div>
          </div>

          <!-- File Upload Section -->
          <div class="space-y-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
              Document File
            </h3>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Upload File (PDF, DOC, DOCX)
              </label>
              <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
                <div class="space-y-1 text-center">
                  <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                  </svg>
                  <div class="flex text-sm text-gray-600 dark:text-gray-400">
                    <label for="file" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                      <span>Upload a file</span>
                      <input
                        id="file"
                        name="file"
                        type="file"
                        class="sr-only"
                        @change="handleFileSelect($event, form)"
                        accept=".pdf,.doc,.docx"
                      />
                    </label>
                    <p class="pl-1">or drag and drop</p>
                  </div>
                  <p class="text-xs text-gray-500 dark:text-gray-400">
                    PDF, DOC, DOCX up to 10MB
                  </p>
                </div>
              </div>
              <p v-if="errors.file" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.file }}
              </p>
            </div>

            <!-- Current File (when editing and file exists) -->
            <div v-if="isEditing && memorandumOfAgreement?.file_name && selectedFiles.length === 0" class="space-y-2">
              <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Current File:</h4>
              <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                      <svg v-if="memorandumOfAgreement.file_type === 'application/pdf'" class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                      </svg>
                      <svg v-else class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-gray-900 dark:text-white">{{ memorandumOfAgreement.file_name }}</p>
                      <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatFileSize(memorandumOfAgreement.file_size) }}</p>
                    </div>
                  </div>
                  <a
                    :href="`${baseUrl}/download/memorandum-of-agreement/${memorandumOfAgreement.moa_id}/${memorandumOfAgreement.file_name}`"
                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"
                    target="_blank"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Download
                  </a>
                </div>

                <!-- PDF Preview -->
                <div v-if="memorandumOfAgreement.file_type === 'application/pdf'" class="mt-4">
                  <embed
                    :src="baseUrl + '/serve-file/' + memorandumOfAgreement.file_url"
                    type="application/pdf"
                    class="w-full h-96 border border-gray-200 dark:border-gray-600 rounded"
                  />
                </div>

                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                  Upload a new file above to replace the current file.
                </p>
              </div>
            </div>

            <!-- Selected Files Preview -->
            <div v-if="selectedFiles.length > 0" class="space-y-2">
              <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Selected File:</h4>
              <div class="space-y-2">
                <div v-for="(file, index) in selectedFiles" :key="index" class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                  <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-3">
                      <div class="flex-shrink-0">
                        <svg v-if="getFileType(file) === 'pdf'" class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                        </svg>
                        <svg v-else class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                        </svg>
                      </div>
                      <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ file.name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatFileSize(file.size) }}</p>
                      </div>
                    </div>
                    <button
                      type="button"
                      @click="removeFile(index, form)"
                      class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
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
                    <div v-else-if="previewObjects[index].type === 'docx' && previewObjects[index].content" class="bg-gray-100 dark:bg-gray-700 rounded p-3 max-h-64 overflow-y-auto">
                      <pre class="text-xs text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ previewObjects[index].content }}</pre>
                    </div>
                    <div v-else-if="previewObjects[index].type === 'error'" class="bg-red-50 dark:bg-red-900/20 rounded p-3">
                      <p class="text-xs text-red-600 dark:text-red-400">{{ previewObjects[index].content }}</p>
                    </div>
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
            :disabled="loading"
            class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white text-sm font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed"
          >
            <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ formText.button }}
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>

  <!-- Floating Developer Footer -->
  <FloatingFooter />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import FloatingFooter from '@/Components/FloatingFooter.vue'
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useFileUpload } from '@/composables/useFileUpload'

// Props
const props = defineProps({
  memorandumOfAgreement: {
    type: Object,
    default: null
  },
  isEditing: {
    type: Boolean,
    default: false
  }
})

// Current year for validation
const currentYear = new Date().getFullYear()

// Form data
const form = useForm({
  resolution_no: props.memorandumOfAgreement?.resolution_no || '',
  date_received: props.memorandumOfAgreement?.date_received || '',
  title: props.memorandumOfAgreement?.title || '',
  year: props.memorandumOfAgreement?.year || currentYear,
  file: null,
  extracted_text: '',
})

// Use the file upload composable (no auto-fill for MOAs)
const {
  selectedFiles,
  previewObjects,
  processingFiles,
  handleFileSelect,
  removeFile,
  getFileType,
  formatFileSize,
  cleanupAllPreviews
} = useFileUpload(false)

const loading = ref(false)
const errors = computed(() => form.errors)
const baseUrl = computed(() => typeof window !== 'undefined' ? window.location.origin : '')

// Make props available in template
const isEditing = computed(() => props.isEditing)
const memorandumOfAgreement = computed(() => props.memorandumOfAgreement)

// Dynamic text based on form type
const formText = computed(() => ({
  title: props.isEditing ? 'Edit Memorandum of Agreement' : 'Create New Memorandum of Agreement',
  subtitle: props.isEditing ? 'Update MOA information' : 'Add a new memorandum of agreement to the system',
  button: props.isEditing ? 'Update MOA' : 'Create MOA'
}))

// Submit form
const submit = async () => {
  loading.value = true

  // Collect extracted text from preview objects if file was uploaded
  let extractedText = ''
  if (selectedFiles.value && selectedFiles.value.length > 0) {
    const preview = previewObjects.value[0]
    if (preview && preview.extractedText) {
      extractedText = preview.extractedText
    }
    // Set the file in form
    form.file = selectedFiles.value[0]
  }

  // Only set extracted_text if we have content
  form.extracted_text = extractedText || ''

  try {
    if (props.isEditing) {
      // Use transform to add _method for PUT request with file upload
      form.transform((data) => ({
        ...data,
        _method: 'PUT',
      })).post(`/memorandum-of-agreements/${props.memorandumOfAgreement.moa_id}`, {
        forceFormData: true,
        onSuccess: () => {
          cleanupAllPreviews()
        },
        onError: (err) => {
          console.error('Form submission errors:', err)
        }
      })
    } else {
      await form.post('/memorandum-of-agreements', {
        forceFormData: true,
        onSuccess: () => {
          // Reset form on success
          form.reset()
          selectedFiles.value = []
          cleanupAllPreviews()
          previewObjects.value = []
        },
        onError: (err) => {
          console.error('Form submission errors:', err)
        }
      })
    }
  } catch (error) {
    console.error('Form submission error:', error)
  } finally {
    loading.value = false
  }
}
</script>
