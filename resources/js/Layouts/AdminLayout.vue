<template>
  <div class="min-h-screen xl:flex bg-gray-50 dark:bg-gray-900">
    <AppSidebar />
    <div
      class="flex-1 transition-all duration-300 ease-in-out bg-gray-50 dark:bg-gray-900"
    >
      <AppHeader :title="title" />
      
      <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 transform translate-y-2"
        enter-to-class="opacity-100 transform translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="showSuccessAlert" class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
          <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative dark:bg-green-900/20 dark:border-green-600 dark:text-green-400" role="alert">
            <span class="block sm:inline">{{ successMessage }}</span>
            <button @click="showSuccessAlert = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
              <svg class="fill-current h-6 w-6 text-green-500 dark:text-green-400" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <title>Close</title>
                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
              </svg>
            </button>
          </div>
        </div>
      </Transition>

      <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 transform translate-y-2"
        enter-to-class="opacity-100 transform translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="showErrorAlert" class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
          <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative dark:bg-red-900/20 dark:border-red-600 dark:text-red-400" role="alert">
            <span class="block sm:inline">{{ errorMessage }}</span>
            <button @click="showErrorAlert = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
              <svg class="fill-current h-6 w-6 text-red-500 dark:text-red-400" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <title>Close</title>
                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
              </svg>
            </button>
          </div>
        </div>
      </Transition>

      <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 bg-gray-50 dark:bg-gray-900">
        <slot></slot>
      </div>
      <!-- Footer -->
      <footer class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 pt-0">
        <p class="text-sm text-center text-gray-500 dark:text-gray-400">
          Archival Management System
        </p>
      </footer>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onUnmounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AppHeader from '@/Components/AppHeader.vue'
import AppSidebar from '@/Components/AppSidebar.vue'

defineProps({
  title: {
    type: String,
    default: 'Dashboard'
  }
})

const page = usePage()
const showSuccessAlert = ref(false)
const showErrorAlert = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
let successTimeout = null
let errorTimeout = null

// Watch for flash messages
watch(() => page.props.flash, (flash) => {
  // Clear existing timeouts
  if (successTimeout) clearTimeout(successTimeout)
  if (errorTimeout) clearTimeout(errorTimeout)

  if (flash.success) {
    successMessage.value = flash.success
    showSuccessAlert.value = true
    showErrorAlert.value = false
    
    // Auto-dismiss after 3 seconds
    successTimeout = setTimeout(() => {
      showSuccessAlert.value = false
    }, 3000)
  }
  
  if (flash.error) {
    errorMessage.value = flash.error
    showErrorAlert.value = true
    showSuccessAlert.value = false
    
    // Auto-dismiss after 3 seconds
    errorTimeout = setTimeout(() => {
      showErrorAlert.value = false
    }, 3000)
  }
}, { deep: true, immediate: true })

// Cleanup timeouts on unmount
onUnmounted(() => {
  if (successTimeout) clearTimeout(successTimeout)
  if (errorTimeout) clearTimeout(errorTimeout)
})

</script>