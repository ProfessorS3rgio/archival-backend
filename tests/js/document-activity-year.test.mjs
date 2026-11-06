import test from 'node:test'
import assert from 'node:assert/strict'
import { ref } from 'vue'
import { getDocumentActivityYear, useDocumentFilter } from '../../resources/js/composables/useDocumentSearch.js'

test('year filtering accepts numeric and text selections and resets to all records', () => {
  const records = [
    { id: 1, activity_year: 2022, created_at: '2026-01-01' },
    { id: 2, activity_year: 2023, created_at: '2026-01-01' },
    { id: 3, activity_year: null, date_approved: '0003-03-21', created_at: '2026-01-01' },
  ]
  const year = ref(2022)
  const { filteredDocuments } = useDocumentFilter(ref({ ordinances: records }), ref(''), ref(''), year, ref(''))
  assert.deepEqual(filteredDocuments.value.map(doc => doc.id), [1])
  year.value = '2023'
  assert.deepEqual(filteredDocuments.value.map(doc => doc.id), [2])
  year.value = ''
  assert.equal(filteredDocuments.value.length, 3)
})

test('calendar years stay consistent at timezone boundaries and reject malformed dates', () => {
  assert.equal(getDocumentActivityYear({ date_approved: '2022-12-31T23:00:00-08:00' }), 2022)
  assert.equal(getDocumentActivityYear({ date_approved: null, created_at: '2026-01-01T00:00:00Z' }), 2026)
  for (const date of ['0005-12-02', '0003-03-21', '0000-00-00', '2025-02-30', 'invalid']) {
    assert.equal(getDocumentActivityYear({ date_approved: date, created_at: '2026-01-01' }), null)
  }
  assert.equal(getDocumentActivityYear({ activity_year: null, created_at: '2026-01-01' }), null)
})
