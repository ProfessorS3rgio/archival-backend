<template>
  <AdminLayout :title="formText.title">
    <div class="max-w-6xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ formText.title }}</h1>
          <p class="text-gray-600 dark:text-gray-400">{{ formText.subtitle }}</p>
        </div>
        <Link :href="isMunicipalForm ? '/municipal-resolutions' : '/resolutions'" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          Back to {{ type }} Resolutions
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
                  'relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2',
                  autoFillEnabled ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-700'
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
            <!-- Resolution Number -->
            <div>
              <label for="resolution_no" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Resolution Number <span class="text-red-500">*</span>
              </label>
              <input
                id="resolution_no"
                v-model="form.resolution_no"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                placeholder="Enter resolution number (e.g., 20219-6820)"
                :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.resolution_no }"
              />
              <p v-if="errors.resolution_no" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.resolution_no }}
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
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                placeholder="e.g., Regular Session 1"
                :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.session }"
              />
              <p v-if="errors.session" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.session }}
              </p>
            </div>
          </div>

          <!-- Authors -->
          <div>
            <label for="authors" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Authors
            </label>
            <div v-for="(author, index) in form.authors" :key="index" class="flex items-center space-x-2 mb-2">
              <input
                :id="'author_' + index"
                v-model="form.authors[index]"
                type="text"
                class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                placeholder="Enter author name"
                :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.author }"
              />
              <button
                v-if="form.authors.length > 1"
                type="button"
                @click="removeAuthor(index)"
                class="text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 p-2 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
              </button>
            </div>
            <button
              type="button"
              @click="addAuthor"
              class="inline-flex items-center px-3 py-1 border border-blue-300 dark:border-blue-600 rounded-lg text-sm font-medium text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Add Author
            </button>
            <p v-if="errors.author" class="mt-1 text-sm text-red-600 dark:text-red-400">
              {{ errors.author }}
            </p>
          </div>

          <!-- Date Approved -->
          <div>
            <label for="date_approved" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Date Approved
            </label>
            <input
              id="date_approved"
              v-model="form.date_approved"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
              :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.date_approved }"
            />
            <p v-if="errors.date_approved" class="mt-1 text-sm text-red-600 dark:text-red-400">
              {{ errors.date_approved }}
            </p>
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
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
              placeholder="Enter resolution title"
              :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.title }"
            />
            <p v-if="errors.title" class="mt-1 text-sm text-red-600 dark:text-red-400">
              {{ errors.title }}
            </p>
          </div>

          <!-- Subject and Municipality in one row -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Subject -->
            <div>
              <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Subject
              </label>
              <select
                id="subject"
                v-model="form.subject"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.subject }"
              >
                <option value="">Select subject (optional)</option>
                <!-- Add subject options here if available -->
              </select>
              <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Subject categorization is optional for resolutions.</p>
              <p v-if="errors.subject" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.subject }}
              </p>
            </div>

            <!-- Municipality -->
            <div>
              <label for="municipality" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Municipality
                <span v-if="isMunicipalForm" class="text-red-500">*</span>
              </label>
              <select
                id="municipality"
                v-model="form.municipality"
                :required="isMunicipalForm"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors disabled:bg-gray-100 disabled:dark:bg-gray-800 disabled:text-gray-500 disabled:dark:text-gray-400 disabled:cursor-not-allowed"
                :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': errors.municipality }"
              >
                <option value="">{{ isMunicipalForm ? 'Select municipality' : 'Select municipality (optional)' }}</option>
                <option value="Alicia">Alicia</option>
                <option value="Buug">Buug</option>
                <option value="Diplahan">Diplahan</option>
                <option value="Imelda">Imelda</option>
                <option value="Ipil">Ipil</option>
                <option value="Kabasalan">Kabasalan</option>
                <option value="Mabuhay">Mabuhay</option>
                <option value="Malangas">Malangas</option>
                <option value="Naga">Naga</option>
                <option value="Olutanga">Olutanga</option>
                <option value="Payao">Payao</option>
                <option value="Roseller Lim">Roseller Lim</option>
                <option value="Siay">Siay</option>
                <option value="Talusan">Talusan</option>
                <option value="Titay">Titay</option>
                <option value="Tungawan">Tungawan</option>
              </select>
              <p v-if="!isMunicipalForm" class="mt-2 text-xs text-gray-500 dark:text-gray-400">Municipality selection is optional for Provincial Resolutions</p>
              <p v-if="errors.municipality" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.municipality }}
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
          <div v-if="props.resolution && props.resolution.files && props.resolution.files.length > 0" class="space-y-2">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Existing Files:</h4>
            <div class="space-y-2">
              <div v-for="file in props.resolution.files" :key="file.file_id" class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
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
                      <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatFileSize(file.file_size) }}</p>
                      <p class="text-xs text-gray-400 dark:text-gray-500">Uploaded {{ new Date(file.created_at).toLocaleDateString() }}</p>
                    </div>
                  </div>
                  <div class="flex items-center space-x-2">
                    <a
                      :href="`/serve-file/${file.file_url}`"
                      target="_blank"
                      class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 text-sm font-medium"
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
                      class="w-full h-[500px] border border-gray-300 dark:border-gray-600 rounded"
                      type="application/pdf"
                    ></iframe>
                  </div>
                  <div v-else-if="(getFileTypeFromName(file.file_name) === 'docx' || getFileTypeFromName(file.file_name) === 'doc') && file.public_preview_url" class="bg-gray-100 dark:bg-gray-700 rounded p-2">
                    <iframe
                      :src="getOfficeEmbedUrl(file.public_preview_url)"
                      width="100%"
                      height="600"
                      frameborder="0"
                      class="border border-gray-300 dark:border-gray-600 rounded"
                    ></iframe>
                  </div>
                  <div v-else class="bg-gray-100 dark:bg-gray-700 rounded p-3 max-h-64 overflow-y-auto">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Document preview not available for this file type. <a :href="`/serve-file/${file.file_url}`" target="_blank" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300">Click here to view</a></p>
                  </div>
                </div>
                
                <!-- Marked for Removal -->
                <div v-if="form.remove_files.includes(file.file_id)" class="mt-3 border-t border-red-200 dark:border-red-700 pt-3">
                  <div class="flex items-center space-x-2 text-red-600 dark:text-red-400">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm font-medium">Marked for removal</span>
                  </div>
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

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
          <Link
            href="/resolutions"
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
            {{ processing ? (props.resolution ? 'Updating...' : 'Creating...') : formText.button }}
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
  resolution: {
    type: Object,
    default: null
  }
})

