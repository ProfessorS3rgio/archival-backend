<template>
  <AdminLayout :title="formText.title">
    <div class="max-w-6xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ formText.title }}</h1>
          <p class="text-gray-600 dark:text-gray-400">{{ formText.subtitle }}</p>
        </div>
        <Link href="/national-office-communications" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          Back to National Offices
        </Link>
      </div>

      <!-- Form -->
      <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-6">
        <!-- Basic Information -->
        <div class="space-y-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
            Basic Information
          </h3>

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
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                placeholder="Enter title"
                :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.title }"
              />
              <p v-if="errors.title" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.title }}
              </p>
            </div>

            <!-- Subject/Municipality -->
            <div>
              <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Subject/Office <span class="text-red-500">*</span>
              </label>
              <select
                id="subject"
                v-model="form.subject"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.subject }"
              >
                <option value="">Select Subject/Office</option>
                <option v-for="option in subjectOptions" :key="option" :value="option">
                  {{ option }}
                </option>
              </select>
              <p v-if="errors.subject" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.subject }}
              </p>
            </div>

            <!-- Custom Subject Input (when Others is selected) -->
            <div v-if="form.subject === 'Others (Please specify)'">
              <label for="custom_subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Specify Subject/Office <span class="text-red-500">*</span>
              </label>
              <input
                id="custom_subject"
                v-model="customSubject"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                placeholder="Enter custom subject/office"
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
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                placeholder="e.g., 2024"
                min="1900"
                max="2100"
                :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.year }"
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
                  <label for="files" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
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

          <!-- Existing Files (for edit mode) -->
          <div v-if="props.nationalOffice && props.nationalOffice.file_name" class="space-y-2">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Existing File:</h4>
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-3">
                  <div class="flex-shrink-0">
                    <svg v-if="getFileTypeFromName(props.nationalOffice.file_name) === 'pdf'" class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                    </svg>
                    <svg v-else class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ props.nationalOffice.file_name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatFileSize(props.nationalOffice.file_size) }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Uploaded {{ new Date(props.nationalOffice.date_uploaded).toLocaleDateString() }}</p>
                  </div>
                </div>
                <div class="flex items-center space-x-2">
                  <a
                    :href="`/serve-file/${props.nationalOffice.file_url}`"
                    target="_blank"
                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium"
                  >
                    View
                  </a>
                  <button
                    type="button"
                    @click="removeExistingFile"
                    class="p-1 rounded-full transition-colors"
                    :class="removeExistingFileFlag
                      ? 'text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20'
                      : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                  >
                    <svg v-if="removeExistingFileFlag" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                  </button>
                </div>
              </div>

              <!-- File Preview -->
              <div v-if="!removeExistingFileFlag" class="mt-3 border-t border-gray-200 dark:border-gray-600 pt-3">
                <h5 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Preview</h5>
                <div v-if="getFileTypeFromName(props.nationalOffice.file_name) === 'pdf'" class="bg-gray-100 dark:bg-gray-700 rounded p-2">
                  <iframe
                    :src="`/serve-file/${props.nationalOffice.file_url}`"
                    class="w-full h-[500px] border border-gray-300 dark:border-gray-600 rounded"
                    type="application/pdf"
                  ></iframe>
                </div>
                <div v-else-if="(getFileTypeFromName(props.nationalOffice.file_name) === 'docx' || getFileTypeFromName(props.nationalOffice.file_name) === 'doc') && props.nationalOffice.public_preview_url" class="bg-gray-100 dark:bg-gray-700 rounded p-2">
                  <iframe
                    :src="getOfficeEmbedUrl(props.nationalOffice.public_preview_url)"
                    width="100%"
                    height="600"
                    frameborder="0"
                    class="border border-gray-300 dark:border-gray-600 rounded"
                  ></iframe>
                </div>
                <div v-else class="bg-gray-100 dark:bg-gray-700 rounded p-4 text-center">
                  <svg class="w-8 h-8 text-blue-500 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                  </svg>
                  <p class="text-sm text-gray-600 dark:text-gray-400">{{ props.nationalOffice.file_name }}</p>
                  <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">Document preview not available. <a :href="`/serve-file/${props.nationalOffice.file_url}`" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">Click here to view</a></p>
                </div>
              </div>

              <!-- Marked for Removal -->
              <div v-if="removeExistingFileFlag" class="mt-3 border-t border-red-200 dark:border-red-700 pt-3">
                <div class="flex items-center space-x-2 text-red-600 dark:text-red-400">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                  </svg>
                  <span class="text-sm font-medium">Marked for removal</span>
                </div>
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
                      <span class="text-sm text-gray-600 dark:text-gray-400">Converting document to PDF...</span>
                    </div>
                  </div>
                  <div v-else-if="previewObjects[index].type === 'pdf' && previewObjects[index].url" class="bg-gray-100 dark:bg-gray-700 rounded p-2">
                    <iframe
                      :src="previewObjects[index].url"
                      class="w-full h-[600px] border border-gray-300 dark:border-gray-600 rounded"
                      type="application/pdf"
                    ></iframe>
                  </div>                  <div v-else-if="previewObjects[index].type === 'office' && previewObjects[index].url" class="bg-gray-100 dark:bg-gray-700 rounded p-2">
                    <iframe
                      :src="getOfficeEmbedUrl(previewObjects[index].url)"
                      width="100%"
                      height="600"
                      frameborder="0"
                      class="border border-gray-300 dark:border-gray-600 rounded"
                    ></iframe>
                  </div>                  <div v-else-if="previewObjects[index].type === 'docx' && previewObjects[index].content" class="bg-gray-100 dark:bg-gray-700 rounded p-3 max-h-64 overflow-y-auto">
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

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
          <Link
            href="/national-office-communications"
            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          >
            Cancel
          </Link>
          <button
            type="submit"
            :disabled="processing"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            <svg v-if="processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-else>
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
            </span>
            {{ processing ? (props.nationalOffice ? 'Updating...' : 'Creating...') : formText.button }}
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>

  <!-- Floating Developer Footer -->
  <FloatingFooter />
