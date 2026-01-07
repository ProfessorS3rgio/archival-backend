<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow-xl rounded-lg sm:px-10">
        <div class="text-center">
          <h1 class="mt-6 text-3xl font-bold text-gray-900 dark:text-white">
            404 - Page Not Found
          </h1>
          <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            The page you're looking for doesn't exist or has been moved.
          </p>
          <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6">
            <h2 class="text-lg font-semibold text-blue-900 dark:text-blue-300 mb-4">
              Need Help?
            </h2>
            <p class="text-sm text-blue-800 dark:text-blue-400 mb-3">
              If you believe this is an error, please contact the System Developer:
            </p>
            <div class="space-y-2 text-sm text-blue-700 dark:text-blue-300">
              <div>
                <strong>Email:</strong> Billymamaril2002@gmail.com
              </div>
              <div class="flex items-center justify-center">
                <a
                  href="https://www.facebook.com/billy.mamaril.334"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                >
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                  </svg>
                  <span>Facebook: Billy Cabales Mamaril</span>
                </a>
              </div>
              <div>
                <strong>Phone:</strong> +63 997 195 2649
              </div>
              <div>
                <strong>Address:</strong> Ipil Zamboanga Sibugay, Philippines
              </div>
            </div>
          </div>
          <div class="mt-6">
            <a
              href="/dashboard"
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
            >
              Back to Dashboard
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Floating Footer -->
    <FloatingFooter />
  </div>
</template>

<script setup>
import FloatingFooter from '@/Components/FloatingFooter.vue'
</script>