import test from 'node:test'
import assert from 'node:assert/strict'
import { useUploadSubmission } from '../../resources/js/composables/useUploadSubmission.js'

function form() {
  return {
    processing: false,
    files: ['selected.pdf'],
    errors: {},
    clearErrors() { this.errors = {} },
    setError(field, message) { this.errors[field] = message },
  }
}

test('100% transferred stays in saving state until the server confirms success', async () => {
  const data = form()
  let finish
  const state = useUploadSubmission(data, {
    successDelay: 0,
    request: (_, __, config) => {
      config.onUploadProgress({ loaded: 40, total: 100 })
      return new Promise(resolve => { finish = resolve })
    },
  })
  const pending = state.submitUpload('/ordinances', {})
  assert.equal(data.processing, true)
  assert.equal(state.uploadProgress.value, 40)
  assert.equal(state.uploadState.value, 'uploading')
  await assert.rejects(state.submitUpload('/ordinances', {}), /already in progress/)
  finish({ status: 200 })
  await pending
  assert.equal(state.uploadState.value, 'success')
  assert.equal(data.processing, false)
})

test('finishing the browser transfer does not claim the document is saved', async () => {
  let finish
  const state = useUploadSubmission(form(), {
    successDelay: 0,
    request: (_, __, config) => {
      config.onUploadProgress({ loaded: 100, total: 100 })
      return new Promise(resolve => { finish = resolve })
    },
  })
  const pending = state.submitUpload('/ordinances', {})
  assert.equal(state.uploadState.value, 'saving')
  assert.match(state.uploadMessage.value, /Waiting for the server/)
  finish({ status: 200 })
  await pending
  assert.equal(state.uploadState.value, 'success')
})

test('network failure preserves selected files, unlocks the form, and allows a manual retry', async () => {
  const data = form()
  let attempts = 0
  const state = useUploadSubmission(data, {
    successDelay: 0,
    request: async () => {
      if (++attempts === 1) throw new Error('Network Error')
      return { status: 200 }
    },
  })
  await assert.rejects(state.submitUpload('/ordinances', {}))
  assert.equal(state.uploadState.value, 'network-error')
  assert.match(state.uploadMessage.value, /check the document list/i)
  assert.deepEqual(data.files, ['selected.pdf'])
  assert.equal(data.processing, false)
  assert.equal(attempts, 1, 'Never retry a potentially saved POST automatically')
  await state.submitUpload('/ordinances', {})
  assert.equal(state.uploadState.value, 'success')
})

test('validation errors appear on fields and never show success', async () => {
  const data = form()
  const state = useUploadSubmission(data, {
    request: async () => { throw { response: { status: 422, data: { errors: { title: ['Title is required.'] } } } } },
  })
  await assert.rejects(state.submitUpload('/ordinances', {}))
  assert.equal(state.uploadState.value, 'error')
  assert.equal(data.errors.title, 'Title is required.')
  assert.equal(data.processing, false)
})

test('oversized requests and expired login redirects are not reported as saved', async () => {
  for (const status of [413, 419, 500]) {
    const state = useUploadSubmission(form(), {
      request: async () => { throw { response: { status } } },
    })
    await assert.rejects(state.submitUpload('/ordinances', {}))
    assert.equal(state.uploadState.value, 'error')
  }
  const state = useUploadSubmission(form(), {
    request: async () => ({ request: { responseURL: 'https://example.test/login' } }),
  })
  await assert.rejects(state.submitUpload('/ordinances', {}))
  assert.match(state.uploadMessage.value, /session expired/i)
})

test('an HTML response cannot be mistaken for a confirmed document save', async () => {
  const state = useUploadSubmission(form(), {
    request: async () => ({ status: 200, data: '<!doctype html><html>Unconfirmed response</html>' }),
  })
  await assert.rejects(state.submitUpload('/ordinances', {}))
  assert.equal(state.uploadState.value, 'error')
  assert.match(state.uploadMessage.value, /could not confirm/)
})
