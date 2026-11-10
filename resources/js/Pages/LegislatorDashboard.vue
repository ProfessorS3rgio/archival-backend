<template>
  <AdminLayout title="Legislator Dashboard">
    <div class="space-y-6">
      <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2">
          <li class="text-sm font-medium text-gray-500 dark:text-gray-400">Home</li>
          <li aria-hidden="true" class="text-gray-400">/</li>
          <li class="text-sm font-medium text-gray-700 dark:text-gray-200">Document Search</li>
        </ol>
      </nav>

      <section class="overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-sm dark:border-blue-900/40 dark:bg-white/[0.03]">
        <div class="border-l-4 border-blue-600 bg-blue-50/70 px-5 py-5 dark:bg-blue-900/10 sm:px-7">
          <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
              <p class="text-xs font-bold uppercase tracking-[0.18em] text-blue-600 dark:text-blue-400">Legislator workspace</p>
              <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Search the archives</h1>
              <p class="mt-2 max-w-2xl text-sm text-gray-600 dark:text-gray-400">
                Find ordinances, resolutions, drafts, and other official records quickly.
              </p>
            </div>
            <div class="mt-3 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 sm:mt-0">
              <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2m6-2a10 10 0 1 1-20 0 10 10 0 0 1 20 0Z" />
                </svg>
              </span>
              Search anytime
            </div>
          </div>
        </div>

        <div class="p-5 sm:p-7">
          <label for="legislator-search" class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">What are you looking for?</label>
          <div class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
            <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m2.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
            </svg>
            <input
              id="legislator-search"
              ref="searchInput"
              v-model="searchQuery"
              type="search"
              placeholder="Search by title, author, year, or keyword..."
              class="w-full rounded-xl border border-gray-300 bg-white py-3 pl-12 pr-4 text-gray-900 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
            />
            </div>
            <button
              type="button"
              class="rounded-xl bg-blue-600 px-6 py-3 font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
              @click="searchInput?.focus()"
            >
              Search
            </button>
          </div>
          <div class="mt-3 flex flex-wrap items-center justify-between gap-2 text-sm">
            <span class="text-gray-500 dark:text-gray-400">Tip: use a document number, title word, or author name.</span>
            <span class="font-medium text-blue-600 dark:text-blue-400">{{ resultCount }} {{ resultCount === 1 ? 'match' : 'matches' }}</span>
          </div>
        </div>
      </section>

      <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
        <div class="border-b border-gray-100 pb-5 dark:border-gray-800">
          <div class="mb-4 flex items-center justify-between gap-3">
            <div>
              <div class="flex items-center gap-2">
                <span class="h-5 w-1 rounded-full bg-blue-600"></span>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Refine your results</h2>
              </div>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Use one or more filters to narrow your results.</p>
            </div>
            <button
              v-if="hasFilters"
              type="button"
              class="shrink-0 rounded-lg px-3 py-2 text-sm font-medium text-blue-600 transition hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/20"
              @click="clearSearch"
            >
              Reset
            </button>
          </div>
          <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div>
              <label for="type-filter" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Document type</label>
              <select id="type-filter" v-model="selectedType" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
              <option value="">All document types</option>
              <option v-for="type in documentTypes" :key="type.value" :value="type.value">{{ type.label }}</option>
              </select>
            </div>
            <div>
              <label for="year-filter" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Activity year</label>
              <select id="year-filter" v-model="selectedYear" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                <option value="">All years</option>
                <option v-for="year in availableYears" :key="year" :value="String(year)">{{ year }}</option>
              </select>
            </div>
            <div>
              <label for="author-filter" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Author</label>
              <select id="author-filter" v-model="selectedAuthor" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                <option value="">All authors</option>
                <option v-for="author in availableAuthors" :key="author" :value="author">{{ author }}</option>
              </select>
            </div>
            <div>
              <label for="keyword-filter" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Keyword</label>
              <input id="keyword-filter" v-model="searchQuery" type="search" placeholder="Title or keyword" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
            </div>
          </div>
        </div>

        <div v-if="displayedDocuments.length" class="divide-y divide-gray-100 dark:divide-gray-800">
          <a
            v-for="document in displayedDocuments"
            :key="`${document.type}-${document.id}`"
            :href="getViewUrl(document)"
            class="block py-4 transition first:pt-5 last:pb-0 hover:bg-gray-50 dark:hover:bg-gray-800/50 sm:px-3"
          >
            <div class="flex items-start justify-between gap-4">
              <div class="min-w-0">
                <p class="font-semibold text-gray-900 dark:text-white">{{ document.title || 'Untitled document' }}</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                  {{ getTypeLabel(document.type) }}
                  <span v-if="document.activity_year"> · {{ document.activity_year }}</span>
                  <span v-if="document.author"> · {{ document.author }}</span>
                </p>
              </div>
              <span class="shrink-0 text-sm font-medium text-blue-600 dark:text-blue-400">Open</span>
            </div>
          </a>
        </div>
        <div v-else class="py-12 text-center">
          <p class="font-medium text-gray-700 dark:text-gray-200">No documents found</p>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try a different title, keyword, or document type.</p>
        </div>
        <div v-if="resultCount" class="mt-5 flex items-center justify-between border-t border-gray-100 pt-4 text-sm dark:border-gray-800">
          <p class="text-gray-500 dark:text-gray-400">
            Showing <span class="font-semibold text-gray-700 dark:text-gray-200">{{ displayedDocuments.length }}</span>
            of <span class="font-semibold text-gray-700 dark:text-gray-200">{{ resultCount }}</span> matching documents
          </p>
          <span v-if="resultCount > displayLimit" class="text-xs text-gray-400 dark:text-gray-500">First {{ displayLimit }} shown</span>
        </div>
      </section>

      <section class="rounded-2xl border border-blue-100 bg-blue-50 p-5 dark:border-blue-900/40 dark:bg-blue-900/20 sm:p-6">
        <h2 class="text-lg font-semibold text-blue-950 dark:text-blue-100">How to use document search</h2>
        <ol class="mt-4 grid gap-4 text-sm text-blue-900 dark:text-blue-100 md:grid-cols-3">
          <li><span class="font-bold">1. Search</span><br />Enter a title, author, year, or keyword in the search box.</li>
          <li><span class="font-bold">2. Narrow results</span><br />Choose a document type when you want a more focused list.</li>
          <li><span class="font-bold">3. Open a record</span><br />Select a result to view its complete document details and files.</li>
        </ol>
      </section>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { getTypeLabel, getViewUrl } from '@/composables/useDocumentSearch'
