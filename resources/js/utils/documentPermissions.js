export function canDeleteDocument(user, document, ownerField = 'added_by') {
  if (!user?.id || !document) return false
  if (user.role === 'admin') return true
  const ownerId = document[ownerField]
  return user.role === 'staff' && ownerId != null && String(ownerId) === String(user.id)
}
