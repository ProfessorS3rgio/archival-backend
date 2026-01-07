<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4 mb-6">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <DocsIcon class="h-8 w-8 text-blue-500" />
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Ordinances</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.ordinances }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <DocsIcon class="h-8 w-8 text-green-500" />
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Resolutions</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.resolutions }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <DraftIcon class="h-8 w-8 text-yellow-500" />
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Draft Ordinances</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.draftOrdinances }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <DraftIcon class="h-8 w-8 text-purple-500" />
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Draft Resolutions</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.draftResolutions }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <ComponentCard title="Recent Ordinances">
        <div class="space-y-3">
          <div v-for="ordinance in recentOrdinances" :key="ordinance.ordinance_id" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div>
              <p class="font-medium text-gray-900 dark:text-white">{{ ordinance.title || 'Untitled' }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">No. {{ ordinance.ordinance_no }} - {{ ordinance.author }}</p>
            </div>
            <span class="text-xs text-gray-400">{{ formatDate(ordinance.date_approved) }}</span>
          </div>
          <div v-if="recentOrdinances.length === 0" class="text-center text-gray-500 py-4">
            No ordinances found
          </div>
        </div>
      </ComponentCard>

      <ComponentCard title="Recent Resolutions">
        <div class="space-y-3">
          <div v-for="resolution in recentResolutions" :key="resolution.resolution_id" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div>
              <p class="font-medium text-gray-900 dark:text-white">{{ resolution.title || 'Untitled' }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">No. {{ resolution.resolution_no }} - {{ resolution.author }}</p>
            </div>
            <span class="text-xs text-gray-400">{{ formatDate(resolution.date_approved) }}</span>
          </div>
          <div v-if="recentResolutions.length === 0" class="text-center text-gray-500 py-4">
            No resolutions found
          </div>
        </div>
      </ComponentCard>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import PageBreadcrumb from "@/components/common/PageBreadcrumb.vue";
import AdminLayout from "@/components/layout/AdminLayout.vue";
import ComponentCard from "@/components/common/ComponentCard.vue";
import { DocsIcon, DraftIcon } from "@/icons";

const currentPageTitle = ref("Dashboard");
const stats = ref({
  ordinances: 0,
  resolutions: 0,
  draftOrdinances: 0,
  draftResolutions: 0,
});
const recentOrdinances = ref([]);
const recentResolutions = ref([]);

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString();
};

onMounted(async () => {
  try {
    // Fetch statistics
    const [ordinancesRes, resolutionsRes] = await Promise.all([
      axios.get('/ordinances'),
      axios.get('/resolutions'),
    ]);

    console.log('Ordinances data:', ordinancesRes.data);
    console.log('Resolutions data:', resolutionsRes.data);

    stats.value = {
      ordinances: ordinancesRes.data.length,
      resolutions: resolutionsRes.data.length,
      draftOrdinances: 0, // Will add these later when migrations are ready
      draftResolutions: 0,
    };

    // Get recent items (last 5)
    recentOrdinances.value = ordinancesRes.data.slice(-5).reverse();
    recentResolutions.value = resolutionsRes.data.slice(-5).reverse();
  } catch (error) {
    console.error('Error fetching dashboard data:', error);
  }
});
</script>