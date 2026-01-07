<template>
  <AdminLayout title="View Draft Ordinance">
    <DraftOrdinanceDetail
      :record="draft"
      :files="draft.files || []"
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
import DraftOrdinanceDetail from '@/Pages/Show/components/DraftOrdinanceDetail.vue'
import { computed } from 'vue'

const props = defineProps({
  draft: {
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

