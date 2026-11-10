export function registerFormErrorHandlers(router, notify) {
  const showError = (text) => notify({ icon: 'error', title: 'Unable to complete request', text })

  router.on('exception', (event) => {
    if (!event.detail.exception?.isAxiosError) return
    event.preventDefault()
    showError('Connection interrupted. Your form is still open. Check whether your changes were saved before trying again.')
  })

  // A proxy or an early middleware failure may respond before Laravel starts a session.
  router.on('invalid', (event) => {
    const status = event.detail.response?.status
    if (status < 400 || !status) return
    event.preventDefault()
    const message = {
      401: 'Please sign in again before submitting this form.',
      403: 'You do not have permission to perform this action.',
      404: 'This record is no longer available. Refresh the page before trying again.',
      413: 'The upload is too large. Choose fewer or smaller files and try again.',
      419: 'Your session expired. Refresh the page, sign in if needed, and try again.',
      422: 'Please check the form values and try again.',
      429: 'Too many attempts. Please wait a moment before trying again.',
    }[status] || 'We could not complete the request. Your form is still open. Check whether your changes were saved before trying again.'
    showError(message)
  })
}
