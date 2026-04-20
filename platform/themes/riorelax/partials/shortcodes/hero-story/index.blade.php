@php
    $backgroundImage = $shortcode->background_image ? RvMedia::getImageUrl($shortcode->background_image) : null;
    $foregroundImage = $shortcode->foreground_image ? RvMedia::getImageUrl($shortcode->foreground_image) : null;
@endphp

<section
    class="hero-story"
    @if ($backgroundImage)
        style="background-image: url('{{ $backgroundImage }}');"
    @endif
>
    <style>
        .hero-story {
            position: relative;
            overflow: hidden;
            min-height: 720px;
            padding: 120px 0 88px;
            background-color: #40372f;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .hero-story::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 18% 20%, rgba(185, 130, 90, 0.24), transparent 28%),
                linear-gradient(90deg, rgba(24, 18, 14, 0.76) 0%, rgba(24, 18, 14, 0.4) 48%, rgba(24, 18, 14, 0.14) 100%);
        }

        .hero-story .container {
            position: relative;
            z-index: 1;
        }

        .hero-story__grid {
            position: relative;
            min-height: 500px;
        }

        .hero-story__content {
            max-width: 640px;
            padding: 42px 40px;
            border: 1px solid rgba(255, 245, 232, 0.18);
            border-radius: 30px;
            background: rgba(41, 31, 24, 0.34);
            box-shadow: 0 28px 72px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            color: #fff8ef;
        }

        .hero-story__subtitle {
            display: block;
            margin-bottom: 14px;
            color: #e1c6a7;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .hero-story__title {
            margin: 0;
            color: #fff8ef;
            font-family: "Cormorant Garamond", Georgia, serif;
            font-size: clamp(48px, 7vw, 88px);
            font-weight: 600;
            line-height: 0.94;
            letter-spacing: -0.03em;
        }

        .hero-story__description {
            margin-top: 22px;
            color: rgba(255, 248, 239, 0.82);
            font-size: 17px;
            line-height: 1.95;
            white-space: pre-line;
        }

        .hero-story .slider-btn {
            margin-top: 28px;
        }

        .hero-story .slider-btn a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 54px;
            padding: 0 26px;
            border-radius: 999px;
            background: linear-gradient(135deg, #b9825a, #a16f4c);
            color: #fffaf4;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-decoration: none;
            box-shadow: 0 16px 36px rgba(185, 130, 90, 0.22);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hero-story .slider-btn a:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 42px rgba(185, 130, 90, 0.28);
        }

        .hero-story__aside {
            position: absolute;
            right: 0;
            bottom: -18px;
            width: min(100%, 280px);
            overflow: hidden;
            border: 8px solid rgba(255, 250, 244, 0.88);
            border-radius: 24px;
            box-shadow: 0 24px 54px rgba(18, 9, 5, 0.28);
        }

        .hero-story__aside img {
            display: block;
            width: 100%;
            aspect-ratio: 4 / 5;
            object-fit: cover;
        }

        @media (max-width: 991px) {
            .hero-story {
                min-height: 0;
                padding: 96px 0 72px;
            }

            .hero-story__grid {
                min-height: 0;
            }

            .hero-story__content {
                max-width: 100%;
                padding: 32px 24px;
            }

            .hero-story__aside {
                position: relative;
                right: auto;
                bottom: auto;
                margin: 24px 0 0 auto;
                width: min(100%, 240px);
            }
        }
    </style>

    <div class="container">
        <div class="hero-story__grid">
            <div class="hero-story__content hero-content">
                @if ($shortcode->subtitle)
                    <span class="hero-story__subtitle">{{ $shortcode->subtitle }}</span>
                @endif

                @if ($shortcode->title)
                    <h1 class="hero-story__title">{!! BaseHelper::clean($shortcode->title) !!}</h1>
                @endif

                @if ($shortcode->description)
                    <p class="hero-story__description">{!! BaseHelper::clean($shortcode->description) !!}</p>
                @endif

                @if ($shortcode->button_label && $shortcode->button_url)
                    <div class="slider-btn">
                        <a href="{{ $shortcode->button_url }}">
                            {{ $shortcode->button_label }}
                        </a>
                    </div>
                @endif
            </div>

            @if ($foregroundImage)
                <div class="hero-story__aside">
                    <img src="{{ $foregroundImage }}" alt="{{ $shortcode->title }}">
                </div>
            @endif
        </div>
    </div>
</section>
