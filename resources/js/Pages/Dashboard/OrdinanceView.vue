<template>
  <AdminLayout title="View Ordinance">
    <OrdinanceDetail
      :record="ordinance"
      :files="ordinance.files || []"
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
import OrdinanceDetail from '@/Pages/Show/components/OrdinanceDetail.vue'
import { computed } from 'vue'

const props = defineProps({
  ordinance: {
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

