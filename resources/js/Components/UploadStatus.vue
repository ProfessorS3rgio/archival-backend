<template>
  <section
    v-if="state !== 'idle'"
    class="fixed bottom-6 right-6 z-50 w-[calc(100%-3rem)] max-w-md rounded-xl border bg-white p-5 shadow-xl dark:bg-gray-800"
    :class="failed ? 'border-red-400' : state === 'success' ? 'border-green-400' : 'border-blue-400'"
    :role="failed ? 'alert' : 'status'"
    aria-live="polite"
    aria-atomic="true"
  >
    <p class="flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
      <span v-if="busy" class="h-4 w-4 animate-spin rounded-full border-2 border-blue-200 border-t-blue-600" aria-hidden="true" />
      {{ title }}
    </p>
    <progress v-if="busy" :value="progress ?? undefined" max="100" class="mt-3 h-2 w-full accent-blue-600" aria-label="File transfer progress" />
    <p v-if="busy && progress !== null" class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ progress }}% transferred</p>
    <p class="mt-2 text-sm text-gray-700 dark:text-gray-200">{{ message }}</p>
    <p v-if="failed" class="mt-2 text-sm text-gray-600 dark:text-gray-300">When ready, use the form's submit button to try again.</p>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  state: { type: String, required: true },
  progress: { type: Number, default: null },
  message: { type: String, default: '' },
})
const failed = computed(() => ['network-error', 'error'].includes(props.state))
const busy = computed(() => ['uploading', 'saving'].includes(props.state))
const title = computed(() => ({
  uploading: 'Uploading…',
  saving: 'Saving document…',
  success: 'Upload complete',
  'network-error': 'Connection interrupted',
  error: 'Upload needs attention',
}[props.state] || 'Document upload'))
</script>
