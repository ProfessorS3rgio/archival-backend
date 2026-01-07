<template>
  <AdminLayout title="Dashboard">
    <nav class="flex" aria-label="Breadcrumb">
  <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
    <li class="inline-flex items-center">
      <a href="#" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
          <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
        </svg>
        Home
      </a>
    </li>
    <li aria-current="page">
      <div class="flex items-center">
        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
        </svg>
        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Dashboard</span>
      </div>
    </li>
  </ol>
</nav>

    <div class="space-y-6">
      <!-- Welcome Section -->
      <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Welcome back!</h1>
        <p class="text-gray-600 dark:text-gray-400">Here's what's happening with your archival documents today.</p>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Ordinances -->
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] hover:shadow-lg transition-shadow duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total Ordinances</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.ordinances }}</p>
              <p class="text-xs text-green-600 dark:text-green-400 mt-1 flex items-center">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                +12% from last month
              </p>
            </div>
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                <DocsIcon class="w-5 h-5 text-blue-600 dark:text-blue-400" />
              </div>
            </div>
          </div>
        </div>

        <!-- Total Resolutions -->
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] hover:shadow-lg transition-shadow duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total Resolutions</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.resolutions }}</p>
              <p class="text-xs text-green-600 dark:text-green-400 mt-1 flex items-center">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                +8% from last month
              </p>
            </div>
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                <DocsIcon class="w-5 h-5 text-green-600 dark:text-green-400" />
              </div>
            </div>
          </div>
        </div>

        <!-- Draft Ordinances -->
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] hover:shadow-lg transition-shadow duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Draft Ordinances</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.draftOrdinances }}</p>
              <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1 flex items-center">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                </svg>
                Pending review
              </p>
            </div>
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center">
                <DraftIcon class="w-5 h-5 text-yellow-600 dark:text-yellow-400" />
              </div>
            </div>
          </div>
        </div>

        <!-- Draft Resolutions -->
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] hover:shadow-lg transition-shadow duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Draft Resolutions</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.draftResolutions }}</p>
              <p class="text-xs text-purple-600 dark:text-purple-400 mt-1 flex items-center">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                </svg>
                In progress
              </p>
            </div>
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-purple-50 dark:bg-purple-900/20 rounded-xl flex items-center justify-center">
                <DraftIcon class="w-5 h-5 text-purple-600 dark:text-purple-400" />
              </div>
            </div>
          </div>
        </div>

        <!-- Accomplishments -->
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] hover:shadow-lg transition-shadow duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Accomplishments</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.accomplishments }}</p>
              <p class="text-xs text-indigo-600 dark:text-indigo-400 mt-1 flex items-center">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Completed
              </p>
            </div>
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Projects -->
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] hover:shadow-lg transition-shadow duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Projects</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.projects }}</p>
              <p class="text-xs text-teal-600 dark:text-teal-400 mt-1 flex items-center">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Active
              </p>
            </div>
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-teal-50 dark:bg-teal-900/20 rounded-xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- MOAs -->
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] hover:shadow-lg transition-shadow duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">MOAs</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.moas }}</p>
              <p class="text-xs text-pink-600 dark:text-pink-400 mt-1 flex items-center">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                </svg>
                Signed
              </p>
            </div>
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-pink-50 dark:bg-pink-900/20 rounded-xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-pink-600 dark:text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Municipal Communications -->
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] hover:shadow-lg transition-shadow duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Municipal Communications</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.municipalCommunications }}</p>
              <p class="text-xs text-orange-600 dark:text-orange-400 mt-1 flex items-center">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                Active
              </p>
            </div>
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-orange-50 dark:bg-orange-900/20 rounded-xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- National Offices -->
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] hover:shadow-lg transition-shadow duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">National Offices</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.nationalOffices }}</p>
              <p class="text-xs text-cyan-600 dark:text-cyan-400 mt-1 flex items-center">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                </svg>
                Listed
              </p>
            </div>
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-cyan-50 dark:bg-cyan-900/20 rounded-xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-cyan-600 dark:text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Provincial Offices -->
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] hover:shadow-lg transition-shadow duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Provincial Offices</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.provincialOffices }}</p>
              <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-1 flex items-center">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Operational
              </p>
            </div>
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Committee Reports -->
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] hover:shadow-lg transition-shadow duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Committee Reports</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.committeeReports }}</p>
              <p class="text-xs text-violet-600 dark:text-violet-400 mt-1 flex items-center">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                </svg>
                Documented
              </p>
            </div>
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-violet-50 dark:bg-violet-900/20 rounded-xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Staff -->
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] hover:shadow-lg transition-shadow duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total Staff</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.totalStaff }}</p>
              <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 flex items-center">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                </svg>
                Active Users
              </p>
            </div>
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-slate-50 dark:bg-slate-900/20 rounded-xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- File Statistics -->
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] hover:shadow-lg transition-shadow duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total Files</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.totalFiles }}</p>
              <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">Total Size: {{ formatFileSize(stats.totalFileSize) }}</p>
            </div>
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Document Activity and Quick Actions -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 flex-1 min-h-0">
        <!-- Document Activity - Full List with Search -->
        <div class="lg:col-span-2">
          <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] flex flex-col min-h-full">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
              <div class="flex flex-col gap-4 sm:flex-row sm:justify-between sm:items-center">
                <div>
                  <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Document Activity</h3>
                  <p class="mt-1 text-gray-500 text-sm dark:text-gray-400">
                    Search and browse all documents in the system
                  </p>
                </div>
              </div>

              <!-- Search and Filter -->
              <div class="mt-4 flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                      </svg>
                    </div>
                    <input
                      v-model="searchQuery"
                      type="text"
                      placeholder="Search documents by title..."
                      class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                  </div>
                </div>
                <div class="sm:w-48">
                  <select
                    v-model="selectedType"
                    class="block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="">All Types</option>
                    <option value="ordinance">Ordinances</option>
                    <option value="resolution">Resolutions</option>
                    <option value="draft-ordinance">Draft Ordinances</option>
                    <option value="draft-resolution">Draft Resolutions</option>
                    <option value="accomplishment">Accomplishments</option>
                    <option value="project">Projects</option>
                    <option value="moa">MOAs</option>
                    <option value="municipal-communication">Municipal Communications</option>
                    <option value="national-office">National Offices</option>
                    <option value="provincial-office">Provincial Offices</option>
                    <option value="committee-report">Committee Reports</option>
                  </select>
                </div>
                <div class="sm:w-40">
                  <select
                    v-model="selectedYear"
                    class="block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="">All Years</option>
                    <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
                  </select>
                </div>
                <div class="sm:w-56">
                  <select
                    v-model="selectedAuthor"
                    class="block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="">All Authors</option>
                    <option v-for="author in availableAuthors" :key="author" :value="author">{{ author }}</option>
                  </select>
                </div>
              </div>
            </div>
            <div
              v-if="searchQuery || selectedType || selectedYear || selectedAuthor"
              class="px-6 pb-4 text-sm text-gray-600 dark:text-gray-400 flex items-center justify-between"
            >
              <span>
                Found
                <span class="font-semibold text-gray-900 dark:text-white">
                  {{ filteredDocuments.length }}
                </span>
                {{ filteredDocuments.length === 1 ? 'document' : 'documents' }} matching your filters.
              </span>
              <button
                type="button"
                class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                @click="() => { searchQuery = ''; selectedType = ''; selectedYear = ''; selectedAuthor = '' }"
              >
                Clear
              </button>
            </div>

            <!-- Document List -->
            <div class="flex-1 overflow-y-auto max-h-[750px]">
              <div v-if="filteredDocuments.length === 0" class="p-6 text-center text-gray-500 dark:text-gray-400">
                No documents found matching your search.
              </div>
              <div v-else class="divide-y divide-gray-100 dark:divide-gray-800">
                <div
                  v-for="document in filteredDocuments.slice(0, 12)"
                  :key="`${document.type}-${document.id}`"
                  class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors"
                >
                  <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                      <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                          <div :class="getTypeIconClass(document.type)" class="w-8 h-8 rounded-lg flex items-center justify-center">
                            <component :is="getTypeIcon(document.type)" class="w-4 h-4" />
                          </div>
                        </div>
                        <div class="min-w-0 flex-1">
                          <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            {{ document.title }}
                          </p>
                          <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ getTypeLabel(document.type) }} • {{ formatDate(document.created_at) }}
                          </p>
                        </div>
                      </div>
                    </div>
                    <div class="flex-shrink-0 ml-4">
                      <Link
                        :href="getViewUrl(document, searchQuery)"
                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors"
                      >
                        View
                      </Link>
                    </div>
                  </div>
                </div>
                <!-- Show more indicator when there are more than 12 documents -->
                <div v-if="filteredDocuments.length > 12" class="px-6 py-3 bg-gray-50 dark:bg-gray-800/50 text-center border-t border-gray-100 dark:border-gray-700">
                  <p class="text-sm text-gray-500 dark:text-gray-400">
                    Showing 12 of {{ filteredDocuments.length }} documents. 
                    <span class="text-blue-600 dark:text-blue-400">Use filters to narrow results.</span>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="space-y-6">
          <!-- Quick Actions Card -->
          <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">Quick Actions</h3>
            <div class="space-y-3">
              <Link href="/ordinances/create" class="flex items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors group">
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center mr-3">
                  <PlusIcon class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                </div>
                <span class="text-sm font-medium text-blue-900 dark:text-blue-100">New Ordinance</span>
              </Link>
              <Link href="/resolutions/create" class="flex items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors group">
                <div class="w-8 h-8 bg-green-100 dark:bg-green-800 rounded-lg flex items-center justify-center mr-3">
                  <PlusIcon class="w-4 h-4 text-green-600 dark:text-green-400" />
                </div>
                <span class="text-sm font-medium text-green-900 dark:text-green-100">New Resolution</span>
              </Link>
              <Link href="/draft-ordinances/create" class="flex items-center p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors group">
                <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-800 rounded-lg flex items-center justify-center mr-3">
                  <DraftIcon class="w-4 h-4 text-yellow-600 dark:text-yellow-400" />
                </div>
                <span class="text-sm font-medium text-yellow-900 dark:text-yellow-100">New Ordinance Draft</span>
              </Link>
              <Link href="/draft-resolutions/create" class="flex items-center p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors group">
                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-800 rounded-lg flex items-center justify-center mr-3">
                  <DraftIcon class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                </div>
                <span class="text-sm font-medium text-purple-900 dark:text-purple-100">New Resolution Draft</span>
              </Link>
              <Link href="/accomplishments/create" class="flex items-center p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-colors group">
                <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-800 rounded-lg flex items-center justify-center mr-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <span class="text-sm font-medium text-indigo-900 dark:text-indigo-100">New Accomplishment</span>
              </Link>
              <Link href="/projects/create" class="flex items-center p-3 bg-teal-50 dark:bg-teal-900/20 rounded-lg hover:bg-teal-100 dark:hover:bg-teal-900/30 transition-colors group">
                <div class="w-8 h-8 bg-teal-100 dark:bg-teal-800 rounded-lg flex items-center justify-center mr-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                  </svg>
                </div>
                <span class="text-sm font-medium text-teal-900 dark:text-teal-100">New Project</span>
              </Link>
              <Link href="/memorandum-of-agreements/create" class="flex items-center p-3 bg-pink-50 dark:bg-pink-900/20 rounded-lg hover:bg-pink-100 dark:hover:bg-pink-900/30 transition-colors group">
                <div class="w-8 h-8 bg-pink-100 dark:bg-pink-800 rounded-lg flex items-center justify-center mr-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-pink-600 dark:text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
                <span class="text-sm font-medium text-pink-900 dark:text-pink-100">New MOA</span>
              </Link>
              <Link href="/municipal-communications/create" class="flex items-center p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors group">
                <div class="w-8 h-8 bg-orange-100 dark:bg-orange-800 rounded-lg flex items-center justify-center mr-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                  </svg>
                </div>
                <span class="text-sm font-medium text-orange-900 dark:text-orange-100">New Municipal Communication</span>
              </Link>
              <Link href="/national-office-communications/create" class="flex items-center p-3 bg-cyan-50 dark:bg-cyan-900/20 rounded-lg hover:bg-cyan-100 dark:hover:bg-cyan-900/30 transition-colors group">
                <div class="w-8 h-8 bg-cyan-100 dark:bg-cyan-800 rounded-lg flex items-center justify-center mr-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-cyan-600 dark:text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                </div>
                <span class="text-sm font-medium text-cyan-900 dark:text-cyan-100">New National Office</span>
              </Link>
              <Link href="/provincial-offices/create" class="flex items-center p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-colors group">
                <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-800 rounded-lg flex items-center justify-center mr-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
                <span class="text-sm font-medium text-emerald-900 dark:text-emerald-100">New Provincial Office</span>
              </Link>
              <Link href="/committee-reports/create" class="flex items-center p-3 bg-violet-50 dark:bg-violet-900/20 rounded-lg hover:bg-violet-100 dark:hover:bg-violet-900/30 transition-colors group">
                <div class="w-8 h-8 bg-violet-100 dark:bg-violet-800 rounded-lg flex items-center justify-center mr-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
                <span class="text-sm font-medium text-violet-900 dark:text-violet-100">New Committee Report</span>
              </Link>
              <Link href="/system-logs" class="flex items-center p-3 bg-gray-50 dark:bg-gray-900/20 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-900/30 transition-colors group">
                <div class="w-8 h-8 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center mr-3">
                  <InfoCircleIcon class="w-4 h-4 text-gray-600 dark:text-gray-400" />
                </div>
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">System Logs</span>
              </Link>
            </div>
          </div>


        </div>
      </div>

      <!-- Recent Documents with Tabs -->
      <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Recent Documents</h3>
            <div class="flex space-x-1 bg-gray-100 dark:bg-gray-800 p-1 rounded-lg">
              <button
                @click="activeTab = 'ordinances'"
                :class="[
                  'px-4 py-2 text-sm font-medium rounded-md transition-colors',
                  activeTab === 'ordinances'
                    ? 'bg-white dark:bg-gray-700 text-blue-600 dark:text-blue-400 shadow-sm'
                    : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
                ]"
              >
                Ordinances
              </button>
              <button
                @click="activeTab = 'resolutions'"
                :class="[
                  'px-4 py-2 text-sm font-medium rounded-md transition-colors',
                  activeTab === 'resolutions'
                    ? 'bg-white dark:bg-gray-700 text-green-600 dark:text-green-400 shadow-sm'
                    : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
                ]"
              >
                Resolutions
              </button>
              <button
                @click="activeTab = 'draft-ordinances'"
                :class="[
                  'px-4 py-2 text-sm font-medium rounded-md transition-colors',
                  activeTab === 'draft-ordinances'
                    ? 'bg-white dark:bg-gray-700 text-yellow-600 dark:text-yellow-400 shadow-sm'
                    : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
                ]"
              >
                Ordinance Drafts
              </button>
              <button
                @click="activeTab = 'draft-resolutions'"
                :class="[
                  'px-4 py-2 text-sm font-medium rounded-md transition-colors',
                  activeTab === 'draft-resolutions'
                    ? 'bg-white dark:bg-gray-700 text-purple-600 dark:text-purple-400 shadow-sm'
                    : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
                ]"
              >
                Resolution Drafts
              </button>
              <button
                @click="activeTab = 'accomplishments'"
                :class="[
                  'px-4 py-2 text-sm font-medium rounded-md transition-colors',
                  activeTab === 'accomplishments'
                    ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow-sm'
                    : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
                ]"
              >
                Accomplishments
              </button>
              <button
                @click="activeTab = 'projects'"
                :class="[
                  'px-4 py-2 text-sm font-medium rounded-md transition-colors',
                  activeTab === 'projects'
                    ? 'bg-white dark:bg-gray-700 text-teal-600 dark:text-teal-400 shadow-sm'
                    : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
                ]"
              >
                Projects
              </button>
              <button
                @click="activeTab = 'moas'"
                :class="[
                  'px-4 py-2 text-sm font-medium rounded-md transition-colors',
                  activeTab === 'moas'
                    ? 'bg-white dark:bg-gray-700 text-pink-600 dark:text-pink-400 shadow-sm'
                    : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
                ]"
              >
                MOAs
              </button>
              <button
                @click="activeTab = 'committee-reports'"
                :class="[
                  'px-4 py-2 text-sm font-medium rounded-md transition-colors',
                  activeTab === 'committee-reports'
                    ? 'bg-white dark:bg-gray-700 text-violet-600 dark:text-violet-400 shadow-sm'
                    : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
                ]"
              >
                Committee Reports
              </button>
              <button
                @click="activeTab = 'systemlogs'"
                :class="[
                  'px-4 py-2 text-sm font-medium rounded-md transition-colors',
                  activeTab === 'systemlogs'
                    ? 'bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-400 shadow-sm'
                    : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
                ]"
              >
                System Logs
              </button>
            </div>
            <Link 
              :href="activeTab === 'ordinances' 
                ? '/ordinances' 
                : activeTab === 'resolutions' 
                ? '/resolutions' 
                : activeTab === 'draft-ordinances'
                ? '/draft-ordinances'
                : activeTab === 'draft-resolutions'
                ? '/draft-resolutions'
                : activeTab === 'accomplishments'
                ? '/accomplishments'
                : activeTab === 'projects'
                ? '/projects'
                : activeTab === 'moas'
                ? '/moas'
                : activeTab === 'municipal-communications'
                ? '/municipal-communications'
                : activeTab === 'national-offices'
                ? '/national-offices'
                : activeTab === 'provincial-offices'
                ? '/provincial-offices'
                : activeTab === 'committee-reports'
                ? '/committee-reports'
                : '/system-logs'" 
              class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium"
            >
              View all  
            </Link>
          </div>
        </div>
        <div class="p-6">
          <!-- Ordinances Tab Content -->
          <div v-show="activeTab === 'ordinances'" class="space-y-4">
            <div v-for="ordinance in recentOrdinances.slice(0, 5)" :key="ordinance.ordinance_id" class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                  <DocsIcon class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ ordinance.title || 'Untitled Ordinance' }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">No. {{ ordinance.ordinance_no }}</p>
                <div v-if="ordinance.author" class="text-xs text-gray-500 dark:text-gray-400">
                  <div v-for="author in ordinance.author.split(';')" :key="author" class="trim">{{ author.trim() }}</div>
                </div>
                <p class="text-xs text-gray-400 mt-1">{{ formatDate(ordinance.date_approved) }}</p>
              </div>
              <div class="flex-shrink-0 ml-2">
                <button @click="openDocumentModal(ordinance, 'ordinance')" class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 dark:hover:bg-blue-900/50 rounded-lg transition-colors group">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600 dark:text-blue-400 group-hover:text-blue-700 dark:group-hover:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
              </div>
            </div>
            <div v-if="recentOrdinances.length === 0" class="text-center py-8">
              <DocsIcon class="w-12 h-12 text-gray-300 mx-auto mb-3" />
              <p class="text-gray-500 dark:text-gray-400">No ordinances found</p>
              <Link href="/ordinances/create" class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 mt-2 inline-block">
                Create your first ordinance  
              </Link>
            </div>
          </div>

          <!-- Resolutions Tab Content -->
          <div v-show="activeTab === 'resolutions'" class="space-y-4">
            <div v-for="resolution in recentResolutions.slice(0, 5)" :key="resolution.resolution_id" class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                  <DocsIcon class="w-4 h-4 text-green-600 dark:text-green-400" />
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ resolution.title || 'Untitled Resolution' }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">No. {{ resolution.resolution_no }}</p>
                <div v-if="resolution.author" class="text-xs text-gray-500 dark:text-gray-400">
                  <div v-for="author in resolution.author.split(';')" :key="author" class="trim">{{ author.trim() }}</div>
                </div>
                <p class="text-xs text-gray-400 mt-1">{{ formatDate(resolution.date_approved) }}</p>
              </div>
              <div class="flex-shrink-0 ml-2">
                <button @click="openDocumentModal(resolution, 'resolution')" class="inline-flex items-center justify-center w-8 h-8 bg-green-100 dark:bg-green-900/30 hover:bg-green-200 dark:hover:bg-green-900/50 rounded-lg transition-colors group">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-600 dark:text-green-400 group-hover:text-green-700 dark:group-hover:text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
              </div>
            </div>
            <div v-if="recentResolutions.length === 0" class="text-center py-8">
              <DocsIcon class="w-12 h-12 text-gray-300 mx-auto mb-3" />
              <p class="text-gray-500 dark:text-gray-400">No resolutions found</p>
              <Link href="/resolutions/create" class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 mt-2 inline-block">
                Create your first resolution  
              </Link>
            </div>
          </div>

          <!-- Ordinance Drafts Tab Content -->
          <div v-show="activeTab === 'draft-ordinances'" class="space-y-4">
            <div v-for="draft in recentOrdinanceDrafts.slice(0, 5)" :key="draft.ordinance_draft_id" class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                  <DraftIcon class="w-4 h-4 text-yellow-600 dark:text-yellow-400" />
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ draft.title || 'Untitled Ordinance Draft' }}</p>
                <div v-if="draft.author" class="text-xs text-gray-500 dark:text-gray-400">
                  <div v-for="author in draft.author.split(';')" :key="author" class="trim">{{ author.trim() }}</div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ draft.session }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ formatDate(draft.date_drafted) }}</p>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 mt-1">
                  {{ draft.status || 'Draft' }}
                </span>
              </div>
              <div class="flex-shrink-0 ml-2">
                <button @click="openDocumentModal(draft, 'draft-ordinance')" class="inline-flex items-center justify-center w-8 h-8 bg-yellow-100 dark:bg-yellow-900/30 hover:bg-yellow-200 dark:hover:bg-yellow-900/50 rounded-lg transition-colors group">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-yellow-600 dark:text-yellow-400 group-hover:text-yellow-700 dark:group-hover:text-yellow-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
              </div>
            </div>
            <div v-if="!recentOrdinanceDrafts || recentOrdinanceDrafts.length === 0" class="text-center py-8">
              <DraftIcon class="w-12 h-12 text-gray-300 mx-auto mb-3" />
              <p class="text-gray-500 dark:text-gray-400">No ordinance drafts found</p>
              <Link href="/draft-ordinances/create" class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 mt-2 inline-block">
                Create your first ordinance draft  
              </Link>
            </div>
          </div>

          <!-- Resolution Drafts Tab Content -->
          <div v-show="activeTab === 'draft-resolutions'" class="space-y-4">
            <div v-for="draft in recentResolutionDrafts.slice(0, 5)" :key="draft.resolution_draft_id" class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                  <DraftIcon class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ draft.title || 'Untitled Resolution Draft' }}</p>
                <div v-if="draft.author" class="text-xs text-gray-500 dark:text-gray-400">
                  <div v-for="author in draft.author.split(';')" :key="author" class="trim">{{ author.trim() }}</div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ draft.session }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ formatDate(draft.date_drafted) }}</p>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400 mt-1">
                  {{ draft.status || 'Draft' }}
                </span>
              </div>
              <div class="flex-shrink-0 ml-2">
                <button @click="openDocumentModal(draft, 'draft-resolution')" class="inline-flex items-center justify-center w-8 h-8 bg-purple-100 dark:bg-purple-900/30 hover:bg-purple-200 dark:hover:bg-purple-900/50 rounded-lg transition-colors group">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-purple-600 dark:text-purple-400 group-hover:text-purple-700 dark:group-hover:text-purple-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
              </div>
            </div>
            <div v-if="!recentResolutionDrafts || recentResolutionDrafts.length === 0" class="text-center py-8">
              <DraftIcon class="w-12 h-12 text-gray-300 mx-auto mb-3" />
              <p class="text-gray-500 dark:text-gray-400">No resolution drafts found</p>
              <Link href="/draft-resolutions/create" class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 mt-2 inline-block">
                Create your first resolution draft  
              </Link>
            </div>
          </div>

          <!-- Accomplishments Tab Content -->
          <div v-show="activeTab === 'accomplishments'" class="space-y-4">
            <div v-for="accomplishment in recentAccomplishments.slice(0, 5)" :key="accomplishment.id" class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ accomplishment.title || 'Untitled Accomplishment' }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ accomplishment.description || 'No description' }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ formatDate(accomplishment.created_at) }}</p>
              </div>
              <div class="flex-shrink-0 ml-2">
                <button @click="openDocumentModal(accomplishment, 'accomplishment')" class="inline-flex items-center justify-center w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 hover:bg-indigo-200 dark:hover:bg-indigo-900/50 rounded-lg transition-colors group">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-600 dark:text-indigo-400 group-hover:text-indigo-700 dark:group-hover:text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
              </div>
            </div>
            <div v-if="!recentAccomplishments || recentAccomplishments.length === 0" class="text-center py-8">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <p class="text-gray-500 dark:text-gray-400">No accomplishments found</p>
              <Link href="/accomplishments/create" class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 mt-2 inline-block">
                Create your first accomplishment  
              </Link>
            </div>
          </div>

          <!-- Projects Tab Content -->
          <div v-show="activeTab === 'projects'" class="space-y-4">
            <div v-for="project in recentProjects.slice(0, 5)" :key="project.id" class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                  </svg>
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ project.title || 'Untitled Project' }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ project.description || 'No description' }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ formatDate(project.created_at) }}</p>
              </div>
              <div class="flex-shrink-0 ml-2">
                <button @click="openDocumentModal(project, 'project')" class="inline-flex items-center justify-center w-8 h-8 bg-teal-100 dark:bg-teal-900/30 hover:bg-teal-200 dark:hover:bg-teal-900/50 rounded-lg transition-colors group">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-teal-600 dark:text-teal-400 group-hover:text-teal-700 dark:group-hover:text-teal-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
              </div>
            </div>
            <div v-if="!recentProjects || recentProjects.length === 0" class="text-center py-8">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
              <p class="text-gray-500 dark:text-gray-400">No projects found</p>
              <Link href="/projects/create" class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 mt-2 inline-block">
                Create your first project  
              </Link>
            </div>
          </div>

          <!-- MOAs Tab Content -->
          <div v-show="activeTab === 'moas'" class="space-y-4">
            <div v-for="moa in recentMoas.slice(0, 5)" :key="moa.id" class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-pink-100 dark:bg-pink-900/30 rounded-lg flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-pink-600 dark:text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ moa.title || 'Untitled MOA' }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ moa.parties || 'No parties specified' }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ formatDate(moa.created_at) }}</p>
              </div>
              <div class="flex-shrink-0 ml-2">
                <button @click="openDocumentModal(moa, 'moa')" class="inline-flex items-center justify-center w-8 h-8 bg-pink-100 dark:bg-pink-900/30 hover:bg-pink-200 dark:hover:bg-pink-900/50 rounded-lg transition-colors group">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-pink-600 dark:text-pink-400 group-hover:text-pink-700 dark:group-hover:text-pink-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
              </div>
            </div>
            <div v-if="!recentMoas || recentMoas.length === 0" class="text-center py-8">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <p class="text-gray-500 dark:text-gray-400">No MOAs found</p>
              <Link href="/memorandum-of-agreements/create" class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 mt-2 inline-block">
                Create your first MOA  
              </Link>
            </div>
          </div>

          <!-- Committee Reports Tab Content -->
          <div v-show="activeTab === 'committee-reports'" class="space-y-4">
            <div v-for="report in recentCommitteeReports.slice(0, 5)" :key="report.report_id" class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-violet-100 dark:bg-violet-900/30 rounded-lg flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ report.title || 'Untitled Report' }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ report.subject || 'No subject specified' }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ formatDate(report.created_at) }}</p>
              </div>
              <div class="flex-shrink-0 ml-2">
                <button @click="openDocumentModal(report, 'committee-report')" class="inline-flex items-center justify-center w-8 h-8 bg-violet-100 dark:bg-violet-900/30 hover:bg-violet-200 dark:hover:bg-violet-900/50 rounded-lg transition-colors group">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-violet-600 dark:text-violet-400 group-hover:text-violet-700 dark:group-hover:text-violet-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
              </div>
            </div>
            <div v-if="!recentCommitteeReports || recentCommitteeReports.length === 0" class="text-center py-8">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <p class="text-gray-500 dark:text-gray-400">No committee reports found</p>
              <Link href="/committee-reports/create" class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 mt-2 inline-block">
                Create your first report  
              </Link>
            </div>
          </div>

          <!-- System Logs Tab Content -->
          <div v-show="activeTab === 'systemlogs'" class="space-y-4">
            <div v-for="log in recentSystemLogs.slice(0, 5)" :key="log.id" class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
              <div class="flex-shrink-0">
                <div :class="[
                  'w-8 h-8 rounded-lg flex items-center justify-center',
                  getLogIconBg(log.action)
                ]">
                  <component v-if="getLogIcon(log.action) === PlusIcon || getLogIcon(log.action) === InfoCircleIcon" :is="getLogIcon(log.action)" :class="[
                    'w-4 h-4',
                    getLogIconColor(log.action)
                  ]" />
                  <!-- Update Icon -->
                  <svg v-else-if="getLogIcon(log.action) === 'update-icon'" xmlns="http://www.w3.org/2000/svg" :class="[
                    'w-4 h-4',
                    getLogIconColor(log.action)
                  ]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                  </svg>
                  <!-- Delete Icon -->
                  <svg v-else-if="getLogIcon(log.action) === 'delete-icon'" xmlns="http://www.w3.org/2000/svg" :class="[
                    'w-4 h-4',
                    getLogIconColor(log.action)
                  ]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                  <!-- Rollback Icon -->
                  <svg v-else-if="getLogIcon(log.action) === 'rollback-icon'" xmlns="http://www.w3.org/2000/svg" :class="[
                    'w-4 h-4',
                    getLogIconColor(log.action)
                  ]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                  </svg>
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                  <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ getActionLabel(log.action) }}
                  </p>
                  <span :class="[
                    'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
                    getActionBadgeClass(log.action)
                  ]">
                    {{ getModelTypeLabel(log.target_table) }}
                  </span>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  {{ log.user ? log.user.username : 'System' }}
                </p>
                <p class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  {{ formatDate(log.created_at) }}
                </p>
                <p v-if="log.description" class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                  {{ log.description }}
                </p>
              </div>
              <div class="flex-shrink-0 ml-2">
                <Link :href="route('system-logs.show', log.log_id)" class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors group">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </Link>
              </div>
            </div>
            <div v-if="!recentSystemLogs || recentSystemLogs.length === 0" class="text-center py-8">
              <InfoCircleIcon class="w-12 h-12 text-gray-300 mx-auto mb-3" />
              <p class="text-gray-500 dark:text-gray-400">No system logs found</p>
              <Link href="/system-logs" class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 mt-2 inline-block">
                View system logs  
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>

  <!-- Document Modal -->
  <DocumentModal
    :show="showDocumentModal"
    :title="modalTitle"
    :subtitle="getModalSubtitle()"
    :document="selectedDocument"
    :type="modalType"
    :view-url="selectedDocument && modalType ? getViewUrl(selectedDocument, modalType) : ''"
    @close="closeDocumentModal"
  />

  <!-- Floating Developer Footer -->
  <FloatingFooter />
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DocumentModal from '@/Components/DocumentModal.vue'
import FloatingFooter from '@/Components/FloatingFooter.vue'
import { DocsIcon, DraftIcon, PlusIcon, PieChartIcon, BarChartIcon, InfoCircleIcon } from '@/icons'
import { formatDate, getTypeLabel, getViewUrl } from '@/composables/useDocumentSearch'
import { useDashboard } from '@/composables/useDashboard'

