<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div class="space-y-5 sm:space-y-6">
      <ComponentCard title="Ordinance Records">
        <BasicTableOne :data="ordinances" :headers="headers" />
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
import BasicTableOne from "@/components/tables/basic-tables/BasicTableOne.vue";

const currentPageTitle = ref("Ordinance");
const ordinances = ref([]);
const headers = ref([
  { key: 'ordinance_no', label: 'Ordinance No' },
  { key: 'author', label: 'Author' },
  { key: 'title', label: 'Title' },
  { key: 'subject', label: 'Subject' },
  { key: 'date_approved', label: 'Date Approved' },
]);

onMounted(async () => {
  try {
    const response = await axios.get('/ordinances');
    ordinances.value = response.data;
  } catch (error) {
    console.error('Error fetching ordinances:', error);
  }
});
</script>