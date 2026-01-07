<template>
  <slot></slot>
</template>

<script setup lang="ts">
import { ref, provide, onMounted, watch, computed } from 'vue'

type Theme = 'light' | 'dark'

const theme = ref<Theme>('light')
const isInitialized = ref(false)

const isDarkMode = computed(() => theme.value === 'dark')

const toggleTheme = () => {
  theme.value = theme.value === 'light' ? 'dark' : 'light'
  console.log('Theme toggled to:', theme.value)
}

onMounted(() => {
  const savedTheme = localStorage.getItem('theme') as Theme | null
  
  let initialTheme: Theme
  if (savedTheme) {
    initialTheme = savedTheme
  } else {
    // Check system preference
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
    initialTheme = prefersDark ? 'dark' : 'light'
  }

  theme.value = initialTheme
  isInitialized.value = true
  
  // Apply theme immediately on mount
  if (initialTheme === 'dark') {
    document.documentElement.classList.add('dark')
  } else {
    document.documentElement.classList.remove('dark')
  }
})

watch([theme, isInitialized], ([newTheme, newIsInitialized]) => {
  if (newIsInitialized) {
    localStorage.setItem('theme', newTheme)
    if (newTheme === 'dark') {
      document.documentElement.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
    }
  }
})

provide('theme', {
  isDarkMode,
  toggleTheme,
})
</script>

<script lang="ts">
import { inject } from 'vue'

export function useTheme() {
  const theme = inject('theme')
  if (!theme) {
    throw new Error('useTheme must be used within a ThemeProvider')
  }
  return theme
}
</script>