</template>

<script setup>
import { ref, computed, onUnmounted, onMounted, watch } from 'vue'
import FloatingFooter from '@/Components/FloatingFooter.vue'
import { useForm, usePage, router } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useFileUpload } from '@/composables/useFileUpload'
import axios from 'axios'

const page = usePage()

const props = defineProps({
  nationalOffice: {
    type: Object,
    default: null
  },
  isEditing: {
    type: Boolean,
    default: false
  },
  subjectOptions: {
    type: Array,
    default: () => []
  }
})

const form = useForm({
  title: '',
  subject: '',
  year: new Date().getFullYear(),
  date_received: '',
  files: [],
  extracted_text: ''
})

// Custom subject for "Others" option
const customSubject = ref('')

// Available subject options (from database + Others option)
const subjectOptions = computed(() => {
  const options = [...props.subjectOptions]
  if (!options.includes('Others (Please specify)')) {
    options.push('Others (Please specify)')
  }
  return options
})

// Use the file upload composable with auto-fill disabled
const {
  selectedFiles,
  previewObjects,
  processingFiles,
  handleFileSelect,
  removeFile,
  getFileType,
  formatFileSize,
  cleanupAllPreviews,
  getOfficeEmbedUrl
} = useFileUpload(false)

// Existing file removal
const removeExistingFileFlag = ref(false)
const toggleRemoveExistingFile = () => {
  removeExistingFileFlag.value = !removeExistingFileFlag.value
}

// Processing and errors
const processing = computed(() => form.processing)
const errors = computed(() => form.errors)

// Dynamic text based on form type
const formText = computed(() => ({
  title: props.isEditing ? 'Edit National Office Communication' : 'Create New National Office Communication',
  subtitle: props.isEditing ? 'Update national office communication details' : 'Add a new national office communication to the system',
  button: props.isEditing ? 'Update National Office Communication' : 'Create National Office Communication'
}))

// Initialize form with existing data if editing
const initializeForm = () => {
  if (props.isEditing && props.nationalOffice) {
    form.title = props.nationalOffice.title || ''
    form.subject = props.nationalOffice.subject || ''
    form.year = props.nationalOffice.year || new Date().getFullYear()
    form.date_received = props.nationalOffice.date_received
      ? new Date(props.nationalOffice.date_received).toISOString().split('T')[0]
      : ''
  }
}
// Initialize form on mount
onMounted(() => {
  initializeForm()
  // Auto form fill-up disabled
})

