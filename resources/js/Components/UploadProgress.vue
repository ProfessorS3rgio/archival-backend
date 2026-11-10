<template>
  <div v-if="processing" role="status" aria-live="polite" aria-atomic="true" aria-busy="true"
    class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-blue-800 dark:border-blue-800 dark:bg-blue-950/40 dark:text-blue-200">
    <div class="flex items-center gap-3">
      <svg class="h-5 w-5 shrink-0 animate-spin motion-reduce:animate-none" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" opacity="0.2" />
        <path d="M12 3a9 9 0 0 1 9 9" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
      </svg>
      <div>
        <p class="text-sm font-semibold">{{ saving ? 'Saving your changes...' : progress === null ? 'Preparing your upload...' : `Uploading... ${progress}%` }}</p>
        <p class="mt-1 text-xs">{{ saving ? 'Your file has reached the server. Finishing the save; please keep this page open.' : 'Please keep this page open until the upload finishes.' }}</p>
      </div>
    </div>
    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-blue-100 dark:bg-blue-900"
      role="progressbar" :aria-label="saving ? 'Saving changes' : 'Upload progress'" :aria-valuenow="saving ? undefined : progress ?? undefined" aria-valuemin="0" aria-valuemax="100">
      <div class="h-full rounded-full bg-blue-600 transition-[width] duration-200"
        :class="{ 'animate-pulse motion-reduce:animate-none': saving || progress === null }" :style="{ width: saving || progress === null ? '100%' : `${progress}%` }" />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
const props = defineProps({
  processing: { type: Boolean, default: false },
  progress: { type: Number, default: null },
})
const saving = computed(() => props.progress !== null && props.progress >= 100)
</script>
