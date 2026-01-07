<script setup>
import { ref, computed } from 'vue'
import FloatingFooter from '@/Components/FloatingFooter.vue'
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { format } from 'date-fns'

const props = defineProps({
  logs: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    required: true
  },
  currentFilters: {
    type: Object,
    default: () => ({})
  }
})

const form = useForm({
  action: props.currentFilters.action || '',
  table: props.currentFilters.table || '',
  user_id: props.currentFilters.user_id || '',
  date_from: props.currentFilters.date_from || '',
  date_to: props.currentFilters.date_to || ''
})

const resetFilters = () => {
  form.action = ''
  form.table = ''
  form.user_id = ''
  form.date_from = ''
  form.date_to = ''
  form.get(route('system-logs.index'))
}

const applyFilters = () => {
  form.get(route('system-logs.index'))
}

const formatDate = (date) => {
  if (!date) return ''
  return format(new Date(date), 'yyyy-MM-dd HH:mm:ss')
}

const getActionColor = (action) => {
  const colors = {
    'create': 'bg-green-100 text-green-800 ring-1 ring-green-400',
    'update': 'bg-blue-100 text-blue-800 ring-1 ring-blue-400',
    'delete': 'bg-red-100 text-red-800 ring-1 ring-red-400',
    'view': 'bg-gray-100 text-gray-800 ring-1 ring-gray-400',
    'rollback': 'bg-purple-100 text-purple-800 ring-1 ring-purple-400'
  }
  return colors[action] || 'bg-gray-100 text-gray-800 ring-1 ring-gray-400'
}

const getRecordDisplay = (log) => {
  // Map table names to user-friendly labels
  const tableLabels = {
    'resolution_lists': 'Resolution',
    'ordinance_lists': 'Ordinance',
    'resolution_draft': 'Resolution Draft',
    'ordinance_draft': 'Ordinance Draft',
    'resolution_files': 'Resolution File',
    'ordinance_files': 'Ordinance File',
    'resolution_draft_files': 'Resolution Draft File',
    'ordinance_draft_files': 'Ordinance Draft File',
    'moa_lists': 'Memorandum of Agreement',
    'municipalities_communication_lists': 'Municipality Communication',
    'committee_categories': 'Committee',
    'committee_reports': 'Committee Report',
    'accomplish_lists': 'Accomplishment',
    'project_lists': 'Project',
    'national_offices_list': 'National Office Communication',
    'provincial_offices_list': 'Provincial Office',
    'users': 'User',
    'settings': 'Setting'
  }

  const label = tableLabels[log.target_table] || log.target_table.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
  
  // For draft records, return N/A for the number
  if (log.target_table === 'resolution_draft' || log.target_table === 'ordinance_draft') {
    return {
      type: label,
      number: 'N/A'
    }
  }
  
  // Try to extract record number from old_values or new_values
  const values = log.old_values || log.new_values
  let recordNumber = log.target_id
  
  if (values) {
    // Check for various number fields
    if (values.resolution_no) recordNumber = values.resolution_no
    else if (values.ordinance_no) recordNumber = values.ordinance_no
    else if (values.draft_resolution_no) recordNumber = values.draft_resolution_no
    else if (values.draft_ordinance_no) recordNumber = values.draft_ordinance_no
    else if (values.title) recordNumber = values.title.substring(0, 30) + (values.title.length > 30 ? '...' : '')
  }
  
  return {
    type: label,
    number: recordNumber
  }
}
</script>

<template>
  <AdminLayout title="System Logs">
    <nav class="flex" aria-label="Breadcrumb">
  <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
    <li class="inline-flex items-center">
      <Link href="/dashboard" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
          <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
        </svg>
        Home
      </Link>
    </li>
    <li aria-current="page">
      <div class="flex items-center">
        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
        </svg>
        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">System Logs</span>
      </div>
    </li>
  </ol>
