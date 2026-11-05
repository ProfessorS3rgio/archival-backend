<template>
  <Teleport to="body">
    <Transition name="page-loading">
      <div v-if="visible" class="page-loading pointer-events-none fixed inset-0 z-[100]" data-page-loading>
        <div class="page-loading__track absolute inset-x-0 top-0 h-0.5 overflow-hidden bg-blue-100/70 dark:bg-blue-950/70" aria-hidden="true">
          <div class="page-loading__line h-full w-1/3 rounded-full bg-gradient-to-r from-blue-500 via-sky-400 to-blue-600" />
        </div>

        <div class="absolute inset-x-0 top-24 flex justify-center px-4 sm:top-6">
          <div class="flex max-w-full items-center gap-3.5 rounded-2xl border border-white/80 bg-white/95 py-3 pl-3 pr-6 shadow-[0_12px_48px_-12px_rgba(15,23,42,0.25)] backdrop-blur-xl dark:border-slate-700/80 dark:bg-slate-900/95 dark:shadow-black/30" role="status" aria-live="polite" aria-atomic="true">
            <div class="relative flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400" aria-hidden="true">
              <svg class="page-loading__spinner absolute inset-0 h-12 w-12" viewBox="0 0 48 48" fill="none">
                <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-opacity="0.12" stroke-width="2" />
                <path d="M24 4a20 20 0 0 1 20 20" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
              </svg>
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9ZM14 3v6h6M8 13h8M8 17h5" />
              </svg>
            </div>
            <div class="min-w-0">
              <p class="text-sm font-semibold tracking-tight text-slate-900 dark:text-white">Loading page</p>
              <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Getting everything ready&hellip;</p>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { onMounted, onUnmounted, ref } from 'vue'
import { router } from '@inertiajs/vue3'

const visible = ref(false)
let activeVisit = null
let showTimer = null
const removeListeners = []

const finish = () => {
  clearTimeout(showTimer)
  showTimer = null
  activeVisit = null
  visible.value = false
}

onMounted(() => {
  removeListeners.push(
    router.on('start', ({ detail: { visit } }) => {
      // Search, filtering, prefetching, and uploads keep their own feedback.
      if (visit.method !== 'get' || visit.async || visit.prefetch || !visit.showProgress) return
      if (new URL(visit.url, window.location.href).pathname === window.location.pathname) return

      clearTimeout(showTimer)
      activeVisit = visit
      showTimer = setTimeout(() => { visible.value = true }, 160)
    }),
    router.on('finish', ({ detail: { visit } }) => {
      // A cancelled earlier visit must not hide a newer navigation's indicator.
      if (visit === activeVisit) finish()
    }),
  )
})

onUnmounted(() => {
  removeListeners.forEach(remove => remove())
  finish()
})
</script>

<style scoped>
.page-loading__line { animation: page-loading-travel 1.5s ease-in-out infinite; }
.page-loading__spinner { animation: page-loading-spin 1s linear infinite; }
.page-loading-enter-active, .page-loading-leave-active { transition: opacity 180ms ease; }
.page-loading-enter-from, .page-loading-leave-to { opacity: 0; }
@keyframes page-loading-spin { to { transform: rotate(360deg); } }
@keyframes page-loading-travel {
  from { transform: translateX(-100%); }
  to { transform: translateX(400%); }
}
@media (prefers-reduced-motion: reduce) {
  .page-loading__line, .page-loading__spinner { animation: none; }
  .page-loading__line { width: 100%; opacity: 0.65; }
  .page-loading-enter-active, .page-loading-leave-active { transition: none; }
}
</style>
