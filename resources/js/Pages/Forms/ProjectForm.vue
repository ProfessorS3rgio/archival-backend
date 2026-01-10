<template>
  <AdminLayout :title="formText.title">
    <div class="max-w-6xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ formText.title }}</h1>
          <p class="text-gray-600 dark:text-gray-400">{{ formText.subtitle }}</p>
        </div>
        <Link href="/projects" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          Back to Projects
        </Link>
      </div>

      <!-- Form -->
      <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-6">
        <!-- Basic Information -->
        <div class="space-y-4">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
              Project Details
            </h3>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Project Name -->
            <div class="md:col-span-2">
              <label for="project_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Project Name <span class="text-red-500">*</span>
              </label>
              <input
                id="project_name"
                v-model="form.project_name"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.project_name }"
                placeholder="Enter project name"
                required
              />
              <p v-if="errors.project_name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.project_name }}
              </p>
            </div>

            <!-- Code No -->
            <div>
              <label for="code_no" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Code Number
              </label>
              <input
                id="code_no"
                v-model="form.code_no"
                type="number"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.code_no }"
                placeholder="e.g., 001"
                min="1"
              />
              <p v-if="errors.code_no" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.code_no }}
              </p>
            </div>

            <!-- District -->
            <div>
              <label for="district" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                District
              </label>
              <input
                id="district"
                v-model="form.district"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.district }"
                placeholder="Enter district"
              />
              <p v-if="errors.district" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.district }}
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
                max="2100"
              />
              <p v-if="errors.year" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.year }}
              </p>
            </div>

            <!-- Location -->
            <div>
              <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Location
              </label>
              <input
                id="location"
                v-model="form.location"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.location }"
                placeholder="Enter location"
              />
              <p v-if="errors.location" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.location }}
              </p>
            </div>

            <!-- Source Fund -->
            <div>
              <label for="source_fund" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Source of Fund
              </label>
              <select
                id="source_fund"
                v-model="form.source_fund"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.source_fund }"
              >
                <option value="">Select source of fund</option>
                <option value="National Government">National Government</option>
                <option value="Provincial Government">Provincial Government</option>
                <option value="Municipal Government">Municipal Government</option>
                <option value="Barangay Government">Barangay Government</option>
                <option value="Private Sector">Private Sector</option>
                <option value="NGO">NGO</option>
                <option value="International Aid">International Aid</option>
                <option value="Others (Please specify)">Others (Please specify)</option>
              </select>
              <p v-if="errors.source_fund" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.source_fund }}
              </p>
            </div>

            <!-- Custom Source Fund Input (when Others is selected) -->
            <div v-if="form.source_fund === 'Others (Please specify)'">
              <label for="custom_source_fund" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Specify Source of Fund <span class="text-red-500">*</span>
              </label>
              <input
                id="custom_source_fund"
                v-model="customSourceFund"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                placeholder="Enter custom source of fund"
              />
            </div>

            <!-- Source Type -->
            <div>
              <label for="source_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Source Type
              </label>
              <select
                id="source_type"
                v-model="form.source_type"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.source_type }"
              >
                <option value="">Select source type</option>
                <option value="Budget Allocation">Budget Allocation</option>
                <option value="Grant">Grant</option>
                <option value="Loan">Loan</option>
                <option value="Donation">Donation</option>
                <option value="Investment">Investment</option>
                <option value="Partnership">Partnership</option>
                <option value="Other">Other</option>
                <option value="Others (Please specify)">Others (Please specify)</option>
              </select>
              <p v-if="errors.source_type" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.source_type }}
              </p>
            </div>

            <!-- Custom Source Type Input (when Others is selected) -->
            <div v-if="form.source_type === 'Others (Please specify)'">
              <label for="custom_source_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Specify Source Type <span class="text-red-500">*</span>
              </label>
              <input
                id="custom_source_type"
                v-model="customSourceType"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                placeholder="Enter custom source type"
              />
            </div>

            <!-- Amount -->
            <div>
              <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Amount (₱)
              </label>
              <input
                id="amount"
                v-model="form.amount"
                type="number"
                step="0.01"
                min="0"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.amount }"
                placeholder="0.00"
              />
              <p v-if="errors.amount" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.amount }}
              </p>
            </div>
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

          <!-- Existing File Preview (Edit Mode) -->
          <div v-if="isEditing && project?.file_url && selectedFiles.length === 0" class="space-y-2">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Current File:</h4>
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-3">
                  <div class="flex-shrink-0">
                    <svg v-if="project.file_type?.includes('pdf')" class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                    </svg>
                    <svg v-else class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ project.file_name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatFileSize(project.file_size) }} • {{ project.file_type }}</p>
                  </div>
                </div>
                <a
                  :href="'/download/project/' + project.project_id + '/' + encodeURIComponent(project.file_url)"
                  target="_blank"
                  class="inline-flex items-center px-3 py-1 border border-gray-300 dark:border-gray-600 text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
                >
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  Download
                </a>
              </div>

              <!-- Existing File Preview -->
              <div class="mt-3 border-t border-gray-200 dark:border-gray-600 pt-3">
                <h5 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Preview</h5>
                <div v-if="project.file_type?.includes('pdf')" class="bg-gray-100 dark:bg-gray-700 rounded p-2">
                  <embed :src="`${baseUrl}/serve-file/${project.file_url}`" type="application/pdf" width="100%" height="600px" class="border border-gray-300 dark:border-gray-600 rounded" />
                </div>
                <div v-else-if="(project.file_name?.toLowerCase().endsWith('.docx') || project.file_name?.toLowerCase().endsWith('.doc')) && project.public_preview_url" class="bg-gray-100 dark:bg-gray-700 rounded p-2">
                  <iframe
                    :src="getOfficeEmbedUrl(project.public_preview_url)"
                    width="100%"
                    height="600"
                    frameborder="0"
                    class="border border-gray-300 dark:border-gray-600 rounded"
                  ></iframe>
                </div>
                <div v-else class="bg-gray-100 dark:bg-gray-700 rounded p-3">
                  <p class="text-sm text-gray-600 dark:text-gray-400">Preview not available for this file type. Click "Download" to view the file.</p>
                </div>
              </div>

              <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                Upload a new file above to replace this one.
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
                  <div v-else-if="(previewObjects[index].type === 'office' || previewObjects[index].type === 'docx') && previewObjects[index].url" class="bg-gray-100 dark:bg-gray-700 rounded p-2">
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
import { ref, computed, onUnmounted } from 'vue'
import FloatingFooter from '@/Components/FloatingFooter.vue'
import { useForm, router } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useFileUpload } from '@/composables/useFileUpload'
import axios from 'axios'

