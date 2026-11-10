<template>
  <AdminLayout title="My Profile">
    <Head title="My Profile" />
    <div class="max-w-3xl mx-auto space-y-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Profile</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage your account information.</p>
      </div>

      <section class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-center gap-4 border-b border-gray-200 pb-6 dark:border-gray-700">
          <button type="button" @click="pictureInput?.click()" :disabled="form.processing"
            :aria-label="picturePreview ? 'Change profile picture' : 'Upload profile picture'"
            :title="picturePreview ? 'Change profile picture' : 'Upload profile picture'"
            class="group relative h-20 w-20 shrink-0 cursor-pointer rounded-full focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-4 disabled:cursor-wait disabled:opacity-50">
            <img v-if="picturePreview" :src="picturePreview" alt="Profile picture preview" class="h-full w-full rounded-full object-cover" />
            <span v-else class="flex h-full w-full items-center justify-center rounded-full bg-blue-100 text-2xl font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300" aria-hidden="true">{{ initials }}</span>
            <span class="absolute bottom-0 right-0 flex h-8 w-8 items-center justify-center rounded-full border-2 border-white bg-blue-600 text-white shadow-sm transition-colors group-hover:bg-blue-700 dark:border-gray-800">
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3Z" />
                <circle cx="12" cy="13" r="4" />
              </svg>
            </span>
          </button>
          <div class="min-w-0">
            <h2 class="break-words text-lg font-semibold text-gray-900 dark:text-white">{{ user.name }}</h2>
            <p class="text-sm capitalize text-gray-500 dark:text-gray-400">{{ user.role }}</p>
            <button type="button" @click="pictureInput?.click()" :disabled="form.processing" class="mt-1 text-sm font-medium text-blue-600 hover:underline disabled:opacity-50 dark:text-blue-400">{{ picturePreview ? 'Change photo' : 'Upload photo' }}</button>
          </div>
        </div>

        <form @submit.prevent="save" class="mt-6 space-y-5" :aria-busy="form.processing" novalidate>
          <UploadProgress :processing="form.processing" :progress="form.progress?.percentage ?? null" />
          <fieldset :disabled="form.processing" class="space-y-5">
          <div>
            <label for="profile_picture" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Profile Picture</label>
            <input id="profile_picture" ref="pictureInput" type="file" accept="image/jpeg,image/png,image/webp" @change="selectPicture"
              :aria-invalid="Boolean(form.errors.profile_picture)" aria-describedby="picture-help picture-error"
              class="block w-full text-sm text-gray-700 dark:text-gray-300" />
            <p id="picture-help" class="mt-2 text-xs text-gray-500 dark:text-gray-400">JPG, PNG, or WebP. Maximum 2 MB and 4096 x 4096 pixels.</p>
            <p v-if="!form.processing && (form.profile_picture || form.remove_profile_picture)" role="status" class="mt-2 text-sm text-blue-600 dark:text-blue-400">Click Save Changes to apply your picture update.</p>
            <p v-if="form.errors.profile_picture" id="picture-error" role="alert" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.profile_picture }}</p>
            <button v-if="picturePreview" type="button" @click="removePicture" class="mt-2 text-sm text-red-600 hover:underline dark:text-red-400">Remove picture</button>
          </div>
          <div v-for="field in fields" :key="field.key">
            <label :for="field.key" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ field.label }}</label>
            <input :id="field.key" v-model="form[field.key]" :type="field.type" :autocomplete="field.autocomplete" required maxlength="255"
              :aria-invalid="Boolean(form.errors[field.key])" :aria-describedby="form.errors[field.key] ? `${field.key}-error` : undefined"
              class="w-full rounded-lg border bg-white px-3 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
              :class="form.errors[field.key] ? 'border-red-500' : 'border-gray-300 dark:border-gray-600'" />
            <p v-if="form.errors[field.key]" :id="`${field.key}-error`" role="alert" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors[field.key] }}</p>
          </div>
          <p v-if="form.errors.general" role="alert" class="text-sm text-red-600 dark:text-red-400">{{ form.errors.general }}</p>
          <div class="flex flex-wrap items-center gap-4">
            <button type="submit" :disabled="form.processing" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50"><svg v-if="form.processing" class="h-4 w-4 animate-spin motion-reduce:animate-none" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" opacity="0.2" /><path d="M12 3a9 9 0 0 1 9 9" stroke="currentColor" stroke-width="3" stroke-linecap="round" /></svg>{{ form.processing ? (form.progress?.percentage >= 100 ? 'Finishing save...' : 'Uploading / saving...') : 'Save Changes' }}</button>
            <p v-if="form.recentlySuccessful" role="status" class="text-sm text-green-700 dark:text-green-400">Profile saved.</p>
          </div>
          </fieldset>
        </form>
      </section>

      <section class="flex flex-col gap-4 rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 class="font-semibold text-gray-900 dark:text-white">Password</h2>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update the password you use to sign in.</p>
        </div>
        <Link href="/settings/change-password" class="shrink-0 rounded-lg border border-gray-300 px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">Change Password</Link>
      </section>
    </div>
  </AdminLayout>
</template>

<script setup>
import UploadProgress from '@/Components/UploadProgress.vue'
import { computed, ref, onBeforeUnmount } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const page = usePage()
const user = computed(() => page.props.auth.user)
const initials = computed(() => user.value.name.split(/\s+/).filter(Boolean).slice(0, 2).map(part => Array.from(part)[0]).join('').toUpperCase())
const fields = [
  { key: 'name', label: 'Full Name', type: 'text', autocomplete: 'name' },
  { key: 'email', label: 'Email Address', type: 'email', autocomplete: 'email' },
  { key: 'username', label: 'Username', type: 'text', autocomplete: 'username' },
]
const form = useForm({ name: user.value.name, email: user.value.email, username: user.value.username, profile_picture: null, remove_profile_picture: false })
const pictureInput = ref(null)
const localPreview = ref(null)
const picturePreview = computed(() => localPreview.value || (form.remove_profile_picture ? null : user.value.profile_picture_url))
const clearPreview = () => {
  if (localPreview.value) URL.revokeObjectURL(localPreview.value)
  localPreview.value = null
}
const selectPicture = (event) => {
  clearPreview()
  form.profile_picture = event.target.files[0] || null
  form.remove_profile_picture = false
  form.clearErrors('profile_picture')
  if (form.profile_picture) localPreview.value = URL.createObjectURL(form.profile_picture)
}
const removePicture = () => {
  clearPreview()
  form.profile_picture = null
  form.remove_profile_picture = true
  if (pictureInput.value) pictureInput.value.value = ''
}
const save = () => {
  if (form.processing) return
  form.transform(data => ({ ...data, _method: 'patch' })).post('/profile', {
  forceFormData: true,
  preserveScroll: true,
  onSuccess: () => {
    clearPreview()
    form.profile_picture = null
    form.remove_profile_picture = false
    if (pictureInput.value) pictureInput.value.value = ''
  },
})
}
onBeforeUnmount(clearPreview)
</script>