</nav>

    <div class="py-12">
      <div class="max-w-full mx-auto sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold mb-6">System Activity Logs</h2>
        
        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6 border border-gray-100 dark:border-gray-700">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
              </svg>
              Filters
            </h3>
            <div class="text-sm text-gray-500 dark:text-gray-400">
              {{ Object.values(form).filter(v => v !== '').length }} active filter(s)
            </div>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Action Type
              </label>
              <div class="relative">
                <select v-model="form.action" class="w-full pl-10 border-gray-300 dark:border-gray-600 dark:bg-gray-800 focus:ring-blue-500 focus:border-blue-500 rounded-md shadow-sm">
                  <option value="">All Actions</option>
                  <option v-for="action in filters.actions" :key="action" :value="action">
                    {{ action.charAt(0).toUpperCase() + action.slice(1) }}
                  </option>
                </select>
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
            </div>
            
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Table
              </label>
              <div class="relative">
                <select v-model="form.table" class="w-full pl-10 border-gray-300 dark:border-gray-600 dark:bg-gray-800 focus:ring-blue-500 focus:border-blue-500 rounded-md shadow-sm">
                  <option value="">All Tables</option>
                  <option v-for="table in filters.tables" :key="table" :value="table">
                    {{ table }}
                  </option>
                </select>
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 4a3 3 0 00-3 3v6a3 3 0 003 3h10a3 3 0 003-3V7a3 3 0 00-3-3H5zm-1 9v-1h5v2H5a1 1 0 01-1-1zm7 1h4a1 1 0 001-1v-1h-5v2zm0-4h5V8h-5v2zM9 8H4v2h5V8z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
            </div>
            
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Date From
              </label>
              <div class="relative">
                <input type="date" v-model="form.date_from" class="w-full pl-10 border-gray-300 dark:border-gray-600 dark:bg-gray-800 focus:ring-blue-500 focus:border-blue-500 rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
            </div>
            
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Date To
              </label>
              <div class="relative">
                <input type="date" v-model="form.date_to" class="w-full pl-10 border-gray-300 dark:border-gray-600 dark:bg-gray-800 focus:ring-blue-500 focus:border-blue-500 rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
            </div>
          </div>
          
          <div class="mt-6 flex justify-between items-center">
            <button @click="resetFilters" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-md transition-colors duration-150">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              Reset Filters
            </button>
            <button @click="applyFilters" class="inline-flex items-center px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md shadow-sm transition-colors duration-150">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
              Apply Filters
            </button>
          </div>
        </div>
        
        <!-- Logs Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
          <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
              <thead>
                <tr class="bg-gray-100 dark:bg-gray-700 text-left border-b border-gray-200 dark:border-gray-600">
                  <th class="px-8 py-5 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/7">
                    User
                  </th>
                  <th class="px-8 py-5 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/7">
                    Action
                  </th>
                  <th class="px-8 py-5 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/7">
                    Record
                  </th>
                  <th class="px-8 py-5 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/7">
                    Resolution/Ordinance No
                  </th>
                  <th class="px-8 py-5 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/7">
                    Description
                  </th>
                  <th class="px-8 py-5 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/7">
                    Date
                  </th>
                  <th class="px-8 py-5 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/7">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="log in logs.data" :key="log.log_id" class="hover:bg-gray-50/70 dark:hover:bg-gray-700/70 transition-colors duration-150">
                  <td class="px-8 py-5 text-sm text-gray-900 dark:text-gray-100 font-medium">
                    {{ log.user ? log.user.username : 'Unknown' }}
                  </td>
                  <td class="px-8 py-5 text-sm">
                    <span :class="['px-3 py-1.5 rounded-full text-xs font-semibold shadow-sm', getActionColor(log.action)]">
                      {{ log.action }}
                    </span>
                  </td>
                  <td class="px-8 py-5 text-sm text-gray-700 dark:text-gray-300">
                    {{ getRecordDisplay(log).type }}
                  </td>
                  <td class="px-8 py-5 text-sm text-gray-700 dark:text-gray-300">
                    <span class="font-mono">{{ getRecordDisplay(log).number }}</span>
                  </td>
                  <td class="px-8 py-5 text-sm text-gray-700 dark:text-gray-300">
                    {{ log.description || 'No description' }}
                  </td>
                  <td class="px-8 py-5 text-sm text-gray-600 dark:text-gray-300">
                    {{ formatDate(log.created_at) }}
                  </td>
                  <td class="px-8 py-5 text-sm text-right space-x-2">
                    <Link :href="route('system-logs.show', log.log_id)" class="inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 hover:text-blue-800 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 dark:text-blue-300 dark:hover:text-blue-200 rounded-md transition-colors duration-150">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      View
                    </Link>
                    <Link 
                      v-if="log.action === 'delete' && log.old_values" 
                      :href="route('system-logs.rollback', log.log_id)" 
                      method="post" 
                      as="button" 
                      class="inline-flex items-center px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-700 hover:text-green-800 dark:bg-green-900/30 dark:hover:bg-green-900/50 dark:text-green-300 dark:hover:text-green-200 rounded-md transition-colors duration-150">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                      </svg>
                      Rollback
                    </Link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="px-8 py-8 border-t border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
              <div class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 px-4 py-2 rounded-md">
                Showing <span class="font-medium">{{ logs.from || 0 }}</span> to <span class="font-medium">{{ logs.to || 0 }}</span> of <span class="font-medium">{{ logs.total }}</span> results
              </div>
              <div class="flex flex-wrap justify-center gap-1.5">
                <Link
                  v-for="link in logs.links"
                  :key="link.label"
                  :href="link.url || '#'"
                  :class="[
                    'px-3.5 py-2 border rounded-md text-sm font-medium transition-colors duration-150',
                    link.active 
                      ? 'bg-blue-600 text-white border-blue-600 shadow-sm' 
                      : 'bg-white text-gray-700 border-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600',
                    !link.url ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50 dark:hover:bg-gray-600'
                  ]"
                  v-html="link.label"
                ></Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>

  <!-- Floating Developer Footer -->
  <FloatingFooter />
</template>
