import test from 'node:test'
import assert from 'node:assert/strict'
import { canDeleteDocument } from '../../resources/js/utils/documentPermissions.js'

test('staff delete controls are available only for their own records', () => {
  const staff = { id: 12, role: 'staff' }
  assert.equal(canDeleteDocument(staff, { added_by: 12 }), true)
  assert.equal(canDeleteDocument(staff, { added_by: '12' }), true)
  assert.equal(canDeleteDocument(staff, { added_by: 13 }), false)
  assert.equal(canDeleteDocument(staff, { added_by: null }), false)
  assert.equal(canDeleteDocument(staff, { uploaded_by: 12 }, 'uploaded_by'), true)
  assert.equal(canDeleteDocument(staff, { uploaded_by: 13 }, 'uploaded_by'), false)
})

test('administrators retain access; legislators, regular users, and guests cannot delete', () => {
  assert.equal(canDeleteDocument({ id: 1, role: 'admin' }, { added_by: 12 }), true)
  for (const role of ['legislator', 'user', '']) {
    assert.equal(canDeleteDocument({ id: 12, role }, { added_by: 12 }), false)
  }
  assert.equal(canDeleteDocument(null, { added_by: 12 }), false)
})
