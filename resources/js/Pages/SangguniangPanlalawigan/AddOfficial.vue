<template>
  <AdminLayout :title="`Add Official - ${term.term_no} SP`">
    <div class="max-w-4xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Add New Official</h1>
          <p class="text-gray-600 dark:text-gray-400">{{ term.term_no }} SP ({{ getYearRange(term) }})</p>
        </div>
        <Link 
          :href="`/sp-${termSlug}/officials`"
          class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          Back to Officials
        </Link>
      </div>

      <!-- Form -->
      <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-6">
        <!-- Profile Picture Upload -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Profile Picture
          </label>
          <div class="flex items-center gap-4">
            <div class="relative w-32 h-32">
              <img 
                :src="previewImage" 
                alt="Profile preview"
                class="w-full h-full rounded-full object-cover border-4 border-gray-200 dark:border-gray-700 shadow-lg"
              />
            </div>
            <div>
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
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
              >
                Choose Photo
              </button>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                JPG, PNG up to 2MB
              </p>
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
            <p v-if="hasViceGovernor" class="mt-2 text-xs text-gray-500 dark:text-gray-400">A Vice Governor already exists for this term â€” the option is disabled.</p>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
          <Link
            :href="`/sp-${termSlug}/officials`"
            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          >
            Cancel
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="form.processing">Adding...</span>
            <span v-else>Add Official</span>
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>

  <!-- Floating Developer Footer -->
  <FloatingFooter />
</template>

  <script setup>
import { ref, watch, computed } from 'vue'
import FloatingFooter from '@/Components/FloatingFooter.vue'
import { useForm } from '@inertiajs/vue3'
import { Link, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  term: Object,
  termSlug: String,
})

const page = usePage()

const form = useForm({
  term_id: props.term.term_id,
  full_name: '',
  extension: '',
  district: '',
  position: '',
  profile_picture: null,
})

// Determine if a Vice Governor already exists (if members are provided via page props)
const hasViceGovernor = computed(() => {
  const members = page.props.members || []
  return members.some(m => m.position === 'Vice Governor')
})

// Compute district member counts
const district1Count = computed(() => {
  const members = page.props.members || []
  return members.filter(m => m.district === '1st District').length
})

const district2Count = computed(() => {
  const members = page.props.members || []
  return members.filter(m => m.district === '2nd District').length
})

// Check if districts are at maximum capacity
const district1Full = computed(() => district1Count.value >= 5)
const district2Full = computed(() => district2Count.value >= 5)

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

const submit = () => {
  form.post(`/sp-${props.termSlug}/officials`, {
    forceFormData: true,
    preserveScroll: true,
  })
}
</script>

