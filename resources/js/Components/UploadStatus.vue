<template>
  <Transition name="upload-card">
    <section
      v-if="state !== 'idle'"
      class="upload-card fixed bottom-5 right-5 z-50 w-[calc(100%-2.5rem)] max-w-sm overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-[0_16px_64px_-16px_rgba(15,23,42,0.35)] dark:border-slate-700 dark:bg-slate-900 sm:bottom-7 sm:right-7"
      :class="`upload-card--${tone}`"
      :role="failed ? 'alert' : 'status'"
      aria-live="polite"
      aria-atomic="true"
      :aria-busy="busy"
    >
      <div class="upload-card__accent h-1" />
      <div class="p-5">
        <div class="flex items-start gap-3.5">
          <div class="upload-card__icon relative flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl" aria-hidden="true">
            <svg v-if="busy" class="absolute inset-0 h-12 w-12 -rotate-90" viewBox="0 0 48 48" fill="none">
              <rect x="1" y="1" width="46" height="46" rx="15" stroke="currentColor" stroke-opacity="0.15" stroke-width="2" />
              <rect x="1" y="1" width="46" height="46" rx="15" stroke="currentColor" stroke-width="2" pathLength="100" :stroke-dasharray="`${ringProgress} 100`" class="transition-all duration-500" />
            </svg>
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <template v-if="state === 'success'"><path d="m6 12 4 4 8-8" /></template>
              <template v-else-if="state === 'network-error'"><path d="m3 3 18 18M8.5 8.5A10 10 0 0 1 21 9M3 9a10 10 0 0 1 2-1.5M6 13a6 6 0 0 1 3-1.5M13.5 11.5A6 6 0 0 1 18 13M9 17a2 2 0 0 1 3-.5" /><path d="M12 21h.01" /></template>
              <template v-else-if="failed"><path d="M12 8v5m0 4h.01" /><circle cx="12" cy="12" r="9" /></template>
              <template v-else-if="state === 'saving'"><path d="M5 4h12l3 3v13H4V4h1Zm3 0v6h8V4M8 20v-6h8v6" /></template>
              <template v-else><path d="M12 16V4m-4 4 4-4 4 4M4 15v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4" /></template>
            </svg>
          </div>
          <div class="min-w-0 flex-1 pt-0.5">
            <p class="flex items-center gap-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-400 dark:text-slate-500">
              <svg v-if="busy" class="h-3 w-3 animate-spin motion-reduce:animate-none" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" opacity="0.2" /><path d="M12 3a9 9 0 0 1 9 9" stroke="currentColor" stroke-width="3" stroke-linecap="round" /></svg>
              {{ operation === 'merge' ? 'PDF merge' : 'File upload' }}
            </p>
            <h2 class="mt-1 text-base font-semibold tracking-tight text-slate-900 dark:text-white">{{ title }}</h2>
          </div>
          <button v-if="!busy" type="button" @click="$emit('dismiss')" class="-mr-1 rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-500 dark:hover:bg-slate-800 dark:hover:text-white" aria-label="Dismiss upload status">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="m6 6 12 12M18 6 6 18" /></svg>
          </button>
        </div>

        <p class="mt-4 text-sm leading-relaxed text-slate-600 dark:text-slate-300">{{ message }}</p>

        <div v-if="busy" class="mt-5">
          <div class="mb-2 flex items-center justify-between text-xs">
            <span class="font-medium text-slate-500 dark:text-slate-400">{{ state === 'saving' ? 'Finishing up' : operation === 'merge' ? 'Merging PDFs' : 'Transferring files' }}</span>
            <span class="font-semibold tabular-nums text-slate-800 dark:text-slate-100">{{ progress === null ? 'Connecting…' : `${progress}%` }}</span>
          </div>
          <div role="progressbar" :aria-label="operation === 'merge' ? 'PDF merge progress' : 'File transfer progress'" :aria-valuenow="progress ?? undefined" aria-valuemin="0" aria-valuemax="100" class="h-1.5 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
            <div class="upload-card__bar h-full rounded-full transition-[width] duration-500 ease-out" :class="{ 'upload-card__bar--indeterminate': progress === null }" :style="{ width: progress === null ? '35%' : `${progress}%` }" />
          </div>
        </div>

        <div v-if="!failed" class="mt-5 flex items-center gap-2 border-t border-slate-100 pt-4 dark:border-slate-800">
          <template v-for="(step, index) in steps" :key="step">
            <div v-if="index" class="h-px flex-1 bg-slate-200 dark:bg-slate-700" />
            <div class="flex items-center gap-1.5 text-[11px] font-medium" :class="index <= currentStep ? 'text-slate-700 dark:text-slate-200' : 'text-slate-400 dark:text-slate-600'">
              <span class="flex h-4 w-4 items-center justify-center rounded-full text-[9px]" :class="index < currentStep || state === 'success' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400' : index === currentStep ? 'bg-blue-100 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400' : 'bg-slate-100 dark:bg-slate-800'">
                <svg v-if="index < currentStep || state === 'success'" class="h-2.5 w-2.5" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m2 6 2.5 2.5L10 3" /></svg>
                <span v-else>{{ index + 1 }}</span>
              </span>
              {{ step }}
            </div>
          </template>
        </div>
        <div v-else class="mt-4 rounded-xl bg-slate-50 px-3 py-2.5 text-xs leading-relaxed text-slate-500 dark:bg-slate-800/70 dark:text-slate-400">
          Your selection stays here. Close this notice to review the form before trying again.
        </div>
      </div>
    </section>
  </Transition>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  state: { type: String, required: true },
  progress: { type: Number, default: null },
  message: { type: String, default: '' },
  operation: { type: String, default: 'upload' },
})
defineEmits(['dismiss'])
const steps = ['Upload', 'Save', 'Done']
const failed = computed(() => ['network-error', 'error'].includes(props.state))
const busy = computed(() => ['uploading', 'saving'].includes(props.state))
const tone = computed(() => failed.value ? 'error' : props.state === 'success' ? 'success' : 'active')
const currentStep = computed(() => props.state === 'success' ? 2 : props.state === 'saving' ? 1 : 0)
const ringProgress = computed(() => props.state === 'saving' ? 85 : Math.max(8, props.progress ?? 15))
const title = computed(() => props.operation === 'merge' ? ({
  uploading: 'Merging your PDFs',
  saving: 'Finalizing the merged file',
  success: 'PDFs merged successfully',
  'network-error': 'Merge connection interrupted',
  error: 'Let’s fix the PDF merge',
}[props.state] || 'PDF merge') : ({
  uploading: 'Your files are on their way',
  saving: 'Almost there',
  success: 'All saved. You’re ready.',
  'network-error': 'Connection interrupted',
  error: 'Let’s fix this upload',
}[props.state] || 'Document upload'))
</script>

