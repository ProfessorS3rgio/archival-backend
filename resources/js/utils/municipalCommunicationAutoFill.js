export const municipalities = ['Alicia', 'Buug', 'Diplahan', 'Imelda', 'Ipil', 'Kabasalan', 'Mabuhay', 'Malangas', 'Naga', 'Olutanga', 'Payao', 'Roseller Lim', 'Siay', 'Talusan', 'Titay', 'Tungawan']

const months = ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december']
const clean = value => value.replace(/\s+/g, ' ').trim()

function receivedDate(text) {
  const line = text.match(/(?:date\s+received|received(?:\s+on)?)\s*[:\-]\s*([^\n]+)/i)?.[1]
  if (!line) return null
  let year, month, day
  let match = line.match(/\b(\d{4})-(\d{1,2})-(\d{1,2})\b/)
  if (match) [, year, month, day] = match
  else if ((match = line.match(/\b(\d{1,2})\/(\d{1,2})\/(\d{4})\b/))) [, month, day, year] = match
  else if ((match = line.match(/\b([a-z]+)\s+(\d{1,2}),?\s+(\d{4})\b/i))) {
    month = months.indexOf(match[1].toLowerCase()) + 1
    day = match[2]; year = match[3]
  } else if ((match = line.match(/\b(\d{1,2})\s+([a-z]+),?\s+(\d{4})\b/i))) {
    day = match[1]; month = months.indexOf(match[2].toLowerCase()) + 1; year = match[3]
  }
  if (!year || !month || !day) return null
  const date = new Date(Date.UTC(Number(year), Number(month) - 1, Number(day)))
  if (date.getUTCFullYear() !== Number(year) || date.getUTCMonth() + 1 !== Number(month) || date.getUTCDate() !== Number(day)) return null
  return date.toISOString().slice(0, 10)
}

export function parseMunicipalCommunication(text = '', filename = '') {
  const result = {}
  const normalized = text.replace(/\r\n?/g, '\n')
  const labelledTitle = normalized.match(/(?:^|\n)\s*(?:title|subject|re)\s*:\s*([^\n]+)/i)?.[1]
  const legislativeTitle = normalized.match(/(?:^|\n)\s*((?:AN?\s+)(?:ORDINANCE|RESOLUTION)\b[\s\S]*?)(?=\n\s*(?:WHEREAS|BE\s+IT|SECTION|APPROVED|ADOPTED)\b|$)/i)?.[1]
  const filenameTitle = clean(filename.replace(/\.[^.]+$/, '').replace(/[_,-]+/g, ' '))
  const title = clean(labelledTitle || legislativeTitle || filenameTitle)
  if (title) result.title = [...title].slice(0, 255).join('')

  // Match municipal letterheads/labels rather than guessing from towns mentioned in the body.
  const candidates = new Set()
  for (const municipality of municipalities) {
    const name = municipality.replace(/ /g, '\\s+')
    const pattern = new RegExp(`(?:municipality\\s+of|municipal\\s+government\\s+of|municipality\\s*:)\\s*${name}\\b`, 'i')
    if (pattern.test(normalized)) candidates.add(municipality)
  }
  if (candidates.size === 1) result.subject = [...candidates][0]
  if (!result.subject && labelledTitle) {
    const exact = municipalities.find(name => name.toLowerCase() === clean(labelledTitle).toLowerCase())
    if (exact) result.subject = exact
  }

  const date = receivedDate(normalized)
  if (date) result.date_received = date
  const year = date?.slice(0, 4) || normalized.match(/(?:^|\n)\s*year\s*:\s*((?:19|20)\d{2})\b/i)?.[1] || filename.match(/^((?:19|20)\d{2})(?:\D|$)/)?.[1]
  if (year && Number(year) >= 1900 && Number(year) <= new Date().getFullYear() + 10) result.year = Number(year)
  return result
}

export function fillMunicipalCommunication(form, details, baseline, { editing = false } = {}) {
  const filled = []
  for (const [field, value] of Object.entries(details)) {
    // Do not replace a user's input, including edits made while extraction was running.
    if (form[field] !== baseline[field]) continue
    const blank = form[field] === '' || form[field] == null
    const defaultYear = field === 'year' && !editing && String(form[field]) === String(new Date().getFullYear())
    if (blank || defaultYear) {
      form[field] = value
      filled.push(field)
    }
  }
  return filled
}
