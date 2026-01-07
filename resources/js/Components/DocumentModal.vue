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
              @click="$emit('close')"
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
                      <DocsIcon class="w-7 h-7 text-white" />
                    </div>
                  </div>
                  <div class="flex-1 min-w-0 pt-1">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1" id="modal-title">
                      {{ title }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                      {{ subtitle || 'Document Information' }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Content -->
              <div class="p-8">
                <slot name="content">
                  <div v-if="document" class="space-y-6">
                    <!-- Ordinance & Resolution -->
                    <div v-if="type === 'ordinance' || type === 'resolution'" class="space-y-4">
                      <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4">
                          <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Number
                          </label>
                          <p class="text-base font-semibold text-gray-900 dark:text-white">
                            {{ document.ordinance_no || document.resolution_no || 'N/A' }}
                          </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4">
                          <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Author
                          </label>
                          <p class="text-base font-semibold text-gray-900 dark:text-white">
                            {{ document.author || 'N/A' }}
                          </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4">
                          <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Session
                          </label>
                          <p class="text-base font-semibold text-gray-900 dark:text-white">
                            {{ document.session || 'N/A' }}
                          </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4">
                          <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Date Approved
                          </label>
                          <p class="text-base font-semibold text-gray-900 dark:text-white">
                            {{ formatDate(document.date_approved) }}
                          </p>
                        </div>
                      </div>
                      <div v-if="document.subject" class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                          Subject
                        </label>
                        <p class="text-sm text-gray-900 dark:text-white leading-relaxed">
                          {{ document.subject }}
                        </p>
                      </div>
                    </div>

                    <!-- Draft Ordinance & Resolution -->
                    <div v-else-if="type === 'draft-ordinance' || type === 'draft-resolution'" class="space-y-4">
                      <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4">
                          <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Author
                          </label>
                          <p class="text-base font-semibold text-gray-900 dark:text-white">
                            {{ document.author || 'N/A' }}
                          </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4">
                          <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Session
                          </label>
                          <p class="text-base font-semibold text-gray-900 dark:text-white">
                            {{ document.session || 'N/A' }}
                          </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4">
                          <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Date Drafted
                          </label>
                          <p class="text-base font-semibold text-gray-900 dark:text-white">
                            {{ formatDate(document.date_drafted) }}
                          </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4">
                          <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Status
                          </label>
                          <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                            {{ document.status || 'Draft' }}
                          </span>
                        </div>
                      </div>
                      <div v-if="document.subject" class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                          Subject
                        </label>
                        <p class="text-sm text-gray-900 dark:text-white leading-relaxed">
                          {{ document.subject }}
                        </p>
                      </div>
                    </div>

                    <!-- Accomplishment, Project, MOA -->
                    <div v-else-if="['accomplishment', 'project', 'moa'].includes(type)" class="space-y-4">
                      <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                          Description
                        </label>
                        <p class="text-sm text-gray-900 dark:text-white leading-relaxed">
                          {{ document.description || document.subject || 'No description available' }}
                        </p>
                      </div>
                      <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                          Created
                        </label>
                        <p class="text-base font-semibold text-gray-900 dark:text-white">
                          {{ formatDate(document.created_at) }}
                        </p>
                      </div>
                    </div>
                  </div>
                </slot>
              </div>

              <!-- Footer -->
              <div class="border-t border-gray-200 dark:border-gray-700 px-8 py-6 bg-gray-50 dark:bg-gray-800/50">
                <slot name="footer">
                  <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <button
                      type="button"
                      @click="$emit('close')"
                      class="px-6 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
                    >
                      Close
                    </button>
                    <Link
                      v-if="viewUrl"
                      :href="viewUrl"
                      class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-sm hover:shadow-md inline-flex items-center justify-center"
                    >
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      View Full Document
                    </Link>
                  </div>
                </slot>
              </div>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { DocsIcon } from '@/icons'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  document: {
    type: Object,
    default: null
  },
  type: {
    type: String,
    default: ''
  },
  viewUrl: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['close'])

const handleBackdropClick = () => {
  emit('close')
}

const formatDate = (date) => {
  if (!date) return 'Not Available'
  const parsed = new Date(date)
  if (Number.isNaN(parsed.getTime())) return date
  return parsed.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script>
