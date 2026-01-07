<template>
  <AdminLayout :title="`${term.term_no} SP (${getYearRange(term)}) - Officials`">
    <div class="max-w-7xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ term.term_no }} SP Officials
          </h1>
          <p class="text-gray-600 dark:text-gray-400">
            {{ getYearRange(term) }}
          </p>
        </div>
        <div class="flex gap-3">
          <button 
            @click="showAddModal = true"
            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Official
          </button>
        </div>
      </div>

      <!-- Organizational Chart -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
        <div v-if="members.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No officials yet</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by adding an official.</p>
        </div>

        <!-- Hierarchical Org Chart -->
        <div v-else class="org-chart">
          <!-- Level 1: Vice Governor -->
          <div v-if="getViceGovernor()" class="flex flex-col items-center mb-16">
            <div class="relative group">
              <div class="text-center">
                <div class="relative mx-auto w-40 h-40 mb-4">
                  <img 
                    :src="getProfilePicture(getViceGovernor())" 
                    :alt="getViceGovernor().full_name"
                    class="w-full h-full rounded-full object-cover border-4 border-blue-500 dark:border-blue-400 shadow-xl ring-4 ring-blue-100 dark:ring-blue-900"
                  />
                  <button
                    @click="deleteMember(getViceGovernor().member_id)"
                    class="absolute top-0 right-0 bg-red-600 hover:bg-red-700 text-white rounded-full p-2 opacity-0 group-hover:opacity-100 transition-opacity shadow-lg"
                    title="Delete"
                    v-if="currentUser.role === 'admin'"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                  </button>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/20 px-6 py-3 rounded-lg">
                  <p class="text-lg font-bold text-blue-900 dark:text-blue-300">
                    <span v-if="getViceGovernor().extension">{{ getViceGovernor().extension }}. </span>{{ getViceGovernor().full_name }}
                  </p>
                  <button
                    @click="editMember(getViceGovernor())"
                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 p-1 rounded mt-1"
                    title="Edit"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <p class="text-sm text-blue-700 dark:text-blue-400 font-semibold mt-1">
                    {{ getViceGovernor().position }}
                  </p>
                </div>
              </div>
            </div>
            
            <!-- Connector Line from Vice Governor -->
            <div class="h-12 w-0.5 bg-gradient-to-b from-blue-400 to-gray-300 dark:from-blue-600 dark:to-gray-600"></div>
            
            <!-- Horizontal Divider Line -->
            <div class="relative w-full max-w-5xl">
              <div class="h-0.5 bg-gradient-to-r from-transparent via-gray-300 dark:via-gray-600 to-transparent"></div>
              <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white dark:bg-gray-800 px-4">
                <div class="w-3 h-3 rounded-full bg-blue-400 dark:bg-blue-600"></div>
              </div>
            </div>
          </div>

          <!-- Level 2: Districts -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
            <!-- 1st District -->
            <div v-if="getDistrictMembers('1st District').length > 0" class="relative">
              <!-- Connector Line to District -->
              <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-8 h-8 w-0.5 bg-gray-300 dark:bg-gray-600 hidden lg:block"></div>
              
              <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/10 dark:to-emerald-900/10 rounded-xl p-6 border-2 border-green-200 dark:border-green-800">
                <div class="text-center mb-6">
                  <div class="inline-flex items-center justify-center w-12 h-12 bg-green-500 dark:bg-green-600 rounded-full mb-3">
                    <span class="text-white font-bold text-lg">1</span>
                  </div>
                  <h3 class="text-xl font-bold text-green-900 dark:text-green-300">1st District</h3>
                  <p class="text-sm text-green-700 dark:text-green-400">{{ getDistrictMembers('1st District').length }} Board Members</p>
                </div>
                
                <div class="space-y-4">
                  <div v-for="member in getDistrictMembers('1st District')" :key="member.member_id" class="relative group">
                    <div class="flex items-center gap-4 bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm hover:shadow-md transition-shadow">
                      <div class="relative flex-shrink-0">
                        <img 
                          :src="getProfilePicture(member)" 
                          :alt="member.full_name"
                          class="w-16 h-16 rounded-full object-cover border-2 border-green-300 dark:border-green-700"
                        />
                        <button
                          @click="deleteMember(member.member_id)"
                          class="absolute -top-1 -right-1 bg-red-600 hover:bg-red-700 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity shadow-lg"
                          title="Delete"
                          v-if="currentUser.role === 'admin'"
                        >
                          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                          </svg>
                        </button>
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                          <span v-if="member.extension">{{ member.extension }}. </span>{{ member.full_name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ member.position }}</p>
                      </div>
                      <button
                        @click="editMember(member)"
                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 p-1 rounded opacity-0 group-hover:opacity-100 transition-opacity"
                        title="Edit"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- 2nd District -->
            <div v-if="getDistrictMembers('2nd District').length > 0" class="relative">
              <!-- Connector Line to District -->
              <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-8 h-8 w-0.5 bg-gray-300 dark:bg-gray-600 hidden lg:block"></div>
              
              <div class="bg-gradient-to-br from-purple-50 to-violet-50 dark:from-purple-900/10 dark:to-violet-900/10 rounded-xl p-6 border-2 border-purple-200 dark:border-purple-800">
                <div class="text-center mb-6">
                  <div class="inline-flex items-center justify-center w-12 h-12 bg-purple-500 dark:bg-purple-600 rounded-full mb-3">
                    <span class="text-white font-bold text-lg">2</span>
                  </div>
                  <h3 class="text-xl font-bold text-purple-900 dark:text-purple-300">2nd District</h3>
                  <p class="text-sm text-purple-700 dark:text-purple-400">{{ getDistrictMembers('2nd District').length }} Board Members</p>
                </div>
                
                <div class="space-y-4">
                  <div v-for="member in getDistrictMembers('2nd District')" :key="member.member_id" class="relative group">
                    <div class="flex items-center gap-4 bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm hover:shadow-md transition-shadow">
                      <div class="relative flex-shrink-0">
                        <img 
                          :src="getProfilePicture(member)" 
                          :alt="member.full_name"
                          class="w-16 h-16 rounded-full object-cover border-2 border-purple-300 dark:border-purple-700"
                        />
                        <button
                          @click="deleteMember(member.member_id)"
                          class="absolute -top-1 -right-1 bg-red-600 hover:bg-red-700 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity shadow-lg"
                          title="Delete"
                          v-if="currentUser.role === 'admin'"
                        >
                          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                          </svg>
                        </button>
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                          <span v-if="member.extension">{{ member.extension }}. </span>{{ member.full_name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ member.position }}</p>
                      </div>
                      <button
                        @click="editMember(member)"
                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 p-1 rounded opacity-0 group-hover:opacity-100 transition-opacity"
                        title="Edit"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Ex-Officio -->
            <div v-if="getDistrictMembers('Ex-Officio').length > 0" class="relative">
              <!-- Connector Line to District -->
              <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-8 h-8 w-0.5 bg-gray-300 dark:bg-gray-600 hidden lg:block"></div>
              
              <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/10 dark:to-orange-900/10 rounded-xl p-6 border-2 border-amber-200 dark:border-amber-800">
                <div class="text-center mb-6">
                  <div class="inline-flex items-center justify-center w-12 h-12 bg-amber-500 dark:bg-amber-600 rounded-full mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                  </div>
                  <h3 class="text-xl font-bold text-amber-900 dark:text-amber-300">Ex-Officio</h3>
                  <p class="text-sm text-amber-700 dark:text-amber-400">{{ getDistrictMembers('Ex-Officio').length }} Board Members</p>
                </div>
                
                <div class="space-y-4">
                  <div v-for="member in getDistrictMembers('Ex-Officio')" :key="member.member_id" class="relative group">
                    <div class="flex items-center gap-4 bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm hover:shadow-md transition-shadow">
                      <div class="relative flex-shrink-0">
                        <img 
                          :src="getProfilePicture(member)" 
                          :alt="member.full_name"
                          class="w-16 h-16 rounded-full object-cover border-2 border-amber-300 dark:border-amber-700"
                        />
                        <button
                          @click="deleteMember(member.member_id)"
                          class="absolute -top-1 -right-1 bg-red-600 hover:bg-red-700 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity shadow-lg"
                          title="Delete"
                          v-if="currentUser.role === 'admin'"
                        >
                          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                          </svg>
                        </button>
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                          <span v-if="member.extension">{{ member.extension }}. </span>{{ member.full_name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ member.position }}</p>
                      </div>
                      <button
                        @click="editMember(member)"
                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 p-1 rounded opacity-0 group-hover:opacity-100 transition-opacity"
                        title="Edit"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <!-- Delete Confirmation Modal -->
    <DeleteConfirmationModal
      :show="showDeleteModal"
      title="Delete Official"
      message="Are you sure you want to delete this official? This action cannot be undone."
      @confirm="confirmDelete"
      @close="showDeleteModal = false; deletingMemberId = null"
    />

    <!-- Add Official Modal -->
    <AddOfficialModal
      :show="showAddModal"
      :term="term"
      :termSlug="termSlug"
      :members="members"
      @close="showAddModal = false"
    />

    <!-- Edit Official Modal -->
    <EditOfficialModal
      :show="showEditModal"
      :member="editingMember"
      :term="term"
      :termSlug="termSlug"
      :members="members"
      @close="showEditModal = false; editingMember = null"
    />
  </AdminLayout>

  <!-- Floating Developer Footer -->
  <FloatingFooter />
