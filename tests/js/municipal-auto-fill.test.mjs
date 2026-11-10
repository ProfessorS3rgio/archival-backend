import test from 'node:test'
import assert from 'node:assert/strict'
import { parseMunicipalCommunication, fillMunicipalCommunication } from '../../resources/js/utils/municipalCommunicationAutoFill.js'
import { useCommunicationFileUpload } from '../../resources/js/composables/useCommunicationFileUpload.js'

const text = 'Republic of the Philippines\nMunicipality of Ipil\nSubject: Request for certified copies\nDate Received: January 12, 2022\nApproved: December 10, 2021'

function setup(options = {}) {
  const form = { title: '', subject: '', year: new Date().getFullYear(), date_received: '', files: [], extracted_text: '', clearErrors() {}, setError() {} }
  const state = useCommunicationFileUpload({
    objectUrls: { createObjectURL: () => 'blob:test', revokeObjectURL() {} },
    extractPDFText: async () => text,
    ...options,
  })
  const choose = name => state.handleFileSelect({ target: { files: [new File(['test'], name)], value: name } }, form)
  return { form, state, choose }
}

test('municipal letterhead, title and explicitly received date map to the correct fields', () => {
  assert.deepEqual(parseMunicipalCommunication(text, 'letter.pdf'), {
    title: 'Request for certified copies', subject: 'Ipil', date_received: '2022-01-12', year: 2022,
  })
})

test('uploading a readable PDF actually fills the municipal form, not just search text', async () => {
  const { form, state, choose } = setup()
  await choose('letter.pdf')
  assert.equal(form.title, 'Request for certified copies')
  assert.equal(form.subject, 'Ipil')
  assert.equal(form.year, 2022)
  assert.equal(form.date_received, '2022-01-12')
  assert.equal(form.extracted_text, text)
  assert.match(state.autoFillMessage.value, /Review the details/)
  assert.equal(state.previewObjects.value[0].type, 'pdf')
})

test('the saved disabled preference prevents filling and enabling later uses the selected file', async () => {
  const { form, state, choose } = setup({ autoFillEnabled: false, savePreference: async () => ({ data: { success: true, auto_fill: true } }) })
  await choose('letter.pdf')
  assert.equal(form.title, '')
  assert.equal(form.extracted_text, text)
  await state.toggleAutoFill()
  assert.equal(form.title, 'Request for certified copies')
  assert.equal(form.subject, 'Ipil')
})

test('late extraction results do not overwrite manual input', async () => {
  let finish
  const { form, choose } = setup({ extractPDFText: () => new Promise(resolve => { finish = resolve }) })
  const pending = choose('letter.pdf')
  form.title = 'My own title'
  form.subject = 'Alicia'
  finish(text)
  await pending
  assert.equal(form.title, 'My own title')
  assert.equal(form.subject, 'Alicia')
  assert.equal(form.date_received, '2022-01-12')
})

test('edit forms protect saved details even when auto-fill is explicitly enabled', async () => {
  const { form, state, choose } = setup({ editing: true, savePreference: async () => ({ data: { success: true, auto_fill: true } }) })
  Object.assign(form, { title: 'Saved title', subject: 'Siay', year: 2020, date_received: '2020-05-01' })
  await choose('letter.pdf')
  await state.toggleAutoFill()
  assert.equal(form.title, 'Saved title')
  assert.equal(form.subject, 'Siay')
  assert.equal(form.year, 2020)
  assert.equal(form.date_received, '2020-05-01')
})

test('a PDF with no text explains the limitation and labels filename-only suggestions', async () => {
  const { form, state, choose } = setup({ extractPDFText: async () => '' })
  await choose('2022,amending-sp-ordinance.pdf')
  assert.equal(form.title, '2022 amending sp ordinance')
  assert.equal(form.year, 2022)
  assert.equal(form.subject, '')
  assert.equal(form.date_received, '')
  assert.match(state.autoFillMessage.value, /filename only/)
  assert.match(state.autoFillMessage.value, /scanned documents need OCR/)
})

test('approval dates and towns mentioned in the body are not guessed as received metadata', () => {
  const result = parseMunicipalCommunication('AN ORDINANCE AMENDING THE RULES\nSECTION 1. Applies to Siay and Ipil.\nAPPROVED: January 12, 2022', 'ordinance.pdf')
  assert.equal(result.title, 'AN ORDINANCE AMENDING THE RULES')
  assert.equal(result.subject, undefined)
  assert.equal(result.date_received, undefined)
  assert.equal(result.year, undefined)
})

test('ambiguous municipalities, impossible dates and overlong titles are handled safely', () => {
  const result = parseMunicipalCommunication(`Municipality of Ipil\nMunicipality of Siay\nSubject: ${'x'.repeat(400)}\nDate Received: February 30, 2022`, 'letter.pdf')
  assert.equal(result.subject, undefined)
  assert.equal(result.date_received, undefined)
  assert.equal(result.title.length, 255)
  const form = { title: 'Entered manually', year: 2019 }
  assert.deepEqual(fillMunicipalCommunication(form, { title: 'Extracted', year: 2022 }, { ...form }), [])
})

test('real PDF.js text extraction feeds the municipal field-filling flow', async () => {
  const { getDocument } = await import('pdfjs-dist/legacy/build/pdf.mjs')
  const stream = 'BT /F1 12 Tf 50 750 Td (Municipality of Ipil) Tj 0 -20 Td (Subject: Request for certified copies) Tj 0 -20 Td (Date Received: January 12, 2022) Tj ET'
  const objects = [
    '<< /Type /Catalog /Pages 2 0 R >>',
    '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
    '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
    '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
    `<< /Length ${stream.length} >>\nstream\n${stream}\nendstream`,
  ]
  let pdf = '%PDF-1.4\n'
  const offsets = [0]
  objects.forEach((object, i) => { offsets.push(pdf.length); pdf += `${i + 1} 0 obj\n${object}\nendobj\n` })
  const xref = pdf.length
  pdf += `xref\n0 6\n0000000000 65535 f \n${offsets.slice(1).map(offset => `${String(offset).padStart(10, '0')} 00000 n \n`).join('')}trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n${xref}\n%%EOF`
  const { form, choose } = setup({ extractPDFText: async () => {
    const task = getDocument({ data: new TextEncoder().encode(pdf), useSystemFonts: true })
    try {
      const document = await task.promise
      const content = await (await document.getPage(1)).getTextContent()
      return content.items.map(item => item.str + (item.hasEOL ? '\n' : ' ')).join('')
    } finally { await task.destroy() }
  } })
  await choose('actual-letter.pdf')
  assert.equal(form.title, 'Request for certified copies')
  assert.equal(form.subject, 'Ipil')
  assert.equal(form.date_received, '2022-01-12')
  assert.equal(form.year, 2022)
})
