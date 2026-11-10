<template>
  <div v-if="messages.length" role="alert" class="mb-5 rounded-lg border border-red-300 bg-red-50 p-4 text-sm text-red-800 dark:border-red-700 dark:bg-red-900/20 dark:text-red-300">
    <p class="font-semibold">Please check your submission</p>
    <ul class="mt-2 list-inside list-disc space-y-1">
      <li v-for="message in messages" :key="message">{{ message }}</li>
    </ul>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({ errors: { type: Object, default: () => ({}) } })
const messages = computed(() => [...new Set(Object.values(props.errors || {}).flat().filter(message => typeof message === 'string' && message))])
</script>
