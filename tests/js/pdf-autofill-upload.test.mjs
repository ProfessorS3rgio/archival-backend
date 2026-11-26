import test from 'node:test'
import assert from 'node:assert/strict'
import { useFileUpload } from '../../resources/js/composables/useFileUpload.js'

test('PDF text autofills form fields while queued extraction owns search text', async () => {
  const state = useFileUpload(true, { extractPDFText: async () => 'ORDINANCE NO. 123\nAN ORDINANCE ESTABLISHING A TEST ARCHIVE' })
  const form = { title: '', author: '', date_approved: '', ordinance_no: '', files: [], extracted_text: '', file_extracted_texts: [] }
  const file = new File(['pdf bytes'], 'ordinance.pdf', { type: 'application/pdf' })
  const pending = state.handleFileSelect({ target: { files: [file] } }, form)
  await pending

  assert.equal(state.previewObjects.value[0].type, 'pdf')
  assert.equal(form.title, 'ESTABLISHING A TEST ARCHIVE')
  assert.equal(form.ordinance_no, '123')
  assert.equal(form.extracted_text, '')
  assert.deepEqual(form.file_extracted_texts, [''])
  state.cleanupAllPreviews()
})