</template>

<script setup>
import { ref, computed } from 'vue'
import FloatingFooter from '@/Components/FloatingFooter.vue'
import { router } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DeleteConfirmationModal from '@/Components/DeleteConfirmationModal.vue'
import AddOfficialModal from '@/Components/AddOfficialModal.vue'
import EditOfficialModal from '@/Components/EditOfficialModal.vue'

const props = defineProps({
  term: Object,
  members: Array,
  termSlug: String,
})

const currentUser = computed(() => usePage().props.auth.user)

const showDeleteModal = ref(false)
const showAddModal = ref(false)
const showEditModal = ref(false)
const deletingMemberId = ref(null)
const editingMember = ref(null)

const getYearRange = (term) => {
  if (!term.start_year || !term.end_year) return ''
  const startYear = new Date(term.start_year).getFullYear()
  const endYear = new Date(term.end_year).getFullYear()
  return `${startYear}-${endYear}`
}

const getDistrictMembers = (district) => {
  return props.members.filter(member => member.district === district)
}

const getViceGovernor = () => {
  return props.members.find(member => member.position === 'Vice Governor')
}

const getProfilePicture = (member) => {
  if (member.profile_picture && member.profile_picture.file_url) {
    return `/storage/${member.profile_picture.file_url}`
  }
  // Default avatar SVG
  return `data:image/svg+xml,${encodeURIComponent(`
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%239CA3AF">
      <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
    </svg>
  `)}`
}

const deleteMember = (memberId) => {
  deletingMemberId.value = memberId
  showDeleteModal.value = true
}

const editMember = (member) => {
  editingMember.value = member
  showEditModal.value = true
}

const confirmDelete = () => {
  router.delete(`/sp-${props.termSlug}/officials/${deletingMemberId.value}`, {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false
      deletingMemberId.value = null
    }
  })
}
</script>

