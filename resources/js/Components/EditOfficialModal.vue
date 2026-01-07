<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
        @click="handleBackdropClick"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
      >
        <Transition
          enter-active-class="transition-all duration-300 ease-out"
          enter-from-class="opacity-0 scale-95"
          enter-to-class="opacity-100 scale-100"
          leave-active-class="transition-all duration-200 ease-in"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 scale-95"
        >
          <div
            v-if="show"
            class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden"
            @click.stop
          >
            <!-- Close button -->
            <button
              @click="handleClose"
              class="absolute top-4 right-4 z-10 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors rounded-full p-1 hover:bg-gray-100 dark:hover:bg-gray-800"
              aria-label="Close modal"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>

            <!-- Scrollable content -->
            <div class="overflow-y-auto max-h-[90vh]">
              <!-- Header -->
              <div class="p-8 pb-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-start space-x-4">
                  <div class="flex-shrink-0">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 rounded-2xl flex items-center justify-center shadow-lg">
                      <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </div>
                  </div>
                  <div class="flex-1 min-w-0 pt-1">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1" id="modal-title">
                      Edit Official
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                      {{ term.term_no }} SP ({{ getYearRange(term) }})
                    </p>
                  </div>
                </div>
              </div>

              <!-- Form Content -->
              <form @submit.prevent="submit" class="p-8 space-y-6">
                <!-- Profile Picture Upload - Centered -->
                <div class="flex justify-center">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 text-center">
                      Profile Picture
                    </label>
                    <div class="flex flex-col items-center gap-4">
                      <div class="relative w-32 h-32">
                        <img
                          :src="previewImage"
                          alt="Profile preview"
                          class="w-full h-full rounded-full object-cover border-4 border-gray-200 dark:border-gray-700 shadow-lg"
                        />
                      </div>
                      <div class="text-center">
                        <input
                          type="file"
                          ref="profilePictureInput"
                          @change="handleFileSelect"
                          accept="image/jpeg,image/png,image/jpg"
                          class="hidden"
                        />
                        <button
                          type="button"
                          @click="$refs.profilePictureInput.click()"
                          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm font-medium"
                        >
                          Change Photo
                        </button>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                          JPG, PNG up to 2MB
                        </p>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <!-- Full Name -->
                  <div class="md:col-span-2">
                    <label for="full_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Full Name <span class="text-red-500">*</span>
                    </label>
                    <input
                      id="full_name"
                      v-model="form.full_name"
                      type="text"
                      required
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                      placeholder="Enter full name"
                      :class="{ 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500': form.errors.full_name }"
                    />
                    <p v-if="form.errors.full_name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                      {{ form.errors.full_name }}
                    </p>
                  </div>

                  <!-- Extension -->
                  <div>
                    <label for="extension" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Extension
                    </label>
                    <select
                      id="extension"
                      v-model="form.extension"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    >
                      <option value="">Select extension</option>
                      <option value="Hon">Hon</option>
                      <option value="Atty">Atty</option>
                      <option value="Doc">Doc</option>
                    </select>
                  </div>

                  <!-- District -->
                  <div>
                    <label for="district" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      District
                    </label>
                    <select
                      id="district"
                      v-model="form.district"
                      :disabled="districtDisabled"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
                    >
                      <option value="">Select district</option>
                      <option value="1st District">1st District</option>
                      <option value="2nd District">2nd District</option>
                    </select>
                    <p v-if="!form.position" class="mt-2 text-xs text-gray-500 dark:text-gray-400">Select a position first (district is only required for Board Members).</p>
                  </div>

                  <!-- Position -->
                  <div class="md:col-span-2">
                    <label for="position" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Position
                    </label>
                    <select
                      id="position"
                      v-model="form.position"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    >
                      <option value="">Select position</option>
                      <option value="Vice Governor" :disabled="hasViceGovernor">Vice Governor</option>
                      <option value="Board Member">Board Member</option>
                      <option value="Board Member Ex-Officio">Board Member Ex-Officio</option>
                    </select>
                    <p v-if="hasViceGovernor" class="mt-2 text-xs text-gray-500 dark:text-gray-400">A Vice Governor already exists for this term — the option is disabled.</p>
                  </div>
                </div>
              </form>

              <!-- Footer -->
              <div class="border-t border-gray-200 dark:border-gray-700 px-8 py-6 bg-gray-50 dark:bg-gray-800/50">
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                  <button
                    type="button"
                    @click="handleClose"
                    :disabled="form.processing"
                    class="px-6 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Cancel
                  </button>
                  <button
                    type="button"
                    @click="submit"
                    :disabled="form.processing"
                    class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-sm hover:shadow-md inline-flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span v-if="form.processing">Updating...</span>
                    <span v-else>Update Official</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch, computed, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  show: Boolean,
  member: Object,
  term: Object,
  termSlug: String,
  members: Array,
})

