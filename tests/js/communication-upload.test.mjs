import test from 'node:test'
import assert from 'node:assert/strict'
import { useCommunicationFileUpload } from '../../resources/js/composables/useCommunicationFileUpload.js'

function setup(options = {}) {
  const revoked = []
  let sequence = 0
  const form = { errors: {}, extracted_text: '', files: [], clearErrors() { this.errors = {} }, setError(key, value) { this.errors[key] = value } }
  const state = useCommunicationFileUpload({
    objectUrls: { createObjectURL: () => `blob:test-${++sequence}`, revokeObjectURL: url => revoked.push(url) },
    extractPDFText: async () => 'PDF text', extractDOCXText: async () => 'DOCX text',
    request: async () => ({ data: { url: 'https://example.test/signed-preview' } }), ...options,
  })
  const choose = (name, type = '', bytes = 'file') => state.handleFileSelect({ target: { files: [new File([bytes], name, { type })], value: name } }, form)
  return { state, form, choose, revoked }
}

test('a PDF stays previewable and uploadable when text extraction fails', async () => {
  const { state, form, choose } = setup({ extractPDFText: async () => { throw new Error('Failed to load PDF worker') } })
  await choose('scanned.PDF', '')
  assert.equal(state.previewObjects.value[0].type, 'pdf')
  assert.match(state.previewObjects.value[0].url, /^blob:/)
  assert.match(state.previewObjects.value[0].warning, /still preview and upload/)
  assert.equal(form.files.length, 1)
  assert.equal(state.processingFiles.value[0], false)
})

test('legacy DOC uses Office preview without invoking the DOCX parser', async () => {
  const { state, choose } = setup({ extractDOCXText: () => assert.fail('DOC is not DOCX') })
  await choose('legacy.doc', 'application/msword')
  assert.equal(state.previewObjects.value[0].type, 'office')
  assert.match(state.previewObjects.value[0].url, /^https:/)
})

test('DOCX preview still works if optional text extraction fails', async () => {
  const { state, choose } = setup({ extractDOCXText: async () => { throw new Error('Invalid ZIP data') } })
  await choose('letter.docx')
  assert.equal(state.previewObjects.value[0].type, 'office')
})

test('image and Office formats offered by the picker produce previews even with an empty MIME type', async () => {
  for (const extension of ['jpg', 'jpeg', 'png', 'xls', 'xlsx', 'ppt', 'pptx']) {
    const { state, choose } = setup()
    await choose(`attachment.${extension}`)
    assert.equal(state.previewObjects.value[0].type, ['jpg', 'jpeg', 'png'].includes(extension) ? 'image' : 'office')
  }
})

test('a failed preview request preserves the original attachment for saving', async () => {
  const { state, form, choose } = setup({ request: async () => { throw { response: { status: 422 } } } })
  await choose('letter.docx')
  assert.equal(state.previewObjects.value[0].type, 'error')
  assert.match(state.previewObjects.value[0].content, /still upload the original/)
  assert.equal(form.files[0].name, 'letter.docx')
  assert.equal(state.processingFiles.value[0], false)
})

test('removing a file while its preview is processing does not resurrect it', async () => {
  let finish
  const { state, form, choose, revoked } = setup({ extractPDFText: () => new Promise(resolve => { finish = resolve }) })
  const pending = choose('remove.pdf')
  state.removeFile(0, form)
  finish('late text')
  await pending
  assert.deepEqual(state.selectedFiles.value, [])
  assert.deepEqual(state.previewObjects.value, [])
  assert.deepEqual(form.files, [])
  assert.deepEqual(revoked, ['blob:test-1'])
})

test('only the latest selection contributes its preview and search text', async () => {
  let finish
  const { state, form, choose } = setup({ extractPDFText: () => new Promise(resolve => { finish = resolve }) })
  const pending = choose('old.pdf')
  await choose('new.png')
  finish('old text')
  await pending
  assert.equal(state.selectedFiles.value[0].name, 'new.png')
  assert.equal(state.previewObjects.value[0].type, 'image')
  assert.equal(form.extracted_text, '')
})

test('unsupported, oversized and multiple attachments get field errors', async () => {
  const { state, form, choose } = setup()
  await choose('run.exe')
  assert.ok(form.errors.files)
  await choose('large.pdf', '', new Uint8Array(10 * 1024 * 1024 + 1))
  assert.match(form.errors.files, /10 MB/)
  await state.handleFileSelect({ target: { files: [new File(['a'], 'a.pdf'), new File(['b'], 'b.pdf')], value: '' } }, form)
  assert.match(form.errors.files, /one attachment/)
})

test('extractor control characters are omitted from optional search text', async () => {
  const { form, choose } = setup({ extractPDFText: async () => 'text\0\f\nnext' })
  await choose('scan.pdf')
  assert.equal(form.extracted_text, 'text\nnext')
})
