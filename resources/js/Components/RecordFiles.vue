<template>
  <span v-if="!files.length" class="text-sm text-gray-400">No files</span>
  <template v-else>
    <button
      ref="trigger"
      type="button"
      class="inline-flex items-center gap-2 whitespace-nowrap rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-medium text-blue-700 transition hover:bg-blue-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-500 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-300 dark:hover:bg-blue-500/20"
      :aria-expanded="open"
      :aria-controls="panelId"
      :aria-label="`${files.length} ${files.length === 1 ? 'file' : 'files'} for ${recordLabel}`"
      @click="open ? close() : show()"
    >
      <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="m8 12 6-6a3 3 0 0 1 4 4l-8 8a5 5 0 0 1-7-7l8-8M6 14l8-8" />
      </svg>
      {{ files.length }} {{ files.length === 1 ? 'file' : 'files' }}
      <svg class="h-3.5 w-3.5 transition-transform" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m5 7.5 5 5 5-5" /></svg>
    </button>

    <Teleport to="body">
      <section
        v-if="open"
        :id="panelId"
        ref="panel"
        :aria-label="`Files for ${recordLabel}`"
        class="fixed z-[110] overflow-hidden rounded-xl border border-gray-200 bg-white text-left shadow-xl dark:border-gray-700 dark:bg-gray-900"
        :style="position"
      >
        <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-4 py-3 dark:border-gray-800">
          <div class="min-w-0">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Attached files <span class="ml-1 text-xs font-normal text-gray-400">{{ files.length }}</span></h3>
            <p class="truncate text-xs text-gray-500 dark:text-gray-400" :title="recordLabel">{{ recordLabel }}</p>
          </div>
          <button type="button" class="shrink-0 rounded-lg p-2 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800" aria-label="Close files" @click="close(true)">
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m5 5 10 10M15 5 5 15" /></svg>
          </button>
        </div>
        <ul class="overflow-y-auto overscroll-contain p-2" :style="{ maxHeight: listHeight + 'px' }">
          <li v-for="(file, index) in files" :key="file.file_id ?? file.file_url ?? index">
            <a
              :href="`/serve-file/${encodeURIComponent(file.file_url)}`"
              target="_blank"
              rel="noopener noreferrer"
              :title="file.file_name || 'Open file'"
              class="flex min-w-0 items-start gap-3 rounded-lg p-3 text-gray-700 transition hover:bg-blue-50 focus-visible:outline-2 focus-visible:outline-blue-500 dark:text-gray-200 dark:hover:bg-gray-800"
            >
              <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400" aria-hidden="true">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9ZM14 3v6h6M8 13h8M8 17h5" /></svg>
              </span>
              <span class="min-w-0 flex-1">
                <span class="block text-sm leading-5 [overflow-wrap:anywhere]">{{ file.file_name || `File ${index + 1}` }}</span>
                <span class="mt-1 block text-xs text-gray-400">Open in new tab <span aria-hidden="true">↗</span></span>
              </span>
            </a>
          </li>
        </ul>
      </section>
    </Teleport>
  </template>
</template>

<script setup>
import { nextTick, onUnmounted, ref, useId } from 'vue'

defineProps({
  files: { type: Array, default: () => [] },
  recordLabel: { type: String, default: 'this record' },
})

const panelId = `record-files-${useId()}`
const trigger = ref(null)
const panel = ref(null)
const open = ref(false)
const position = ref({ visibility: 'hidden' })
const listHeight = ref(240)

const outside = (event) => {
  if (!panel.value?.contains(event.target) && !trigger.value?.contains(event.target)) close()
}
const keydown = (event) => {
  if (event.key === 'Escape') {
    event.preventDefault()
    event.stopPropagation()
    close(true)
  }
}
const scroll = (event) => {
  if (!panel.value?.contains(event.target)) positionPanel()
}
const resize = () => close()
const removeListeners = () => {
  document.removeEventListener('pointerdown', outside)
  document.removeEventListener('focusin', outside)
  document.removeEventListener('keydown', keydown)
  window.removeEventListener('scroll', scroll, true)
  window.removeEventListener('resize', resize)
}
const close = (restoreFocus = false) => {
  open.value = false
  removeListeners()
  if (restoreFocus) trigger.value?.focus()
}
const positionPanel = async () => {
  if (!open.value || !trigger.value || !panel.value) return
  const rect = trigger.value.getBoundingClientRect()
  if (rect.bottom < 0 || rect.top > window.innerHeight || rect.right < 0 || rect.left > window.innerWidth) {
    close()
    return
  }
  const width = Math.min(360, window.innerWidth - 24)
  const below = window.innerHeight - rect.bottom - 20
  const above = rect.top - 20
  const useAbove = below < 240 && above > below
  listHeight.value = Math.max(48, Math.min(280, (useAbove ? above : below) - 76))
  position.value = { ...position.value, width: `${width}px` }
  await nextTick()
  if (!open.value || !panel.value) return
  const height = panel.value.getBoundingClientRect().height
  position.value = {
    width: `${width}px`,
    left: `${Math.max(12, Math.min(rect.right - width, window.innerWidth - width - 12))}px`,
    top: `${Math.max(12, useAbove ? rect.top - height - 8 : rect.bottom + 8)}px`,
  }
}
const show = async () => {
  position.value = { visibility: 'hidden' }
  open.value = true
  await nextTick()
  await positionPanel()
  if (!open.value || !panel.value) return
  document.addEventListener('pointerdown', outside)
  document.addEventListener('focusin', outside)
  document.addEventListener('keydown', keydown)
  window.addEventListener('scroll', scroll, true)
  window.addEventListener('resize', resize)
  panel.value.querySelector('a')?.focus({ preventScroll: true })
}

onUnmounted(removeListeners)
</script>