const props = defineProps({
  stats: Object,
  allDocuments: {
    type: Object,
    default: () => ({})
  },
  recentOrdinances: Array,
  recentResolutions: Array,
  recentOrdinanceDrafts: {
    type: Array,
    default: () => []
  },
  recentResolutionDrafts: {
    type: Array,
    default: () => []
  },
  recentAccomplishments: {
    type: Array,
    default: () => []
  },
  recentProjects: {
    type: Array,
    default: () => []
  },
  recentMoas: {
    type: Array,
    default: () => []
  },
  recentMunicipalCommunications: {
    type: Array,
    default: () => []
  },
  recentNationalOffices: {
    type: Array,
    default: () => []
  },
  recentProvincialOffices: {
    type: Array,
    default: () => []
  },
  recentCommitteeReports: {
    type: Array,
    default: () => []
  },
  recentSystemLogs: {
    type: Array,
    default: () => []
  },
  availableYears: {
    type: Array,
    default: () => []
  },
  availableAuthors: {
    type: Array,
    default: () => []
  },
})

// Use dashboard composable
const {
  activeTab,
  searchQuery,
  selectedType,
  selectedYear,
  selectedAuthor,
  filteredDocuments,
  formatFileSize,
  getActionLabel,
  getModelTypeLabel,
  getLogIcon,
  getLogIconBg,
  getLogIconColor,
  getActionBadgeClass,
  getTypeIcon,
  getTypeIconClass
} = useDashboard(props)

