import test from 'node:test'
import assert from 'node:assert/strict'
import { useAutoFillPreference } from '../../resources/js/composables/useAutoFillPreference.js'

test('the toggle saves to this website and reflects the confirmed preference', async () => {
  let actual
  const state = useAutoFillPreference(true, { request: async (...args) => {
    actual = args
    return { data: { success: true, auto_fill: false } }
  } })
  await state.toggleAutoFill()
  assert.equal(actual[0], '/settings/auto-fill')
  assert.deepEqual(actual[1], { auto_fill: false })
  assert.equal(state.autoFillEnabled.value, false)
  assert.equal(state.autoFillSaving.value, false)
})

test('edit forms start off but can explicitly enable and fill an already selected file', async () => {
  let filled = 0
  const state = useAutoFillPreference(true, {
    initiallyDisabled: true,
    request: async () => ({ data: { success: true, auto_fill: true } }),
    onEnable: async () => { filled++ },
  })
  assert.equal(state.autoFillEnabled.value, false)
  await state.toggleAutoFill()
  assert.equal(state.autoFillEnabled.value, true)
  assert.equal(filled, 1)
})

test('failed saves preserve the previous state and display an actionable error', async () => {
  const state = useAutoFillPreference(false, { request: async () => { throw new Error('Connection refused') } })
  await state.toggleAutoFill()
  assert.equal(state.autoFillEnabled.value, false)
  assert.match(state.autoFillError.value, /Please try again/)
  assert.equal(state.autoFillSaving.value, false)
})

test('unconfirmed HTML responses cannot be mistaken for a saved preference', async () => {
  const state = useAutoFillPreference(false, { request: async () => ({ data: '<html>Sign in</html>' }) })
  await state.toggleAutoFill()
  assert.equal(state.autoFillEnabled.value, false)
  assert.ok(state.autoFillError.value)
})

test('repeated clicks cannot send conflicting preference updates', async () => {
  let finish
  let calls = 0
  const state = useAutoFillPreference(false, { request: () => {
    calls++
    return new Promise(resolve => { finish = resolve })
  } })
  const first = state.toggleAutoFill()
  await state.toggleAutoFill()
  assert.equal(calls, 1)
  finish({ data: { success: true, auto_fill: true } })
  await first
  assert.equal(state.autoFillEnabled.value, true)
})