// Props
const props = defineProps({
  project: {
    type: Object,
    default: null
  },
  isEditing: {
    type: Boolean,
    default: false
  }
})

// Form data
const form = useForm({
  code_no: props.project?.code_no || '',
  district: props.project?.district || '',
  location: props.project?.location || '',
  project_name: props.project?.project_name || '',
  year: props.project?.year || '',
  source_fund: props.project?.source_fund || '',
  source_type: props.project?.source_type || '',
  amount: props.project?.amount || '',
  file: null,
  files: [], // Add files array for composable compatibility
  extracted_text: '',
})

// Use the file upload composable (no auto-fill for projects)
const {
  selectedFiles,
  previewObjects,
  processingFiles,
  handleFileSelect,
  removeFile,
  getFileType,
  formatFileSize,
  getOfficeEmbedUrl,
  cleanupAllPreviews
} = useFileUpload(false)

const loading = ref(false)
const errors = computed(() => form.errors)
const baseUrl = computed(() => typeof window !== 'undefined' ? window.location.origin : '')
const customSourceFund = ref('')
const customSourceType = ref('')

// Make props available in template
const isEditing = computed(() => props.isEditing)
const project = computed(() => props.project)

// Dynamic text based on form type
const formText = computed(() => ({
  title: props.isEditing ? 'Edit Project' : 'Create New Project',
  subtitle: props.isEditing ? 'Update project information' : 'Add a new project to the system',
  button: props.isEditing ? 'Update Project' : 'Create Project'
}))

// Submit form
const submit = async () => {
  loading.value = true

  // Handle custom source fund
  if (form.source_fund === 'Others (Please specify)' && customSourceFund.value) {
    form.source_fund = customSourceFund.value
  }

  // Handle custom source type
  if (form.source_type === 'Others (Please specify)' && customSourceType.value) {
    form.source_type = customSourceType.value
  }

  // Collect extracted text from preview objects if file was uploaded
  let extractedText = ''
  if (selectedFiles.value && selectedFiles.value.length > 0) {
    const preview = previewObjects.value[0]
    if (preview && preview.extractedText) {
      extractedText = preview.extractedText
    }
  }

  // Build FormData
  const formData = new FormData()
  
  // Add all standard fields
  formData.append('code_no', form.code_no)
  formData.append('district', form.district)
  formData.append('location', form.location)
  formData.append('project_name', form.project_name)
  formData.append('year', form.year)
  formData.append('source_fund', form.source_fund)
  formData.append('source_type', form.source_type)
  formData.append('amount', form.amount)
  formData.append('extracted_text', extractedText || '')
  
  // Add file if present
  if (selectedFiles.value && selectedFiles.value.length > 0) {
    formData.append('file', selectedFiles.value[0])
  }

  try {
    if (props.isEditing) {
      // Update existing project
      console.log('Submitting UPDATE with extracted_text:', extractedText.substring(0, 100) + '...')
      formData.append('_method', 'PUT')
      
      axios.post(`/projects/${props.project.project_id}`, formData)
        .then(response => {
          console.log('Form submitted successfully:', response.data)
          cleanupAllPreviews()
          router.visit('/projects')
        })
        .catch(error => {
          console.error('Form submission error:', error.response?.data || error)
          loading.value = false
        })
    } else {
      // Create new project
      console.log('Submitting CREATE with extracted_text:', extractedText.substring(0, 100) + '...')
      
      axios.post('/projects', formData)
        .then(response => {
          console.log('Form submitted successfully:', response.data)
          form.reset()
          selectedFiles.value = []
          cleanupAllPreviews()
          previewObjects.value = []
          router.visit('/projects')
        })
        .catch(error => {
          console.error('Form submission error:', error.response?.data || error)
          loading.value = false
        })
    }
  } catch (error) {
    console.error('Form submission error:', error)
    loading.value = false
  }
}

// Cleanup preview objects when component is unmounted
onUnmounted(() => {
  cleanupAllPreviews()
})
</script>