import { useDashboard } from '@/composables/useDashboard'

const props = defineProps({
  allDocuments: {
    type: Object,
    default: () => ({})
  },
  availableYears: {
    type: Array,
    default: () => []
  },
  availableAuthors: {
    type: Array,
    default: () => []
  }
})

const searchInput = ref(null)
const {
  searchQuery,
  selectedType,
  selectedYear,
  selectedAuthor,
  filteredDocuments
} = useDashboard(props)

const documentTypes = [
  { value: 'ordinance', label: 'Ordinances' },
  { value: 'resolution', label: 'Resolutions' },
  { value: 'draft-ordinance', label: 'Draft ordinances' },
  { value: 'draft-resolution', label: 'Draft resolutions' },
  { value: 'accomplishment', label: 'Accomplishments' },
  { value: 'project', label: 'Projects' },
  { value: 'moa', label: 'Memoranda of agreement' },
  { value: 'municipal-communication', label: 'Municipal communications' },
  { value: 'national-office-communication', label: 'National office communications' },
  { value: 'provincial-office', label: 'Provincial offices' },
  { value: 'committee-report', label: 'Committee reports' }
]

const displayLimit = 10
const resultCount = computed(() => filteredDocuments.value.length)
const displayedDocuments = computed(() => filteredDocuments.value.slice(0, displayLimit))
const hasFilters = computed(() => Boolean(searchQuery.value || selectedType.value || selectedYear.value || selectedAuthor.value))

const clearSearch = () => {
  searchQuery.value = ''
  selectedType.value = ''
  selectedYear.value = ''
  selectedAuthor.value = ''
}
</script>
