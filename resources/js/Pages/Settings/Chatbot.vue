<script setup>
import { Head } from '@inertiajs/vue3'
import { nextTick, ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ assistantUrl: { type: String, required: true } })

const messages = ref([])
const pending = ref(false)
const latestReply = ref(null)

const menu = [
  { label: 'Recent resolutions', intent: 'recent_resolutions' },
  { label: 'Recent ordinances', intent: 'recent_ordinances' },
  { label: 'Search resolutions', intent: 'resolution_search', prompt: 'Enter a title, topic, or keyword.' },
  { label: 'Search ordinances', intent: 'ordinance_search', prompt: 'Enter a title, topic, or keyword.' },
  { label: 'Find by year', intent: 'records_by_year', prompt: 'Enter a year, for example 2026.' },
]

async function runMenu(item) {
  if (pending.value) return
  let value = ''
  if (item.prompt) {
    value = window.prompt(item.prompt) || ''
    if (!value.trim()) return
  }
  const label = item.prompt ? `${item.label}: ${value}` : item.label
  messages.value.push({ question: label, reply: null })
  const message = messages.value.at(-1)
  pending.value = true
  try {
    const input = item.intent === 'records_by_year' ? { year: value } : (value ? { query: value } : {})
    const response = await fetch(props.assistantUrl, {
      method: 'POST', headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify({ intent: item.intent, input }),
    })
    const data = await response.json()
    message.reply = response.ok ? { answer: data.message, items: data.items } : { answer: data.message || 'The archive could not complete the request.', status: 'error' }
  } catch {
    message.reply = { answer: 'The local archive server could not respond.', status: 'error' }
  } finally {
    pending.value = false
    await nextTick()
    latestReply.value?.scrollIntoView({ block: 'nearest' })
  }
}

async function clearChat() {
  messages.value = []
  await nextTick()
}
</script>

<template>
  <Head title="Chatbot" />
  <AdminLayout title="Chatbot">
    <section class="mx-auto max-w-4xl overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800" aria-labelledby="chat-title">
      <header class="flex flex-wrap items-start justify-between gap-4 border-b border-gray-200 px-5 py-5 sm:px-7 dark:border-gray-700">
        <div>
          <h1 id="chat-title" class="text-xl font-semibold text-gray-900 dark:text-white">Ask the archive</h1>
          <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Test questions about indexed ordinances and resolutions in English or Filipino.</p>
        </div>
        <button
          v-if="messages.length"
          type="button"
          class="rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
          :disabled="pending"
          @click="clearChat"
        >Clear chat</button>
      </header>

      <ol v-if="messages.length" class="max-h-[55vh] space-y-6 overflow-y-auto px-5 py-6 sm:px-7" aria-label="Chat history" role="log">
        <li v-for="(message, index) in messages" :key="index" class="space-y-3">
          <div class="ml-auto w-fit max-w-[90%] rounded-2xl bg-blue-50 px-4 py-3 text-gray-900 dark:bg-blue-950 dark:text-blue-50">
            <span class="sr-only">Your question</span>
            <p class="whitespace-pre-wrap break-words text-sm">{{ message.question }}</p>
          </div>
          <div :ref="element => { if (index === messages.length - 1) latestReply = element }" class="rounded-2xl border border-gray-200 px-4 py-3 dark:border-gray-700">
            <span class="sr-only">Archive response</span>
            <p v-if="!message.reply" class="text-sm text-gray-500 dark:text-gray-300">Searching the archive…</p>
            <template v-else>
              <p class="whitespace-pre-wrap break-words text-sm text-gray-900 dark:text-gray-100">{{ message.reply.answer }}</p>
              <ul v-if="message.reply.items?.length" class="mt-3 space-y-2">
                <li v-for="item in message.reply.items" :key="`${item.type}-${item.id}`" class="rounded-lg bg-gray-50 p-3 dark:bg-gray-900/50">
                  <a :href="item.url" class="font-medium text-blue-700 underline dark:text-blue-300">{{ item.number }} — {{ item.title }}</a>
                  <p v-if="item.date_approved" class="mt-1 text-xs text-gray-500 dark:text-gray-400">Approved {{ item.date_approved }}</p>
                </li>
              </ul>
              <ul v-if="message.reply.sources?.length" class="mt-4 space-y-3 border-t border-gray-100 pt-3 dark:border-gray-700" aria-label="Sources">
                <li v-for="source in message.reply.sources" :key="source.label">
                  <a :href="source.url" target="_blank" rel="noopener" class="text-sm font-medium text-blue-700 underline underline-offset-2 dark:text-blue-300">
                    {{ source.title }}, PDF page {{ source.page }}
                  </a>
                  <p class="mt-1 break-words text-sm text-gray-600 dark:text-gray-300">{{ source.text }}</p>
                </li>
              </ul>
            </template>
          </div>
        </li>
      </ol>

      <div class="space-y-3 bg-gray-50 px-5 py-5 sm:px-7 dark:bg-gray-900/40">
        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">How may we assist you?</p>
        <div class="grid gap-2 sm:grid-cols-2" aria-label="Archive assistant menu">
          <button v-for="item in menu" :key="item.intent" type="button" :disabled="pending" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-blue-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700" @click="runMenu(item)">
            {{ item.label }}
          </button>
        </div>
      </div>
    </section>
  </AdminLayout>
</template>
