<section id="home" class="slider-area slider-showcase-area fix p-relative">
    <style>
        .header-three .menu-area:not(.sticky-menu),
        .header-three .menu-area:not(.sticky-menu) .second-menu,
        .header-three .menu-area:not(.sticky-menu) .container,
        .header-three .menu-area:not(.sticky-menu) .container-fluid {
            background: transparent !important;
            box-shadow: none !important;
        }

        .slider-showcase-area,
        .slider-showcase-active {
            background: #000;
        }

        .slider-showcase-active {
            overflow: hidden;
        }

        .slider-showcase-slide {
            align-items: stretch !important;
            background: #000 !important;
            display: flex !important;
            justify-content: center;
            min-height: min(100vh, 1200px) !important;
            overflow: hidden;
            position: relative;
        }

        .slider-showcase-slide::after {
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.04) 0%, rgba(0, 0, 0, 0.18) 100%);
            content: "";
            inset: 0;
            position: absolute;
            z-index: 1;
        }

        .slider-showcase-media {
            display: block;
            height: 100%;
            inset: 0;
            overflow: hidden;
            position: absolute;
            width: 100%;
            z-index: 0;
        }

        .slider-showcase-media img {
            display: block;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: opacity 1.6s ease, transform 6s ease;
            width: 100%;
        }

        .slider-showcase-slide:not(.slick-active) .slider-showcase-media img {
            opacity: 0;
        }

        .slider-showcase-slide.slick-active .slider-showcase-media img {
            opacity: 1;
            transform: scale(1.03);
        }

        .slider-showcase-overlay {
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.02) 0%, rgba(0, 0, 0, 0.08) 42%, rgba(0, 0, 0, 0.22) 100%);
            inset: 0;
            pointer-events: none;
            position: absolute;
            z-index: 2;
        }

        .slider-showcase-content {
            align-items: center;
            display: flex;
            inset: 0;
            justify-content: center;
            padding: 42px 32px 28px;
            pointer-events: none;
            position: absolute;
            text-align: center;
            z-index: 3;
        }

        .slider-showcase-title {
            color: #fff;
            font-size: clamp(22px, 2.3vw, 42px);
            letter-spacing: 0.08em;
            margin: 0;
            max-width: min(90vw, 980px);
            text-shadow: 0 8px 24px rgba(0, 0, 0, 0.45);
        }

        .slider-showcase-caption {
            align-self: flex-end;
            color: rgba(255, 255, 255, 0.96);
            font-size: clamp(15px, 1.15vw, 24px);
            letter-spacing: 0.08em;
            line-height: 1.6;
            margin: 0;
            max-width: min(92vw, 1100px);
            text-shadow: 0 6px 18px rgba(0, 0, 0, 0.72);
        }

        .slider-showcase-active .slick-arrow,
        .slider-showcase-active .slick-dots {
            display: none !important;
        }

        @media (max-width: 991px) {
            .slider-showcase-slide {
                min-height: min(82vh, 820px) !important;
            }

            .slider-showcase-content {
                padding: 24px 16px 22px;
            }

            .slider-showcase-title {
                letter-spacing: 0.04em;
            }

            .slider-showcase-caption {
                letter-spacing: 0.03em;
            }
        }
    </style>

    <div class="slider-active slider-showcase-active">
        @foreach($sliders as $slider)
            @php
                $imageUrl = RvMedia::getImageUrl($slider->image);
                $title = BaseHelper::clean($slider->title);
                $subtitle = BaseHelper::clean($slider->getMetaData('subtitle', true));
                $description = BaseHelper::clean($slider->description);
                $buttonPrimaryUrl = $slider->getMetaData('button_primary_url', true);
                $caption = $subtitle ?: ($description ?: $title);
            @endphp

            <div class="single-slider slider-bg slider-showcase-slide">
                @if ($buttonPrimaryUrl)
                    <a href="{{ $buttonPrimaryUrl }}" class="slider-showcase-media" aria-label="{{ strip_tags($title ?: $caption ?: __('View slide')) }}">
                        <img src="{{ $imageUrl }}" alt="{{ strip_tags($title ?: $caption ?: 'Slider image') }}">
                    </a>
                @else
                    <div class="slider-showcase-media">
                        <img src="{{ $imageUrl }}" alt="{{ strip_tags($title ?: $caption ?: 'Slider image') }}">
                    </div>
                @endif

                <div class="slider-showcase-overlay"></div>

                @if ($caption)
                    <div class="slider-showcase-content">
                        <p class="slider-showcase-caption" data-animation="fadeInUp" data-delay=".3s">{!! $caption !!}</p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</section>

<script>
    window.addEventListener('load', function () {
        var $slider = $('.slider-showcase-active');

        if (!$slider.length || typeof $slider.slick !== 'function') {
            return;
        }

        function runSliderAnimations(elements) {
            var animationEndEvents = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';

            elements.each(function () {
                var $element = $(this);
                var animationDelay = $element.data('delay');
                var animationType = 'animated ' + $element.data('animation');

                $element.css({
                    'animation-delay': animationDelay,
                    '-webkit-animation-delay': animationDelay,
                });

                $element.addClass(animationType).one(animationEndEvents, function () {
                    $element.removeClass(animationType);
                });
            });
        }

        if ($slider.hasClass('slick-initialized')) {
            $slider.slick('unslick');
        }

        $slider.on('init', function () {
            runSliderAnimations($slider.find('.single-slider:first-child [data-animation]'));
        });

        $slider.on('beforeChange', function (event, slick, currentSlide, nextSlide) {
            runSliderAnimations($slider.find('.single-slider[data-slick-index="' + nextSlide + '"] [data-animation]'));
        });

        $slider.slick({
            autoplay: true,
            autoplaySpeed: 6000,
            dots: false,
            fade: true,
            arrows: false,
            infinite: true,
            speed: 1600,
            cssEase: 'ease-in-out',
            pauseOnHover: false,
            pauseOnFocus: false,
            swipe: false,
            draggable: false,
            touchMove: false,
            rtl: typeof RiorelaxTheme !== 'undefined' && typeof RiorelaxTheme.isRtl === 'function' ? RiorelaxTheme.isRtl() : false,
        });
    });
</script>
