<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 flex relative overflow-hidden">
    <!-- Enhanced Background with Grid Overlay -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <!-- Grid Pattern -->
      <div class="absolute inset-0 opacity-[0.02] dark:opacity-[0.05]">
        <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <pattern id="grid-bg" width="60" height="60" patternUnits="userSpaceOnUse">
              <path d="M 60 0 L 0 0 0 60" fill="none" stroke="currentColor" stroke-width="1" class="text-slate-600"/>
            </pattern>
          </defs>
          <rect width="100%" height="100%" fill="url(#grid-bg)" />
        </svg>
      </div>

      <!-- Enhanced Floating Elements -->
      <div class="absolute -top-40 -right-40 w-96 h-96 bg-gradient-to-br from-purple-400/20 to-pink-400/20 dark:from-purple-500/30 dark:to-pink-500/30 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-3xl opacity-40 animate-blob-enhanced"></div>
      <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-gradient-to-tr from-blue-400/20 to-cyan-400/20 dark:from-blue-500/30 dark:to-cyan-500/30 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-3xl opacity-40 animate-blob-enhanced animation-delay-2000"></div>
      <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-gradient-to-r from-indigo-400/20 to-purple-400/20 dark:from-indigo-500/30 dark:to-purple-500/30 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-3xl opacity-40 animate-blob-enhanced animation-delay-4000"></div>

      <!-- Particle Effects -->
      <div class="absolute inset-0">
        <div class="absolute top-1/4 left-1/4 w-2 h-2 bg-blue-400/30 rounded-full animate-float-particle animation-delay-1000"></div>
        <div class="absolute top-3/4 right-1/4 w-1.5 h-1.5 bg-purple-400/30 rounded-full animate-float-particle animation-delay-3000"></div>
        <div class="absolute top-1/2 right-1/3 w-1 h-1 bg-pink-400/30 rounded-full animate-float-particle animation-delay-5000"></div>
        <div class="absolute bottom-1/4 left-1/3 w-2.5 h-2.5 bg-indigo-400/30 rounded-full animate-float-particle animation-delay-7000"></div>
      </div>
    </div>
    <!-- Left Panel - Enhanced Branding -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-slate-800 via-slate-900 to-slate-950 p-16 flex-col justify-between relative overflow-hidden">
      <!-- Enhanced Background Pattern -->
      <div class="absolute inset-0 opacity-5">
        <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <pattern id="grid-branding" width="50" height="50" patternUnits="userSpaceOnUse">
              <path d="M 50 0 L 0 0 0 50" fill="none" stroke="white" stroke-width="0.5"/>
            </pattern>
          </defs>
          <rect width="100%" height="100%" fill="url(#grid-branding)" />
        </svg>
      </div>

      <!-- Subtle Gradient Overlay -->
      <div class="absolute inset-0 bg-gradient-to-br from-blue-600/10 via-transparent to-purple-600/10"></div>

      <div class="relative z-10">
        <!-- Logo/Banner Area with Actual Logo -->
        <div class="mb-12">
          <div class="w-24 h-24 bg-white/10 backdrop-blur-sm rounded-3xl flex items-center justify-center mb-6 border border-white/20 shadow-2xl">
            <img
              src="/assets/logos/logo.png"
              alt="Archival Management Logo"
              class="w-16 h-16 object-contain"
              @error="logoError = true"
            />
            <svg v-if="logoError" class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
            </svg>
          </div>
          <h1 class="text-5xl font-black text-white mb-3 tracking-tight">
            Recording & Archival Management System
          </h1>
          <!-- <p class="text-slate-300 text-xl font-medium leading-relaxed">
            Secure Document Management System
          </p> -->
        </div>

        <!-- Enhanced Features -->
        <div class="space-y-8 mt-16">
          <div class="flex items-start space-x-5 group">
            <div class="flex-shrink-0 w-14 h-14 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/20 group-hover:bg-white/20 transition-all duration-300 shadow-lg">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="text-white font-bold text-xl mb-2">Secure Storage</h3>
              <p class="text-slate-300 text-lg leading-relaxed">End-to-end encrypted document storage with role-based access control</p>
            </div>
          </div>

          <div class="flex items-start space-x-5 group">
            <div class="flex-shrink-0 w-14 h-14 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/20 group-hover:bg-white/20 transition-all duration-300 shadow-lg">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="text-white font-bold text-xl mb-2">Advanced Search</h3>
              <p class="text-slate-300 text-lg leading-relaxed">Powerful full-text search across all documents and metadata</p>
            </div>
          </div>

          <div class="flex items-start space-x-5 group">
            <div class="flex-shrink-0 w-14 h-14 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/20 group-hover:bg-white/20 transition-all duration-300 shadow-lg">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="text-white font-bold text-xl mb-2">Analytics & Reports</h3>
              <p class="text-slate-300 text-lg leading-relaxed">Comprehensive analytics and audit trails for compliance</p>
            </div>
          </div>
        </div>
      </div>

      <div class="relative z-10">
        <p class="text-slate-400 text-base font-medium">
          © 2025 Zamboanga Sibugay. All rights reserved.
        </p>
      </div>
    </div>

    <!-- Right Panel - Enhanced Login Form -->
    <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
      <div class="w-full max-w-md">
        <!-- Mobile Logo with Actual Logo -->
        <div class="lg:hidden mb-12 text-center">
          <div class="inline-flex w-20 h-20 bg-gradient-to-br from-slate-800 to-slate-900 rounded-3xl items-center justify-center mb-6 shadow-2xl border border-slate-200/20">
            <img
              src="/assets/logos/logo.png"
              alt="Archival Management Logo"
              class="w-14 h-14 object-contain"
              @error="logoError = true"
            />
            <svg v-if="logoError" class="w-14 h-14 text-slate-800" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
            </svg>
          </div>
          <h2 class="text-4xl font-black text-slate-900 dark:text-white mb-3 tracking-tight">
            Archival Management
          </h2>
        </div>

        <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-2xl rounded-3xl shadow-2xl border border-slate-200/50 dark:border-slate-700/50 overflow-hidden transform hover:scale-[1.01] transition-all duration-500 ease-out">
          <div class="px-8 pt-8 pb-2">
            <div class="text-center mb-8">
              <h2 class="text-3xl font-black text-slate-900 dark:text-white mb-2 tracking-tight">
                Welcome Back
              </h2>
              <p class="text-slate-600 dark:text-slate-400 text-base font-medium">
                Sign in to access your account
              </p>
            </div>

            <!-- Enhanced Error Alert -->
            <Transition
              enter-active-class="transition ease-out duration-400"
              enter-from-class="transform opacity-0 scale-95 translate-y-2"
              enter-to-class="transform opacity-100 scale-100 translate-y-0"
              leave-active-class="transition ease-in duration-300"
              leave-from-class="transform opacity-100 scale-100 translate-y-0"
              leave-to-class="transform opacity-0 scale-95 translate-y-2"
            >
              <div v-if="showError" class="mb-8 bg-red-50/90 dark:bg-red-900/20 border border-red-200/80 dark:border-red-800/80 rounded-2xl p-5 backdrop-blur-sm shadow-lg">
                <div class="flex items-start">
                  <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                  <div class="ml-4 flex-1">
                    <p class="text-base font-semibold text-red-800 dark:text-red-200">
                      {{ errorMessage }}
                    </p>
                  </div>
                  <button
                    type="button"
                    @click="dismissError"
                    class="ml-auto flex-shrink-0 inline-flex text-red-500 hover:text-red-600 focus:outline-none transition-all duration-200 hover:scale-110"
                  >
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                  </button>
                </div>
              </div>
            </Transition>

            <!-- Success Alert -->
            <Transition
              enter-active-class="transition ease-out duration-400"
              enter-from-class="transform opacity-0 scale-95 translate-y-2"
              enter-to-class="transform opacity-100 scale-100 translate-y-0"
              leave-active-class="transition ease-in duration-300"
              leave-from-class="transform opacity-100 scale-100 translate-y-0"
              leave-to-class="transform opacity-0 scale-95 translate-y-2"
            >
              <div v-if="showSuccess" class="mb-8 bg-green-50/90 dark:bg-green-900/20 border border-green-200/80 dark:border-green-800/80 rounded-2xl p-5 backdrop-blur-sm shadow-lg">
                <div class="flex items-start">
                  <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-green-500 animate-bounce" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                  <div class="ml-4 flex-1">
                    <p class="text-base font-semibold text-green-800 dark:text-green-200">
                      Login successful! Redirecting...
                    </p>
                  </div>
                </div>
              </div>
            </Transition>

            <form @submit.prevent="submit" class="space-y-6">
              <!-- Username Field with Enhanced Styling -->
              <div class="group">
                <label for="username" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors group-focus-within:text-slate-900 dark:group-focus-within:text-white">
                  Username
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none transition-all duration-300 group-focus-within:scale-110 group-focus-within:text-blue-500">
                    <svg class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                  <input
                    id="username"
                    name="username"
                    type="text"
                    autocomplete="username"
                    v-model="form.username"
                    class="appearance-none block w-full pl-10 pr-3 py-3 border-2 border-slate-200 dark:border-slate-600 rounded-xl placeholder-slate-400 dark:placeholder-slate-500 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 sm:text-sm transition-all duration-300 hover:border-slate-300 dark:hover:border-slate-500 focus:scale-[1.02] focus:shadow-xl"
                    placeholder="Enter your username"
                    :class="{ 'border-red-300 dark:border-red-600 focus:ring-red-500/20 focus:border-red-500 animate-shake': hasError }"
                  />
                </div>
              </div>

              <!-- Password Field with Enhanced Styling -->
              <div class="group">
                <label for="password" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors group-focus-within:text-slate-900 dark:group-focus-within:text-white">
                  Password
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none transition-all duration-300 group-focus-within:scale-110 group-focus-within:text-blue-500">
                    <svg class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                  <input
                    id="password"
                    name="password"
                    :type="showPassword ? 'text' : 'password'"
                    autocomplete="current-password"
                    v-model="form.password"
                    class="appearance-none block w-full pl-10 pr-12 py-3 border-2 border-slate-200 dark:border-slate-600 rounded-xl placeholder-slate-400 dark:placeholder-slate-500 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 sm:text-sm transition-all duration-300 hover:border-slate-300 dark:hover:border-slate-500 focus:scale-[1.02] focus:shadow-xl"
                    placeholder="Enter your password"
                    :class="{ 'border-red-300 dark:border-red-600 focus:ring-red-500/20 focus:border-red-500 animate-shake': hasError }"
                  />
                  <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 focus:outline-none focus:text-blue-500 transition-all duration-300 hover:scale-110 focus:scale-110"
                  >
                    <svg v-if="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Remember Me with Enhanced Styling -->
              <div class="flex items-center justify-between pt-2">
                <div class="flex items-center">
                  <input
                    id="remember-me"
                    name="remember-me"
                    type="checkbox"
                    v-model="form.remember"
                    class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-2 border-slate-300 dark:border-slate-600 rounded-lg transition-all duration-300 hover:border-slate-400 dark:hover:border-slate-500 focus:ring-4 focus:ring-blue-500/20"
                  />
                  <label for="remember-me" class="ml-3 block text-base text-slate-700 dark:text-slate-300 font-semibold hover:text-slate-900 dark:hover:text-white transition-colors cursor-pointer">
                    Remember me
                  </label>
                </div>
              </div>

              <!-- Enhanced Submit Button -->
              <div class="pt-2">
                <button
                  type="submit"
                  :disabled="processing"
                  class="group relative w-full flex justify-center items-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 hover:from-blue-700 hover:via-blue-800 hover:to-indigo-800 focus:outline-none focus:ring-4 focus:ring-blue-500/30 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-500 shadow-lg hover:shadow-blue-500/25 transform hover:-translate-y-0.5 hover:scale-[1.01] focus:scale-[1.01] active:scale-[0.98]"
                >
                  <svg v-if="processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg v-else class="w-5 h-5 mr-2 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                  </svg>
                  <span>{{ processing ? 'Signing in...' : 'Sign in to Account' }}</span>
                </button>
              </div>
            </form>
          </div>

          <!-- Enhanced Footer -->
          <div class="px-10 py-8 bg-slate-50/60 dark:bg-slate-900/40 backdrop-blur-sm border-t border-slate-200/50 dark:border-slate-700/50">
            <div class="flex items-center justify-center space-x-3 text-sm text-slate-500 dark:text-slate-400">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
              </svg>
              <span class="font-semibold">Archival Management System v1.0 © 2025</span>
            </div>
            <p class="text-center text-sm text-slate-400 dark:text-slate-500 mt-3 font-medium">
              Secure document management and archival solution
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Developer Footer -->
  <FloatingFooter />
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import FloatingFooter from '@/Components/FloatingFooter.vue'

