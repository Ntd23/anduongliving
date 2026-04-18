export interface ScrollAnimationOptions {
  root?: Element | null
  rootMargin?: string
  threshold?: number | number[]
  animationClass?: string
  inClass?: string
  outClass?: string
}

export const useScrollAnimation = (options: ScrollAnimationOptions = {}) => {
  const {
    root = null,
    rootMargin = '0px',
    threshold = 0.1,
    animationClass = 'animate-on-scroll',
    inClass = 'animate-in',
    outClass = 'animate-out'
  } = options

  const setupScrollAnimations = () => {
    const observerOptions = {
      root,
      rootMargin,
      threshold
    }

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add(inClass)
          entry.target.classList.remove(outClass)
        } else {
          entry.target.classList.add(outClass)
          entry.target.classList.remove(inClass)
        }
      })
    }, observerOptions)

    const animatedElements = document.querySelectorAll(`.${animationClass}`)
    animatedElements.forEach((el) => observer.observe(el))

    return () => {
      observer.disconnect()
    }
  }

  const observeElement = (element: Element, customOptions?: ScrollAnimationOptions) => {
    const opts = { ...options, ...customOptions }
    
    const observerOptions = {
      root: opts.root,
      rootMargin: opts.rootMargin,
      threshold: opts.threshold
    }

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add(opts.inClass!)
          entry.target.classList.remove(opts.outClass!)
        } else {
          entry.target.classList.add(opts.outClass!)
          entry.target.classList.remove(opts.inClass!)
        }
      })
    }, observerOptions)

    observer.observe(element)

    return () => observer.disconnect()
  }

  const observeElements = (elements: NodeListOf<Element> | Element[], customOptions?: ScrollAnimationOptions) => {
    const cleanupFunctions: (() => void)[] = []
    
    elements.forEach((element) => {
      cleanupFunctions.push(observeElement(element, customOptions))
    })

    return () => {
      cleanupFunctions.forEach(cleanup => cleanup())
    }
  }

  return {
    setupScrollAnimations,
    observeElement,
    observeElements
  }
}
