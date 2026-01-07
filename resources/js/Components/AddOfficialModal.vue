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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                      </svg>
                    </div>
                  </div>
                  <div class="flex-1 min-w-0 pt-1">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1" id="modal-title">
                      Add New Official
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
                          Choose Photo
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
                      <option value="1st District" :disabled="district1Full">1st District ({{ district1Count }}/5)</option>
                      <option value="2nd District" :disabled="district2Full">2nd District ({{ district2Count }}/5)</option>
                    </select>
                    <p v-if="!form.position" class="mt-2 text-xs text-gray-500 dark:text-gray-400">Select a position first (district is only required for Board Members).</p>
                    <p v-else-if="form.position === 'Board Member' && district1Full && form.district !== '2nd District'" class="mt-2 text-xs text-amber-600 dark:text-amber-400">1st District has reached the maximum of 5 board members.</p>
                    <p v-else-if="form.position === 'Board Member' && district2Full && form.district !== '1st District'" class="mt-2 text-xs text-amber-600 dark:text-amber-400">2nd District has reached the maximum of 5 board members.</p>
                    <p v-else-if="form.position === 'Board Member' && district1Full && district2Full" class="mt-2 text-xs text-red-600 dark:text-red-400">Both districts have reached the maximum of 5 board members each.</p>
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
                    <span v-if="form.processing">Adding...</span>
                    <span v-else>Add Official</span>
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
import { ref, watch, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  term: {
    type: Object,
    required: true
  },
  termSlug: {
    type: String,
    required: true
  },
  members: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['close'])

const form = useForm({
  term_id: props.term.term_id,
  full_name: '',
  extension: '',
  district: '',
  position: '',
  profile_picture: null,
})

// Compute whether a Vice Governor already exists for this term
const hasViceGovernor = computed(() => {
  return (props.members || []).some(m => m.position === 'Vice Governor')
})

// Compute district member counts
const district1Count = computed(() => {
  return (props.members || []).filter(m => m.district === '1st District').length
})

const district2Count = computed(() => {
  return (props.members || []).filter(m => m.district === '2nd District').length
})

// Check if districts are at maximum capacity
const district1Full = computed(() => district1Count.value >= 5)
const district2Full = computed(() => district2Count.value >= 5)

// District control
const districtDisabled = computed(() => {
  // District enabled only when position is 'Board Member'
  return form.position !== 'Board Member'
})

// Watch position changes to auto-set district for Ex-Officio or clear district for Vice Governor
watch(() => form.position, (val) => {
  if (val === 'Board Member Ex-Officio') {
    form.district = 'Ex-Officio'
  } else if (val === 'Vice Governor') {
    form.district = ''
  } else if (val === 'Board Member') {
    // leave district as-is (user will pick)
  } else {
    form.district = ''
  }
})

const previewImage = ref(getDefaultAvatar())
const profilePictureInput = ref(null)

function getDefaultAvatar() {
  return `data:image/svg+xml,${encodeURIComponent(`
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%239CA3AF">
      <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
    </svg>
  `)}`
}

const getYearRange = (term) => {
  if (!term.start_year || !term.end_year) return ''
  const startYear = new Date(term.start_year).getFullYear()
  const endYear = new Date(term.end_year).getFullYear()
  return `${startYear}-${endYear}`
}

const handleFileSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    // Validate file type
    if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
      alert('Please upload a JPG or PNG image')
      return
    }
    
    // Validate file size (2MB)
    if (file.size > 2 * 1024 * 1024) {
      alert('Image size must be less than 2MB')
      return
    }

    form.profile_picture = file
    
    // Create preview
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
    form.reset()
    previewImage.value = getDefaultAvatar()
    emit('close')
  }
}

const submit = () => {
  form.post(`/sp-${props.termSlug}/officials`, {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      handleClose()
    }
  })
}

// Reset form when modal opens
watch(() => props.show, (newVal) => {
  if (newVal) {
    form.reset()
    form.term_id = props.term.term_id
    previewImage.value = getDefaultAvatar()
  }
})
</script>
