import { computed } from 'vue'

// Advanced search function for better document discovery
export const performAdvancedSearch = (documents, query) => {
  if (!query.trim()) return documents

  const searchTerms = query.toLowerCase().split(/\s+/).filter(term => term.length > 0)
  const results = []

  documents.forEach(doc => {
    // Skip documents with missing required fields
    if (!doc.title || !doc.type) return
    
    let totalScore = 0
    const title = doc.title.toLowerCase()
    const type = doc.type.toLowerCase()
    const typeLabel = getTypeLabel(doc.type).toLowerCase()
    const dateStr = formatDate(doc.created_at).toLowerCase()
    const extractedText = doc.extracted_text ? doc.extracted_text.toLowerCase() : ''

    // Search each term across multiple fields
    searchTerms.forEach(term => {
      let termScore = 0

      // Exact title match (highest priority)
      if (title === term) {
        termScore += 100
      }
      // Title starts with term
      else if (title.startsWith(term)) {
        termScore += 80
      }
      // Title contains term as whole word
      else if (title.includes(` ${term} `) || title.startsWith(`${term} `) || title.endsWith(` ${term}`)) {
        termScore += 60
      }
      // Title contains term anywhere
      else if (title.includes(term)) {
        termScore += 40
      }
      // Fuzzy matching in title (allowing for typos)
      else if (calculateLevenshteinDistance(title, term) <= Math.max(1, term.length * 0.3)) {
        termScore += 20
      }

      // Type matching
      if (type.includes(term) || typeLabel.includes(term)) {
        termScore += 30
      }

      // Date matching
      if (dateStr.includes(term)) {
        termScore += 25
      }

      // Extracted text matching (comprehensive full-text search)
      if (extractedText.includes(term)) {
        termScore += 10
      }

      // Partial word matching in title (for incomplete words)
      const titleWords = title.split(/\s+/)
      titleWords.forEach(word => {
        if (word.startsWith(term) && word !== term) {
          termScore += 15
        }
      })

      totalScore += termScore
    })

    // Only include documents with a minimum score
    if (totalScore > 0) {
      results.push({
        ...doc,
        searchScore: totalScore,
        searchHighlights: getSearchHighlights(doc.title, searchTerms)
      })
    }
  })

  return results
}

// Calculate Levenshtein distance for fuzzy matching
export const calculateLevenshteinDistance = (str1, str2) => {
  const matrix = []

  for (let i = 0; i <= str2.length; i++) {
    matrix[i] = [i]
  }

  for (let j = 0; j <= str1.length; j++) {
    matrix[0][j] = j
  }

  for (let i = 1; i <= str2.length; i++) {
    for (let j = 1; j <= str1.length; j++) {
      if (str2.charAt(i - 1) === str1.charAt(j - 1)) {
        matrix[i][j] = matrix[i - 1][j - 1]
      } else {
        matrix[i][j] = Math.min(
          matrix[i - 1][j - 1] + 1, // substitution
          matrix[i][j - 1] + 1,     // insertion
          matrix[i - 1][j] + 1      // deletion
        )
      }
    }
  }

  return matrix[str2.length][str1.length]
}

// Get search highlights for display
export const getSearchHighlights = (text, searchTerms) => {
  const highlights = []
  const lowerText = text.toLowerCase()

  searchTerms.forEach(term => {
    let index = lowerText.indexOf(term)
    while (index !== -1) {
      highlights.push({
        start: index,
        end: index + term.length,
        term: term
      })
      index = lowerText.indexOf(term, index + 1)
    }
  })

  return highlights
}

// Format date utility
export const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

// Get type label utility
export const getTypeLabel = (type) => {
  const labels = {
    'ordinance': 'Ordinance',
    'resolution': 'Resolution',
    'draft-ordinance': 'Draft Ordinance',
    'draft-resolution': 'Draft Resolution',
    'accomplishment': 'Accomplishment',
    'project': 'Project',
    'moa': 'MOA',
    'municipal-communication': 'Municipal Communication',
    'national-office': 'National Office',
    'provincial-office': 'Provincial Office'
  }
  return labels[type] || type.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
}

