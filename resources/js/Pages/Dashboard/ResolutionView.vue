<template>
  <AdminLayout title="View Resolution">
    <ResolutionDetail
      :record="resolution"
      :files="resolution.files || []"
      :show-edit-button="true"
      :show-back-button="true"
      :back-url="backUrl"
    />
  </AdminLayout>

  <!-- Floating Developer Footer -->
  <FloatingFooter />
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import FloatingFooter from '@/Components/FloatingFooter.vue'
import ResolutionDetail from '@/Pages/Show/components/ResolutionDetail.vue'
import { computed } from 'vue'

const props = defineProps({
  resolution: {
    type: Object,
    required: true
  }
})

// Get search query from URL parameters
const urlParams = new URLSearchParams(window.location.search)
const searchQuery = urlParams.get('search') || ''

// Create back URL with search query preserved
const backUrl = computed(() => {
  return searchQuery ? `/dashboard?search=${encodeURIComponent(searchQuery)}` : '/dashboard'
})
</script>

