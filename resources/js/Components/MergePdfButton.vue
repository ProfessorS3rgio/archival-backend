<template>
  <button
    v-if="props.files.length > 1 && props.files.every(file => file.type === 'application/pdf')"
    type="button"
    class="inline-flex items-center rounded-md border border-blue-300 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-50 disabled:cursor-not-allowed disabled:opacity-60 dark:border-blue-700 dark:text-blue-300 dark:hover:bg-blue-900/20"
    :disabled="props.loading"
    @click="merge"
  >
    Merge PDFs into one file
  </button>
  <UploadStatus
    v-if="props.loading"
    state="uploading"
    :progress="null"
    operation="merge"
    message="Combining your PDFs into one document. Please keep this page open."
  />
</template>

<script setup>
import UploadStatus from '@/Components/UploadStatus.vue'

const props = defineProps({
  files: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
})

const emit = defineEmits(['merge'])
const merge = () => emit('merge')
</script>