// Modal state
const showDocumentModal = ref(false)
const selectedDocument = ref(null)
const modalTitle = ref('')
const modalType = ref('')

// Modal functions
const openDocumentModal = (document, type) => {
  selectedDocument.value = document
  modalType.value = type
  
  // Set modal title based on type
  switch (type) {
    case 'ordinance':
      modalTitle.value = document.title || 'Untitled Ordinance'
      break
    case 'resolution':
      modalTitle.value = document.title || 'Untitled Resolution'
      break
    case 'draft-ordinance':
      modalTitle.value = document.title || 'Untitled Ordinance Draft'
      break
    case 'draft-resolution':
      modalTitle.value = document.title || 'Untitled Resolution Draft'
      break
    case 'accomplishment':
      modalTitle.value = document.title || 'Untitled Accomplishment'
      break
    case 'project':
      modalTitle.value = document.title || 'Untitled Project'
      break
    case 'moa':
      modalTitle.value = document.title || 'Untitled MOA'
      break
    default:
      modalTitle.value = 'Document Details'
  }
  
  showDocumentModal.value = true
}

const closeDocumentModal = () => {
  showDocumentModal.value = false
  selectedDocument.value = null
  modalTitle.value = ''
  modalType.value = ''
}

// Get modal subtitle based on document type
const getModalSubtitle = () => {
  const typeLabels = {
    'ordinance': 'Ordinance Information',
    'resolution': 'Resolution Information',
    'draft-ordinance': 'Draft Ordinance Information',
    'draft-resolution': 'Draft Resolution Information',
    'accomplishment': 'Accomplishment Details',
    'project': 'Project Details',
    'moa': 'Memorandum of Agreement Details'
  }
  return typeLabels[modalType.value] || 'Document Information'
}

// getTypeLabel and getViewUrl are provided by useDocumentSearch composable
</script>