// Get view URL utility
export const getViewUrl = (document, searchQuery = '') => {
  const routes = {
    'ordinance': `/dashboard/ordinances/${document.id}`,
    'resolution': `/dashboard/resolutions/${document.id}`,
    'draft-ordinance': `/dashboard/draft-ordinances/${document.id}`,
    'draft-resolution': `/dashboard/draft-resolutions/${document.id}`,
    'accomplishment': `/accomplishments/${document.id}`,
    'project': `/projects/${document.id}`,
    'moa': `/memorandum-of-agreements/${document.id}`,
    'municipal-communication': `/municipal-communications/${document.id}`,
    'national-office-communication': `/national-office-communications/${document.id}`,
    'provincial-office': `/provincial-offices/${document.id}`,
    'committee-report': `/committee-reports/${document.id}`
  }
  
  const baseUrl = routes[document.type] || '#'
  if (searchQuery && baseUrl !== '#') {
    return `${baseUrl}?search=${encodeURIComponent(searchQuery)}`
  }
  return baseUrl
}

// Get type icon utility
export const getTypeIcon = (type) => {
  const icons = {
    'ordinance': 'DocsIcon',
    'resolution': 'DocsIcon',
    'draft-ordinance': 'DraftIcon',
    'draft-resolution': 'DraftIcon',
    'accomplishment': 'PieChartIcon',
    'project': 'project-icon',
    'moa': 'moa-icon',
    'municipal-communication': 'chat-icon',
    'national-office': 'office-icon',
    'provincial-office': 'office-icon'
  }
  return icons[type] || 'DocsIcon'
}

// Get type icon class utility
export const getTypeIconClass = (type) => {
  const classes = {
    'ordinance': 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
    'resolution': 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
    'draft-ordinance': 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400',
    'draft-resolution': 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
    'accomplishment': 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400',
    'project': 'bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400',
    'moa': 'bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400',
    'municipal-communication': 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400',
    'national-office': 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400',
    'provincial-office': 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400'
  }
  return classes[type] || 'bg-gray-100 dark:bg-gray-900/30 text-gray-600 dark:text-gray-400'
}

// Main composable for document filtering
export const useDocumentFilter = (allDocuments, searchQuery, selectedType, selectedYear, selectedAuthor) => {
  const filteredDocuments = computed(() => {
    let allDocs = []

    // Flatten all document types into a single array
    Object.keys(allDocuments.value || {}).forEach(type => {
      if (allDocuments.value[type]) {
        allDocuments.value[type].forEach(doc => {
          allDocs.push(doc)
        })
      }
    })

    // Filter by search query using advanced search
    if (searchQuery.value?.trim()) {
      allDocs = performAdvancedSearch(allDocs, searchQuery.value.trim())
    }

    // Filter by selected type
    if (selectedType.value) {
      allDocs = allDocs.filter(doc => doc.type === selectedType.value)
    }

    // Filter by selected year
    if (selectedYear.value) {
      allDocs = allDocs.filter(doc => {
        const docDate = doc.date_approved || doc.created_at
        if (!docDate) return false
        const year = new Date(docDate).getFullYear().toString()
        return year === selectedYear.value
      })
    }

    // Filter by selected author
    if (selectedAuthor.value) {
      allDocs = allDocs.filter(doc => {
        if (!doc.author) return false
        const authors = doc.author.split(';').map(a => a.trim())
        return authors.some(a => a.toLowerCase() === selectedAuthor.value.toLowerCase())
      })
    }

    // Sort by relevance score (if searching) or by creation date (newest first)
    if (searchQuery.value?.trim()) {
      return allDocs.sort((a, b) => (b.searchScore || 0) - (a.searchScore || 0))
    } else {
      return allDocs.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
    }
  })

  return {
    filteredDocuments
  }
}