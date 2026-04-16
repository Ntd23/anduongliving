import { ref, onMounted, onUnmounted } from 'vue'

export const useIntersectionObserver = (options?: {
  threshold?: number
  triggerOnce?: boolean
}) => {
  const target = ref<HTMLElement | null>(null)
  const isIntersecting = ref(false)

  let observer: IntersectionObserver | null = null

  onMounted(() => {
    observer = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) {
          isIntersecting.value = true

          if (options?.triggerOnce) {
            observer?.disconnect()
          }
        } else {
          if (!options?.triggerOnce) {
            isIntersecting.value = false
          }
        }
      },
      {
        threshold: options?.threshold || 0.1,
      }
    )

    if (target.value) {
      observer.observe(target.value)
    }
  })

  onUnmounted(() => {
    observer?.disconnect()
  })

  return {
    target,
    isIntersecting,
  }
}