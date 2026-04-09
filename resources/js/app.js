import './bootstrap';
import.meta.glob(['../img/**/*'], { eager: true });
import Alpine from 'alpinejs'
import collapse from '@alpinejs/collapse'

Alpine.plugin(collapse)

window.Alpine = Alpine

window.panelGlobalSearch = (config = {}) => ({
  q: config.initialQuery ?? '',
  open: false,
  loading: false,
  results: [],
  total: 0,
  activeIndex: -1,
  debounceTimer: null,
  minLength: config.minLength ?? 2,
  suggestionsUrl: config.suggestionsUrl ?? '',
  fullResultsUrl: config.fullResultsUrl ?? '',

  init() {
    if (this.q.trim().length >= this.minLength) {
      this.fetchSuggestions(false)
    }
  },

  onInput() {
    window.clearTimeout(this.debounceTimer)
    this.activeIndex = -1

    const value = this.q.trim()

    if (value.length < this.minLength) {
      this.open = false
      this.loading = false
      this.results = []
      this.total = 0
      return
    }

    this.debounceTimer = window.setTimeout(() => {
      this.fetchSuggestions(true)
    }, 180)
  },

  async fetchSuggestions(openOnSuccess = true) {
    const value = this.q.trim()

    if (value.length < this.minLength || !this.suggestionsUrl) {
      this.open = false
      this.loading = false
      this.results = []
      this.total = 0
      return
    }

    this.loading = true

    try {
      const url = new URL(this.suggestionsUrl, window.location.origin)
      url.searchParams.set('q', value)

      const response = await fetch(url.toString(), {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
      })

      if (!response.ok) {
        throw new Error('No se pudieron cargar las sugerencias')
      }

      const data = await response.json()
      this.results = Array.isArray(data.groups) ? data.groups : []
      this.total = Number(data.total ?? 0)
      this.open = openOnSuccess
    } catch (error) {
      this.results = []
      this.total = 0
      this.open = false
      console.error(error)
    } finally {
      this.loading = false
    }
  },

  flattenedItems() {
    return this.results.flatMap(group => group.items ?? [])
  },

  setActiveByOffset(offset) {
    const items = this.flattenedItems()

    if (!items.length) {
      this.activeIndex = -1
      return
    }

    if (this.activeIndex === -1) {
      this.activeIndex = offset > 0 ? 0 : items.length - 1
      return
    }

    this.activeIndex = (this.activeIndex + offset + items.length) % items.length
  },

  goToActiveItem() {
    const items = this.flattenedItems()
    const item = items[this.activeIndex]

    if (item?.url) {
      window.location.href = item.url
      return
    }

    this.goToFullResults()
  },

  goToFullResults() {
    const value = this.q.trim()

    if (value.length < this.minLength || !this.fullResultsUrl) {
      return
    }

    const url = new URL(this.fullResultsUrl, window.location.origin)
    url.searchParams.set('q', value)
    window.location.href = url.toString()
  },

  itemGlobalIndex(groupIndex, itemIndex) {
    let index = 0

    this.results.forEach((group, currentGroupIndex) => {
      if (currentGroupIndex < groupIndex) {
        index += (group.items ?? []).length
      }
    })

    return index + itemIndex
  },

  isActive(groupIndex, itemIndex) {
    return this.itemGlobalIndex(groupIndex, itemIndex) === this.activeIndex
  },

  close() {
    this.open = false
    this.activeIndex = -1
  },
})

Alpine.start()

document.addEventListener('DOMContentLoaded', () => {
  const items = document.querySelectorAll('.reveal')
  if (!items.length) return
  if (!('IntersectionObserver' in window)) return

  const io = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('is-revealed')
        io.unobserve(e.target)
      }
    })
  }, { threshold: 0.15 })

  items.forEach(el => io.observe(el))
})