<style scoped>
.upload-card--active { --status: #2563eb; --status-soft: rgb(37 99 235 / 0.09); }
.upload-card--success { --status: #059669; --status-soft: rgb(5 150 105 / 0.09); }
.upload-card--error { --status: #e05252; --status-soft: rgb(224 82 82 / 0.09); }
.upload-card__accent { background: linear-gradient(90deg, var(--status), color-mix(in srgb, var(--status) 40%, transparent)); }
.upload-card__icon { color: var(--status); background: var(--status-soft); }
.upload-card__bar { background: linear-gradient(90deg, #2563eb, #60a5fa); }
.upload-card__bar--indeterminate { animation: upload-indeterminate 1.6s ease-in-out infinite; }
.upload-card-enter-active, .upload-card-leave-active { transition: opacity 200ms ease, transform 250ms ease; }
.upload-card-enter-from, .upload-card-leave-to { opacity: 0; transform: translateY(12px) scale(0.98); }
@keyframes upload-indeterminate { 0% { transform: translateX(-100%); } 100% { transform: translateX(390%); } }
@media (prefers-reduced-motion: reduce) {
  .upload-card-enter-active, .upload-card-leave-active, .upload-card__bar { transition: none; }
  .upload-card__bar--indeterminate { animation: none; }
}
</style>
