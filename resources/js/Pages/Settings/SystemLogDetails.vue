<script setup>
import { ref, computed } from 'vue'
import FloatingFooter from '@/Components/FloatingFooter.vue'
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { format } from 'date-fns'

const props = defineProps({
  log: {
    type: Object,
    required: true
  },
  canRollback: {
    type: Boolean,
    default: false
  }
})

const formatDate = (date) => {
  if (!date) return ''
  return format(new Date(date), 'yyyy-MM-dd HH:mm:ss')
}

const getActionColor = (action) => {
  const colors = {
    'create': 'text-green-600 dark:text-green-400',
    'update': 'text-blue-600 dark:text-blue-400',
    'delete': 'text-red-600 dark:text-red-400',
    'view': 'text-gray-600 dark:text-gray-400',
    'rollback': 'text-purple-600 dark:text-purple-400'
  }
  return colors[action] || 'text-gray-600 dark:text-gray-400'
}

const getActionBgColor = (action) => {
  const colors = {
    'create': 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800',
    'update': 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800',
    'delete': 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800',
    'view': 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700',
    'rollback': 'bg-purple-50 dark:bg-purple-900/20 border-purple-200 dark:border-purple-800'
  }
  return colors[action] || 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700'
}

const getActionIcon = (action) => {
  const icons = {
    'create': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
    'update': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />',
    'delete': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />',
    'view': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />',
    'rollback': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />'
  }
  return icons[action] || icons['view']
}

const rollbackForm = useForm({})

const handleRollback = () => {
  if (confirm('Are you sure you want to restore this deleted record?')) {
    rollbackForm.post(route('system-logs.rollback', props.log.log_id))
  }
}

const formatJsonForDisplay = (jsonObj) => {
  if (!jsonObj) return 'No data'
  
  const formatValue = (value) => {
    if (value === null) return 'null'
    if (value === undefined) return 'undefined'
    if (typeof value === 'boolean') return value ? 'Yes' : 'No'
    if (typeof value === 'string' && value.trim() === '') return '(empty)'
    if (typeof value === 'string' && /^\d{4}-\d{2}-\d{2}/.test(value)) {
      // Format dates nicely
      try {
        return format(new Date(value), 'MMM dd, yyyy HH:mm')
      } catch {
        return value
      }
    }
    return String(value)
  }
  
  const entries = Object.entries(jsonObj)
  if (entries.length === 0) return 'No data'
  
  return entries
    .map(([key, value]) => `${key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}: ${formatValue(value)}`)
    .join('\n')
}
</script>

