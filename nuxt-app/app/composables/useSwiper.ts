import { ref, onMounted, onUnmounted, type Ref } from 'vue'
import { Swiper as SwiperClass } from 'swiper'
import { Pagination, Navigation, Autoplay } from 'swiper/modules'

export interface SwiperOptions {
  slidesPerView?: number
  spaceBetween?: number
  slidesPerGroup?: number
  loop?: boolean
  autoplay?: boolean | {
    delay: number
    disableOnInteraction?: boolean
  }
  pagination?: boolean | {
    clickable?: boolean
    dynamicBullets?: boolean
  }
  navigation?: boolean
  modules?: any[]
  breakpoints?: Record<number, Partial<SwiperOptions>>
}

export interface UseSwiperReturn {
  swiperRef: Ref<SwiperClass | undefined>
  isAtStart: Ref<boolean>
  isAtEnd: Ref<boolean>
  currentIndex: Ref<number>
  totalSlides: Ref<number>
  goPrev: () => void
  goNext: () => void
  goToSlide: (index: number) => void
  initSwiper: (swiper: SwiperClass) => void
  destroySwiper: () => void
}

export function useSwiper(options: SwiperOptions = {}): UseSwiperReturn {
  // Default options for mobile
  const defaultOptions: SwiperOptions = {
    slidesPerView: 1,
    spaceBetween: 20,
    slidesPerGroup: 1,
    loop: false,
    pagination: {
      clickable: true,
      dynamicBullets: true
    },
    navigation: false,
    modules: [Pagination, Navigation, Autoplay],
    breakpoints: {
      640: {
        slidesPerView: 2,
        spaceBetween: 30
      },
      768: {
        slidesPerView: 3,
        spaceBetween: 40
      },
      1024: {
        slidesPerView: options.slidesPerView || 4,
        spaceBetween: options.spaceBetween || 50
      }
    }
  }

  // Merge user options with defaults
  const mergedOptions = { ...defaultOptions, ...options }

  // Reactive state
  const swiperRef = ref<SwiperClass>()
  const isAtStart = ref(true)
  const isAtEnd = ref(false)
  const currentIndex = ref(0)
  const totalSlides = ref(0)

  // Initialize swiper
  const initSwiper = (swiper: SwiperClass) => {
    swiperRef.value = swiper
    totalSlides.value = swiper.slides.length

    // Update button states
    const updateButtonStates = () => {
      isAtStart.value = swiper.isBeginning
      isAtEnd.value = swiper.isEnd
      currentIndex.value = swiper.activeIndex
    }

    // Initial state
    updateButtonStates()

    // Event listeners
    swiper.on('slideChange', updateButtonStates)
    swiper.on('reachBeginning', () => {
      isAtStart.value = true
      currentIndex.value = 0
    })
    swiper.on('reachEnd', () => {
      isAtEnd.value = true
      currentIndex.value = swiper.slides.length - 1
    })
  }

  // Navigation methods
  const goPrev = () => {
    if (swiperRef.value && !isAtStart.value) {
      swiperRef.value.slidePrev()
    }
  }

  const goNext = () => {
    if (swiperRef.value && !isAtEnd.value) {
      swiperRef.value.slideNext()
    }
  }

  const goToSlide = (index: number) => {
    if (swiperRef.value && index >= 0 && index < totalSlides.value) {
      swiperRef.value.slideTo(index)
    }
  }

  // Cleanup
  const destroySwiper = () => {
    if (swiperRef.value) {
      swiperRef.value.off('slideChange')
      swiperRef.value.off('reachBeginning')
      swiperRef.value.off('reachEnd')
      swiperRef.value.destroy(true, true)
      swiperRef.value = undefined
    }
  }

  // Auto cleanup on unmount
  onUnmounted(() => {
    destroySwiper()
  })

  return {
    swiperRef,
    isAtStart,
    isAtEnd,
    currentIndex,
    totalSlides,
    goPrev,
    goNext,
    goToSlide,
    initSwiper,
    destroySwiper
  }
}

// Mobile-specific composable
export function useMobileSwiper(items: any[] = [], options: SwiperOptions = {}) {
  const swiperOptions: SwiperOptions = {
    slidesPerView: 1,
    spaceBetween: 15,
    slidesPerGroup: 1,
    loop: false,
    pagination: {
      clickable: true,
      dynamicBullets: false
    },
    navigation: false,
    breakpoints: {
      640: {
        slidesPerView: 2,
        spaceBetween: 20
      },
      768: {
        slidesPerView: 3,
        spaceBetween: 25
      }
    },
    ...options
  }

  const swiper = useSwiper(swiperOptions)

  // Auto-disable on desktop
  const shouldUseSwiper = ref(false)
  
  const checkScreenSize = () => {
    shouldUseSwiper.value = window.innerWidth < 768
  }

  onMounted(() => {
    checkScreenSize()
    window.addEventListener('resize', checkScreenSize)
  })

  onUnmounted(() => {
    window.removeEventListener('resize', checkScreenSize)
  })

  return {
    ...swiper,
    shouldUseSwiper,
    items
  }
}

// Grid/Switch composable - automatically switch between grid and swiper
export function useResponsiveSwiper(gridColumns = 3, swiperOptions: SwiperOptions = {}) {
  const swiper = useSwiper(swiperOptions)
  const isMobile = ref(false)
  const gridCols = ref(gridColumns)

  const checkResponsive = () => {
    const width = window.innerWidth
    isMobile.value = width < 768
    
    // Adjust grid columns based on screen size
    if (width < 640) {
      gridCols.value = 1
    } else if (width < 768) {
      gridCols.value = 2
    } else if (width < 1024) {
      gridCols.value = 2
    } else {
      gridCols.value = gridColumns
    }
  }

  onMounted(() => {
    checkResponsive()
    window.addEventListener('resize', checkResponsive)
  })

  onUnmounted(() => {
    window.removeEventListener('resize', checkResponsive)
  })

  return {
    ...swiper,
    isMobile,
    gridCols
  }
}