// Debug logging
console.log('NationalOfficeForm props:', props)
if (props.nationalOffice) {
  console.log('National office data:', props.nationalOffice)
}

// Cleanup on unmount
onUnmounted(() => {
  cleanupAllPreviews()
})

const getFileTypeFromName = (fileName) => {
  if (!fileName) return 'unknown'
  const extension = fileName.split('.').pop().toLowerCase()
  if (extension === 'pdf') return 'pdf'
  if (['doc', 'docx'].includes(extension)) return 'docx'
  return 'unknown'
}

const removeExistingFile = () => {
  removeExistingFileFlag.value = !removeExistingFileFlag.value
}

// Submit form
const submit = async () => {
  // Clear previous errors
  form.errors = {}
  
  // Client-side validation for required fields
  if (!form.title || !form.title.trim()) {
    form.errors.title = 'The title field is required.'
  }
  
  if (!form.subject || !form.subject.trim()) {
    form.errors.subject = 'The subject field is required.'
  }
  
  // If there are validation errors, don't submit
  if (Object.keys(form.errors).length > 0) {
    return
  }

  // Handle subject selection
  if (form.subject === 'Others (Please specify)') {
    form.subject = customSubject.value.trim()
  }

  // Collect extracted text from preview objects
  const extractedTexts = []
  if (selectedFiles.value && selectedFiles.value.length > 0) {
    for (let i = 0; i < selectedFiles.value.length; i++) {
      const preview = previewObjects.value[i]
      if (preview && preview.extractedText) {
        extractedTexts.push(preview.extractedText)
      }
    }
  }
  form.extracted_text = extractedTexts.join('\n\n')

  // For now, only send the first file since the database schema only supports one file per record
  if (selectedFiles.value && selectedFiles.value.length > 0) {
    form.files = [selectedFiles.value[0]]
  } else {
    form.files = []
  }

  form.added_by = page.props.auth?.user?.id || 1

  const submitMethod = props.isEditing ? 'put' : 'post'
  const submitUrl = props.isEditing
    ? `/national-office-communications/${props.nationalOffice.national_offices_id}`
    : '/national-office-communications'

  if (props.isEditing) {
    // For editing, build FormData manually like OrdinanceForm does
    const formData = new FormData()

    // Add all standard fields
    Object.entries(form.data()).forEach(([key, value]) => {
      // Skip files and extracted_text, we'll handle those separately
      if (key !== 'files' && key !== 'extracted_text') {
        formData.append(key, value)
      }
    })

    // Add remove_existing_file flag
    formData.append('remove_existing_file', removeExistingFileFlag.value ? '1' : '0')

    // Add files if present
    if (form.files && form.files.length > 0) {
      for (let i = 0; i < form.files.length; i++) {
        formData.append('files[]', form.files[i])
      }
    }

    // Add extracted_text
    if (form.extracted_text) {
      formData.append('extracted_text', form.extracted_text)
    }

    // Add method spoofing for PUT
    formData.append('_method', 'PUT')

    // Submit using Axios directly
    axios.post(submitUrl, formData)
      .then(response => {
        console.log('Form submitted successfully')
        router.visit('/national-office-communications')
      })
      .catch(error => {
        console.error('Form submission error:', error)
      })
  } else {
    // Create new national office communication
    console.log('Submitting CREATE with extracted_text:', form.extracted_text.substring(0, 100) + '...')
    
    // Build FormData for create as well
    const formData = new FormData()

    Object.entries(form.data()).forEach(([key, value]) => {
      if (key !== 'files' && key !== 'extracted_text') {
        formData.append(key, value)
      }
    })

    if (form.files && form.files.length > 0) {
      for (let i = 0; i < form.files.length; i++) {
        formData.append('files[]', form.files[i])
      }
    }

    if (form.extracted_text) {
      formData.append('extracted_text', form.extracted_text)
    }

    axios.post(submitUrl, formData)
      .then(response => {
        console.log('Form submitted successfully')
        // Reset form on success
        form.reset()
        selectedFiles.value = []
        removeExistingFileFlag.value = false
        cleanupAllPreviews()
        previewObjects.value = []
        router.visit('/national-office-communications')
      })
      .catch(error => {
        console.error('Form submission error:', error)
      })
  }
}
</script>