const initialAuthors = (() => {
  if (props.resolution?.author) {
    const authors = props.resolution.author.split(';').map(a => a.trim()).filter(a => a)
    return authors.length > 0 ? authors : ['']
  }
  return ['']
})()

const form = useForm({
  resolution_no: props.resolution?.resolution_no || '',
  authors: initialAuthors,
  author: '', // Add this for backend compatibility
  session: props.resolution?.session || '',
  title: props.resolution?.title || '',
  subject: props.resolution?.subject || '',
  date_approved: props.resolution?.date_approved ? props.resolution.date_approved.split(' ')[0] : '',
  added_by: props.resolution?.added_by || '',
  municipality: props.resolution?.municipality || '',
  is_municipal: props.resolution?.is_municipal ?? page.url.includes('municipal'),
  files: [],
  extracted_text: '',
  file_extracted_texts: [],
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

// Determine if this is for municipal resolutions
const isMunicipalForm = computed(() => page.url.includes('municipal'))

// Dynamic text based on form type
const formText = computed(() => {
  const type = isMunicipalForm.value ? 'Municipal' : 'Provincial'
  return {
    title: props.resolution ? `Edit ${type} Resolution` : `Create New ${type} Resolution`,
    subtitle: props.resolution ? `Update ${type.toLowerCase()} resolution information` : `Add a new ${type.toLowerCase()} resolution to the system`,
    button: props.resolution ? `Update ${type} Resolution` : `Create ${type} Resolution`
  }
})

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

const addAuthor = () => {
  form.authors.push('')
}

const removeAuthor = (index) => {
  if (form.authors.length > 1) {
    form.authors.splice(index, 1)
  }
}

const getOfficeEmbedUrl = (fileUrl) => {
  if (!fileUrl) return ''
  return `https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(fileUrl)}`
}

const submit = () => {
  form.added_by = page.props.auth?.user?.id || 1 // Get current user ID from Inertia page props

  // Prepare extracted text data
  const fileExtractedTexts = selectedFiles.value.map((_, index) => previewObjects.value[index]?.extractedText ?? '')
  const concatenatedExtractedText = fileExtractedTexts.filter(text => text.trim()).join('\n\n')

  // Prepare author data
  const preparedAuthor = form.authors.filter(a => a.trim()).join('; ')

  if (props.resolution) {
    // Update existing resolution
    console.log('Submitting form with data:', form.data())
    console.log('Files marked for removal:', form.remove_files)
    
    // Build a complete custom form data object to ensure arrays are properly handled
    const formData = new FormData();
    
    // Add all standard fields
    Object.entries(form.data()).forEach(([key, value]) => {
      // Skip files, remove_files, extracted_text, and authors - we'll handle those separately
      if (key !== 'files' && key !== 'remove_files' && key !== 'extracted_text' && key !== 'authors') {
        formData.append(key, value);
      }
    });

    // Add prepared author
    formData.append('author', preparedAuthor);

    // Add concatenated extracted_text for main record
    formData.append('extracted_text', concatenatedExtractedText);
    
    // Add files if present
    if (form.files && form.files.length > 0) {
      for (let i = 0; i < form.files.length; i++) {
        formData.append('files[]', form.files[i]);
        // Add per-file extracted text
        formData.append(`file_extracted_texts[${i}]`, fileExtractedTexts[i] || '');
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
    axios.post(`/resolutions/${props.resolution.resolution_id}`, formData)
      .then(response => {
        console.log('Form submitted successfully:', response.data);
        window.location.href = '/resolutions';
      })
      .catch(error => {
        console.error('Form submission error:', error);
      });
  } else {
    // Create new resolution - set extracted_text as concatenated string
    form.extracted_text = concatenatedExtractedText
    form.author = preparedAuthor
    // Add file_extracted_texts to the form
    form.file_extracted_texts = fileExtractedTexts

    form.post('/resolutions', {
      forceFormData: true,
      onSuccess: () => {
        // Reset form on success
        form.reset()
        form.authors = ['']
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
</script>