<template>
  <AdminLayout :title="`System Log #${log.log_id}`">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
          <div class="flex items-center gap-3">
            <div :class="`rounded-lg p-2 ${getActionBgColor(log.action)}`">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" :class="getActionColor(log.action)" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-html="getActionIcon(log.action)">
              </svg>
            </div>
            <div>
              <h2 class="text-2xl font-bold flex items-center gap-2">
                Log Details
                <span class="text-sm font-normal bg-gray-100 dark:bg-gray-700 rounded-full px-3 py-1 ml-2">
                  #{{ log.log_id }}
                </span>
              </h2>
              <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ formatDate(log.created_at) }}
              </p>
            </div>
          </div>
          <div class="flex flex-wrap gap-2">
            <Link :href="route('system-logs.index')" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              Back to Logs
            </Link>
            <button 
              v-if="canRollback" 
              @click="handleRollback" 
              class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md shadow-sm transition-colors duration-150"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              Rollback Deletion
            </button>
          </div>
        </div>
        
        <!-- Log summary card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6 border border-gray-100 dark:border-gray-700">
          <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700 mb-6">
            <h3 class="text-lg font-semibold">Log Summary</h3>
            <span :class="`px-3 py-1 rounded-full text-xs font-semibold shadow-sm inline-flex items-center gap-1 ${getActionBgColor(log.action)} ${getActionColor(log.action)}`">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-html="getActionIcon(log.action)">
              </svg>
              {{ log.action.charAt(0).toUpperCase() + log.action.slice(1) }}
            </span>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
              <h4 class="text-xs uppercase text-gray-500 dark:text-gray-400 font-semibold tracking-wider mb-2">User</h4>
              <div class="flex items-center gap-3">
                <div class="h-8 w-8 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <p class="text-lg font-medium">{{ log.user ? log.user.username : 'Unknown' }}</p>
              </div>
            </div>
            
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
              <h4 class="text-xs uppercase text-gray-500 dark:text-gray-400 font-semibold tracking-wider mb-2">Table</h4>
              <div class="flex items-center gap-3">
                <div class="h-8 w-8 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                  </svg>
                </div>
                <p class="text-lg font-medium font-mono">{{ log.target_table }}</p>
              </div>
            </div>
            
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
              <h4 class="text-xs uppercase text-gray-500 dark:text-gray-400 font-semibold tracking-wider mb-2">Record ID</h4>
              <div class="flex items-center gap-3">
                <div class="h-8 w-8 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-full flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                  </svg>
                </div>
                <p class="text-lg font-medium font-mono">{{ log.target_id }}</p>
              </div>
            </div>
            
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
              <h4 class="text-xs uppercase text-gray-500 dark:text-gray-400 font-semibold tracking-wider mb-2">IP Address</h4>
              <div class="flex items-center gap-3">
                <div class="h-8 w-8 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-full flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                </div>
                <p class="text-lg font-medium font-mono">{{ log.ip_address || 'Not recorded' }}</p>
              </div>
            </div>
          </div>
          
          <div class="mb-6">
            <h4 class="text-xs uppercase text-gray-500 dark:text-gray-400 font-semibold tracking-wider mb-2">Description</h4>
            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-700">
              <p>{{ log.description || 'No description provided' }}</p>
            </div>
          </div>
          
          <div class="mb-4">
            <h4 class="text-xs uppercase text-gray-500 dark:text-gray-400 font-semibold tracking-wider mb-2">Browser</h4>
            <p class="text-sm text-gray-600 dark:text-gray-300 truncate">{{ log.user_agent || 'Not recorded' }}</p>
          </div>
        </div>
        
          <!-- Data changes section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
          <div v-if="log.old_values" class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-100 dark:border-gray-700">
            <div class="px-6 py-4 bg-red-50 dark:bg-red-900/10 border-b border-red-100 dark:border-red-900/20">
              <div class="flex items-center justify-between">
                <h3 class="font-medium text-red-800 dark:text-red-300 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Old Values
                </h3>
                <div class="flex items-center gap-2 text-sm">
                  <span class="text-gray-500 dark:text-gray-400">Added by:</span>
                  <span class="font-medium text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-700 px-2.5 py-1 rounded-full">
                    {{ log.user ? log.user.username : 'Unknown' }}
                  </span>
                </div>
              </div>
            </div>
            <div class="p-6">
              <div class="grid grid-cols-2 gap-3">
                <!-- Always show Added By first -->
                <div class="col-span-1 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600">
                  <div class="mb-1.5 flex items-center gap-2">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                      Added By
                    </span>
                    <span class="text-blue-500 dark:text-blue-400">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                    </span>
                  </div>
                  <div>
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                      {{ log.user ? log.user.username : 'Unknown User' }}
                    </span>
                  </div>
                </div>
                <template v-for="(value, key) in log.old_values" :key="key">
                  <div v-if="key !== 'added_by'" class="col-span-1 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600">
                    <div class="mb-1.5 flex items-center gap-2">
                      <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                        {{ key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
                      </span>
                      <span v-if="key === 'ordinance_draft_id'" class="text-purple-500 dark:text-purple-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                      </span>
                      <span v-else-if="key === 'year'" class="text-green-500 dark:text-green-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                      </span>
                      <span v-else-if="key === 'author'" class="text-indigo-500 dark:text-indigo-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                      </span>
                      <span v-else-if="key === 'session'" class="text-orange-500 dark:text-orange-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                      </span>
                      <span v-else-if="key === 'title'" class="text-red-500 dark:text-red-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                      </span>
                      <span v-else-if="key === 'subject'" class="text-cyan-500 dark:text-cyan-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                      </span>
                      <span v-else-if="key === 'date_drafted'" class="text-blue-500 dark:text-blue-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                      </span>
                      <span v-else-if="key === 'created_at' || key === 'updated_at'" class="text-emerald-500 dark:text-emerald-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                      </span>
                      <span v-else-if="key === 'status'" class="text-amber-500 dark:text-amber-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                      </span>
                    </div>
                    <div>
                      <span v-if="value === null" class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 dark:bg-gray-600 text-gray-600 dark:text-gray-300">
                        null
                      </span>
                      <span v-else-if="value === ''" class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 dark:bg-gray-600 text-gray-600 dark:text-gray-300">
                        (empty)
                      </span>
                      <span v-else-if="typeof value === 'boolean'" 
                        :class="[
                          'inline-flex items-center px-2 py-1 rounded text-xs font-medium',
                          value ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'
                        ]"
                      >
                        {{ value ? 'Yes' : 'No' }}
                      </span>
                      <span v-else-if="typeof value === 'string' && /^\d{4}-\d{2}-\d{2}/.test(value)" class="inline-flex items-center gap-1.5 text-sm text-blue-600 dark:text-blue-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ format(new Date(value), 'MMM dd, yyyy HH:mm') }}
                      </span>
                      <span v-else class="text-sm text-gray-900 dark:text-gray-100">
                        {{ value }}
                      </span>
                    </div>
                  </div>
                </template>
              </div>
            </div>
          </div>
          
          <div v-if="log.new_values" class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-100 dark:border-gray-700">
            <div class="px-6 py-4 bg-green-50 dark:bg-green-900/10 border-b border-green-100 dark:border-green-900/20">
              <h3 class="font-medium text-green-800 dark:text-green-300 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                New Values
              </h3>
            </div>
            <div class="p-6">
              <div class="grid grid-cols-2 gap-3">
                <!-- Always show Added By first -->
                <div class="col-span-1 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600">
                  <div class="mb-1.5 flex items-center gap-2">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                      Added By
                    </span>
                    <span class="text-blue-500 dark:text-blue-400">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                    </span>
                  </div>
                  <div>
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                      {{ log.user ? log.user.username : 'Unknown User' }}
                    </span>
                  </div>
                </div>
                <template v-for="(value, key) in log.new_values" :key="key">
                  <div v-if="key !== 'added_by'" class="col-span-1 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600">
                    <div class="mb-1.5 flex items-center gap-2">
                      <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                        {{ key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
                      </span>
                      <span v-if="key === 'added_by'" class="text-blue-500 dark:text-blue-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                      </span>
                      <span v-else-if="key === 'ordinance_draft_id'" class="text-purple-500 dark:text-purple-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                      </span>
                      <span v-else-if="key === 'year'" class="text-green-500 dark:text-green-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                      </span>
                      <span v-else-if="key === 'author'" class="text-indigo-500 dark:text-indigo-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                      </span>
                      <span v-else-if="key === 'session'" class="text-orange-500 dark:text-orange-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                      </span>
                      <span v-else-if="key === 'title'" class="text-red-500 dark:text-red-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                      </span>
                      <span v-else-if="key === 'subject'" class="text-cyan-500 dark:text-cyan-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                      </span>
                      <span v-else-if="key === 'date_drafted'" class="text-blue-500 dark:text-blue-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                      </span>
                      <span v-else-if="key === 'created_at' || key === 'updated_at'" class="text-emerald-500 dark:text-emerald-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                      </span>
                      <span v-else-if="key === 'status'" class="text-amber-500 dark:text-amber-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                      </span>
                    </div>
                    <div>
                      <span v-if="key === 'added_by'" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        {{ log.user ? log.user.username : 'Unknown User' }}
                      </span>
                      <span v-else-if="value === null" class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 dark:bg-gray-600 text-gray-600 dark:text-gray-300">
                        null
                      </span>
                      <span v-else-if="value === ''" class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 dark:bg-gray-600 text-gray-600 dark:text-gray-300">
                        (empty)
                      </span>
                      <span v-else-if="typeof value === 'boolean'" 
                        :class="[
                          'inline-flex items-center px-2 py-1 rounded text-xs font-medium',
                          value ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'
                        ]"
                      >
                        {{ value ? 'Yes' : 'No' }}
                      </span>
                      <span v-else-if="typeof value === 'string' && /^\d{4}-\d{2}-\d{2}/.test(value)" class="inline-flex items-center gap-1.5 text-sm text-blue-600 dark:text-blue-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ format(new Date(value), 'MMM dd, yyyy HH:mm') }}
                      </span>
                      <span v-else class="text-sm text-gray-900 dark:text-gray-100">
                        {{ value }}
                      </span>
                    </div>
                  </div>
                </template>
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
