<template>
  <Teleport to="body">
    <Transition name="page-loading">
      <div v-if="visible" class="page-loading pointer-events-none fixed inset-0 z-[100] bg-white/80 backdrop-blur-sm dark:bg-slate-950/80" data-page-loading>
        <div class="absolute inset-0 flex items-center justify-center p-4">
          <div class="flex max-w-full flex-col items-center gap-5 text-center" role="status" aria-live="polite" aria-atomic="true">
            <div class="flex h-28 w-28 shrink-0 items-center justify-center" aria-hidden="true">
              <img :src="sealUrl" class="page-loading__spinner h-full w-full object-contain" width="112" height="112" alt="" decoding="async" />
            </div>
            <p class="text-xs font-medium tracking-[0.16em] text-slate-600 dark:text-slate-300">Loading page&hellip;</p>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { onMounted, onUnmounted, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import sealUrl from '@/assets/zamboanga-sibugay-seal.png'

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
.page-loading__spinner { animation: page-loading-spin 4s linear infinite; }
.page-loading-enter-active, .page-loading-leave-active { transition: opacity 180ms ease; }
.page-loading-enter-from, .page-loading-leave-to { opacity: 0; }
@keyframes page-loading-spin { to { transform: rotate(360deg); } }
@media (prefers-reduced-motion: reduce) {
  .page-loading__spinner { animation: none; }
  .page-loading-enter-active, .page-loading-leave-active { transition: none; }
}
</style>
