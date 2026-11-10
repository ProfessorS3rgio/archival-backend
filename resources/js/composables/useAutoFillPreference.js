import { ref } from 'vue'
import axios from 'axios'

export function useAutoFillPreference(initialValue, { initiallyDisabled = false, request = axios.patch, onEnable = async () => {} } = {}) {
  const autoFillEnabled = ref(Boolean(initialValue) && !initiallyDisabled)
  const autoFillSaving = ref(false)
  const autoFillError = ref('')

  const toggleAutoFill = async (event) => {
    event?.preventDefault?.()
    if (autoFillSaving.value) return
    autoFillSaving.value = true
    autoFillError.value = ''
    const enabled = !autoFillEnabled.value
    try {
      const response = await request('/settings/auto-fill', { auto_fill: enabled }, { headers: { Accept: 'application/json' } })
      if (response.data?.success !== true || response.data.auto_fill !== enabled) throw new Error('Setting was not confirmed')
      autoFillEnabled.value = enabled
      if (enabled) await onEnable()
    } catch {
      autoFillError.value = 'Could not update auto-fill. Please try again.'
    } finally {
      autoFillSaving.value = false
    }
  }

  return { autoFillEnabled, autoFillSaving, autoFillError, toggleAutoFill }
}