defineProps({
  errors: {
    type: Object,
    default: () => ({})
  }
})

const form = useForm({
  username: '',
  password: '',
  remember: false
})

const showPassword = ref(false)
const errorMessage = ref('')
const showError = ref(false)
const showSuccess = ref(false)
const logoError = ref(false)

const submit = () => {
  // Reset states
  errorMessage.value = ''
  showError.value = false
  showSuccess.value = false

  // Client-side validation
  if (!form.username || !form.password) {
    errorMessage.value = 'Please fill in all required fields'
    showError.value = true
    return
  }

  if (form.username.length < 3) {
    errorMessage.value = 'Username must be at least 3 characters long'
    showError.value = true
    return
  }

  if (form.password.length < 6) {
    errorMessage.value = 'Password must be at least 6 characters long'
    showError.value = true
    return
  }

  form.post('/login', {
    onFinish: () => {
      form.reset('password')
    },
    onError: (errors) => {
      // Handle server-side errors - extract string from array if needed
      let message = 'Invalid credentials. Please try again.'
      
      if (errors.username) {
        message = Array.isArray(errors.username) ? errors.username[0] : errors.username
      } else if (errors.password) {
        message = Array.isArray(errors.password) ? errors.password[0] : errors.password
      } else if (errors.message) {
        message = Array.isArray(errors.message) ? errors.message[0] : errors.message
      } else if (errors.email) {
        message = Array.isArray(errors.email) ? errors.email[0] : errors.email
      }
      
      errorMessage.value = message
      showError.value = true

      // Auto-hide error after 5 seconds
      setTimeout(() => {
        showError.value = false
      }, 5000)
    },
    onSuccess: () => {
      errorMessage.value = ''
      showError.value = false
      showSuccess.value = true

      // Auto-hide success after 2 seconds
      setTimeout(() => {
        showSuccess.value = false
      }, 2000)
    }
  })
}

