import './bootstrap';
import.meta.glob(['../img/**/*'], { eager: true });
import Alpine from 'alpinejs'
import collapse from '@alpinejs/collapse'

Alpine.plugin(collapse)

window.Alpine = Alpine
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