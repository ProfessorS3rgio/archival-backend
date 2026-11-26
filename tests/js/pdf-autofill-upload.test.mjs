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

test('a scanned PDF previews immediately and OCR still autofills its fields', async () => {
  let finishOCR
  const ocr = new Promise(resolve => { finishOCR = resolve })
  const state = useFileUpload(true, {
    extractPDFText: async () => '',
    extractScannedPDFText: async () => ocr,
  })
  const form = { title: '', author: '', date_approved: '', ordinance_no: '', files: [], extracted_text: '', file_extracted_texts: [] }
  const file = new File(['scanned pdf'], 'scan.pdf', { type: 'application/pdf' })
  const pending = state.handleFileSelect({ target: { files: [file] } }, form)
  assert.equal(state.previewObjects.value[0].type, 'pdf')
  assert.equal(state.processingFiles.value[0], false)
  finishOCR('ORDINANCE NO. 2344\nSection 1. Title. This Ordinance shall be known and hereafter referred to as the "THE 2026 TRAFFIC EDUCATION AND DRIVER\'S LICENSING ORDINANCE IN THE PROVINCE OF ZAMBOANGA SIBUGAY."')
  await pending
  assert.equal(form.ordinance_no, '2344')
  assert.equal(form.title, "THE 2026 TRAFFIC EDUCATION AND DRIVER'S LICENSING ORDINANCE IN THE PROVINCE OF")
  assert.equal(form.extracted_text, '')
  state.cleanupAllPreviews()
})