const processing = computed(() => form.processing)
const hasError = computed(() => showError.value)

const dismissError = () => {
  showError.value = false
  errorMessage.value = ''
}
</script>

<style scoped>
@keyframes blob-enhanced {
  0% {
    transform: translate(0px, 0px) scale(1) rotate(0deg);
  }
  25% {
    transform: translate(30px, -50px) scale(1.1) rotate(90deg);
  }
  50% {
    transform: translate(-20px, 20px) scale(0.9) rotate(180deg);
  }
  75% {
    transform: translate(40px, -30px) scale(1.05) rotate(270deg);
  }
  100% {
    transform: translate(0px, 0px) scale(1) rotate(360deg);
  }
}

@keyframes float-particle {
  0%, 100% {
    transform: translateY(0px) rotate(0deg);
    opacity: 0.3;
  }
  50% {
    transform: translateY(-20px) rotate(180deg);
    opacity: 0.6;
  }
}

@keyframes shake {
  0%, 100% {
    transform: translateX(0);
  }
  25% {
    transform: translateX(-5px);
  }
  75% {
    transform: translateX(5px);
  }
}

.animate-blob-enhanced {
  animation: blob-enhanced 12s infinite ease-in-out;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}

.animate-float-particle {
  animation: float-particle 8s infinite ease-in-out;
}

.animate-shake {
  animation: shake 0.5s ease-in-out;
}

/* Enhanced focus states for accessibility */
input:focus-visible,
button:focus-visible {
  outline: 2px solid #3b82f6;
  outline-offset: 2px;
}

/* Smooth transitions for all interactive elements */
* {
  transition-property: color, background-color, border-color, transform, opacity, box-shadow;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}

/* Custom scrollbar for better aesthetics */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: transparent;
}

::-webkit-scrollbar-thumb {
  background: rgba(148, 163, 184, 0.3);
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(148, 163, 184, 0.5);
}
</style>