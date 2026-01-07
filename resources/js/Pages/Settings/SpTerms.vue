<template>
  <AdminLayout title="Manage SP Terms">
    <div class="max-w-6xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Sanggunian Panlalawigan Terms</h1>
          <p class="text-gray-600 dark:text-gray-400">Manage SP terms for the sidebar menu</p>
        </div>
        <button
          @click="showAddModal = true"
          class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          Add New Term
        </button>
      </div>

      <!-- Terms Table -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-900">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Term</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Year Range</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Members</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="term in terms" :key="term.term_id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ term.term_no.toUpperCase() }} SP</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm text-gray-600 dark:text-gray-300">{{ getYearRange(term) }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <Link 
                  :href="`/sp-${term.term_no.toLowerCase()}/officials`"
                  class="text-sm text-blue-600 dark:text-blue-400 hover:underline"
                >
                  View Officials
                </Link>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                <button
                  @click="editTerm(term)"
                  class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300"
                >
                  Edit
                </button>
                <button
                  @click="deleteTerm(term)"
                  class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                  v-if="currentUser.role === 'admin'"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showAddModal || showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full mx-4 p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
          {{ showEditModal ? 'Edit Term' : 'Add New Term' }}
        </h2>

        <form @submit.prevent="showEditModal ? updateTerm() : addTerm()" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Term Number <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.term_no"
              type="text"
              required
              placeholder="e.g., 10th"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Start Year <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.start_year"
                type="date"
                required
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                End Year <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.end_year"
                type="date"
                required
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
          </div>

          <div class="flex justify-end gap-3 pt-4">
            <button
              type="button"
              @click="closeModal()"
              class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg"
            >
              {{ showEditModal ? 'Update' : 'Add' }} Term
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <DeleteConfirmationModal
      :show="showDeleteModal"
      title="Delete SP Term"
      :message="`Are you sure you want to delete ${deletingTerm?.term_no.toUpperCase()} SP? This action cannot be undone.`"
      @confirm="confirmDelete"
      @close="showDeleteModal = false; deletingTerm = null"
    />
  </AdminLayout>

  <!-- Floating Developer Footer -->
  <FloatingFooter />
</template>

<script setup>
import { ref, computed } from 'vue'
import FloatingFooter from '@/Components/FloatingFooter.vue'
import { router } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DeleteConfirmationModal from '@/Components/DeleteConfirmationModal.vue'

const props = defineProps({
  terms: Array,
})

const currentUser = computed(() => usePage().props.auth.user)

const showAddModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const editingTerm = ref(null)
const deletingTerm = ref(null)

const form = ref({
  term_no: '',
  start_year: '',
  end_year: '',
})

const getYearRange = (term) => {
  const startYear = new Date(term.start_year).getFullYear()
  const endYear = new Date(term.end_year).getFullYear()
  return `${startYear}-${endYear}`
}

const addTerm = () => {
  router.post('/sp-terms', form.value, {
    onSuccess: () => {
      closeModal()
    }
  })
}

const editTerm = (term) => {
  editingTerm.value = term
  form.value = {
    term_no: term.term_no,
    start_year: term.start_year,
    end_year: term.end_year,
  }
  showEditModal.value = true
}

const updateTerm = () => {
  router.put(`/sp-terms/${editingTerm.value.term_id}`, form.value, {
    onSuccess: () => {
      closeModal()
    }
  })
}

const deleteTerm = (term) => {
  deletingTerm.value = term
  showDeleteModal.value = true
}

const confirmDelete = () => {
  router.delete(`/sp-terms/${deletingTerm.value.term_id}`, {
    onSuccess: () => {
      showDeleteModal.value = false
      deletingTerm.value = null
    }
  })
}

const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  editingTerm.value = null
  form.value = {
    term_no: '',
    start_year: '',
    end_year: '',
  }
}
</script>