const emit = defineEmits(['close'])

const profilePictureInput = ref(null)
const previewImage = ref('')

const form = useForm({
  term_id: '',
  full_name: '',
  extension: '',
  district: '',
  position: '',
  profile_picture: null,
})

// Determine if a Vice Governor already exists (excluding current member if editing)
const hasViceGovernor = computed(() => {
  if (!props.members) return false
  return props.members.some(m => m.position === 'Vice Governor' && m.member_id !== props.member?.member_id)
})

// District control
const districtDisabled = computed(() => {
  return form.position !== 'Board Member'
})

// Auto-set/clear district when position changes
watch(() => form.position, (val) => {
  if (val === 'Board Member Ex-Officio') {
    form.district = 'Ex-Officio'
  } else if (val === 'Vice Governor') {
    form.district = ''
  } else if (val === 'Board Member') {
    // user will pick district
  } else {
    form.district = ''
  }
})

// Watch for member changes to update form
watch(() => props.member, (newMember) => {
  if (newMember) {
    form.term_id = newMember.term_id || ''
    form.full_name = newMember.full_name || ''
    form.extension = newMember.extension || ''
    form.district = newMember.district || ''
    form.position = newMember.position || ''
    form.profile_picture = null
    form.clearErrors()

    // Set preview image
    if (newMember.profile_picture && newMember.profile_picture.file_url) {
      previewImage.value = `/serve-file/${newMember.profile_picture.file_url}`
    } else {
      previewImage.value = `data:image/svg+xml,${encodeURIComponent(`
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%239CA3AF">
          <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
        </svg>
      `)}`
    }
  }
})

const getYearRange = (term) => {
  if (!term.start_year || !term.end_year) return ''
  const startYear = new Date(term.start_year).getFullYear()
  const endYear = new Date(term.end_year).getFullYear()
  return `${startYear}-${endYear}`
}

const handleFileSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    form.profile_picture = file
    const reader = new FileReader()
    reader.onload = (e) => {
      previewImage.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const handleBackdropClick = () => {
  if (!form.processing) {
    handleClose()
  }
}

const handleClose = () => {
  if (!form.processing) {
    form.clearErrors()
    emit('close')
  }
}

const submit = () => {
  // Ensure all required fields are present
  if (!form.full_name || form.full_name.trim() === '') {
    if (props.member && props.member.full_name) {
      form.full_name = props.member.full_name
    } else {
      form.setError('full_name', 'Full name is required')
      return
    }
  }

  // Ensure other required fields
  if (!form.term_id && props.member) {
    form.term_id = props.member.term_id
  }

  // Use POST with _method spoofing for file uploads (Inertia requirement)
  form.post(`/sp-${props.termSlug}/officials/${props.member.member_id}`, {
    _method: 'put',
    preserveScroll: true,
    onSuccess: () => {
      emit('close')
    }
  })
}

onMounted(() => {
  // Initialize form with member data if available
  if (props.member) {
    form.term_id = props.member.term_id || ''
    form.full_name = props.member.full_name || ''
    form.extension = props.member.extension || ''
    form.district = props.member.district || ''
    form.position = props.member.position || ''
    form.profile_picture = null
    form.clearErrors()

    // Set preview image
    if (props.member.profile_picture && props.member.profile_picture.file_url) {
      previewImage.value = `/serve-file/${props.member.profile_picture.file_url}`
    }
  }

  // Set default preview image if no member or no image
  if (!previewImage.value) {
    previewImage.value = `data:image/svg+xml,${encodeURIComponent(`
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%239CA3AF">
        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
      </svg>
    `)}`
  }
})
</script>