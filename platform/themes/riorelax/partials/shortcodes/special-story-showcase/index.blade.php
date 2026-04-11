@php
    $bgImage = $shortcode->background_image ? RvMedia::getImageUrl($shortcode->background_image) : null;
    $image1  = $shortcode->image_1 ? RvMedia::getImageUrl($shortcode->image_1) : null;
    $image2  = $shortcode->image_2 ? RvMedia::getImageUrl($shortcode->image_2) : null;
@endphp

<section class="story" @if($bgImage) style="background-image:url('{{ $bgImage }}')" @endif>
    <style>
        /* ── SECTION ──────────────────────────────────────────── */
        .story {
            position: relative;
            background-color: #1e1d18;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            padding: 64px 48px 56px;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-family: 'Yu Mincho', 'Noto Serif JP', 'Times New Roman', Georgia, serif;
            overflow: hidden;
            color: #fff;
        }

        /* ── DECORATIVE BG TEXT ────────────────────────────────── */
        .story__deco {
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            font-family: 'Playfair Display', 'Georgia', serif;
            font-style: italic;
            font-size: clamp(80px, 12vw, 180px);
            font-weight: 400;
            color: rgba(255, 255, 255, 0.04);
            white-space: nowrap;
            pointer-events: none;
            z-index: 0;
            letter-spacing: 0.04em;
        }

        .story > *:not(.story__deco) {
            position: relative;
            z-index: 1;
        }

        /* ── HEADER ───────────────────────────────────────────── */
        .story__label {
            display: block;
            color: rgba(255, 255, 255, 0.6);
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 12px;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            margin-bottom: 28px;
            text-align: center;
        }

        .story__desc {
            color: rgba(255, 255, 255, 0.75);
            font-size: 13px;
            line-height: 2.2;
            margin: 0 0 48px;
            max-width: 700px;
            text-align: center;
            text-wrap: balance;
        }

        /* ── IMAGE CARDS ──────────────────────────────────────── */
        .story__cards {
            display: flex;
            gap: 6px;
            width: 100%;
            max-width: 1100px;
            margin-bottom: 36px;
        }

        .story__card {
            flex: 1;
            position: relative;
            overflow: hidden;
            min-height: 340px;
        }

        .story__card img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* dark gradient overlay on each card */
        .story__card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top,
                rgba(20, 18, 12, 0.6) 0%,
                rgba(20, 18, 12, 0.1) 50%,
                rgba(20, 18, 12, 0.15) 100%);
            pointer-events: none;
        }

        .story__card-title {
            position: absolute;
            bottom: 24px;
            right: 28px;
            z-index: 2;
            color: #fff;
            font-size: clamp(16px, 1.8vw, 24px);
            font-weight: 400;
            letter-spacing: 0.1em;
            line-height: 1.6;
            text-align: right;
            writing-mode: vertical-rl;
            text-orientation: mixed;
        }

        /* ── NAV ROW ──────────────────────────────────────────── */
        .story__nav {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            width: 100%;
            max-width: 1100px;
            margin-bottom: 36px;
        }

        .story__nav-item {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 16px 24px;
        }

        .story__nav-item:first-child {
            justify-content: flex-end;
            text-align: right;
        }

        .story__nav-item:last-child {
            justify-content: flex-start;
            text-align: left;
        }

        .story__nav-text {
            color: rgba(255, 255, 255, 0.7);
            font-size: 12px;
            line-height: 1.9;
            max-width: 260px;
        }

        .story__nav-badge {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 10px 14px;
            flex-shrink: 0;
            text-align: center;
        }

        .story__nav-badge-top {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 8px;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.5);
            line-height: 1.4;
        }

        .story__nav-badge-vol {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 11px;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.7);
        }

        /* ── CTA BUTTON ───────────────────────────────────────── */
        .story__btn {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: rgba(255, 255, 255, 0.8);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: inherit;
            font-size: 13px;
            letter-spacing: 0.06em;
            min-width: 240px;
            padding: 14px 28px;
            justify-content: center;
            text-decoration: none;
            transition: all 0.22s;
        }

        .story__btn:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.5);
            color: #fff;
        }

        .story__btn-arrow {
            font-size: 10px;
            transition: transform 0.22s;
        }

        .story__btn:hover .story__btn-arrow {
            transform: translateY(2px);
        }

        /* ── RESPONSIVE ───────────────────────────────────────── */
        @media (max-width: 991px) {
            .story {
                padding: 48px 24px 40px;
            }

            .story__cards {
                flex-direction: column;
                gap: 8px;
            }

            .story__card {
                min-height: 260px;
            }

            .story__nav {
                flex-direction: column;
                gap: 16px;
            }

            .story__nav-item {
                justify-content: center !important;
                text-align: center !important;
            }
        }

        @media (max-width: 640px) {
            .story__deco {
                font-size: 60px;
            }

            .story__card-title {
                font-size: 16px;
            }
        }
    </style>

    {{-- Decorative background text --}}
    @if($shortcode->decorative_text)
        <span class="story__deco">{{ $shortcode->decorative_text }}</span>
    @endif

    {{-- Label --}}
    @if($shortcode->section_label)
        <span class="story__label">{!! BaseHelper::clean($shortcode->section_label) !!}</span>
    @endif

    {{-- Description --}}
    @if($shortcode->description)
        <p class="story__desc">{!! BaseHelper::clean($shortcode->description) !!}</p>
    @endif

    {{-- 2 Image cards with titles --}}
    <div class="story__cards">
        @if($image1)
            <div class="story__card">
                <img src="{{ $image1 }}" alt="">
                @if($shortcode->image_1_title)
                    <span class="story__card-title">{{ $shortcode->image_1_title }}</span>
                @endif
            </div>
        @endif
        @if($image2)
            <div class="story__card">
                <img src="{{ $image2 }}" alt="">
                @if($shortcode->image_2_title)
                    <span class="story__card-title">{{ $shortcode->image_2_title }}</span>
                @endif
            </div>
        @endif
    </div>

    {{-- Navigation row --}}
    <div class="story__nav">
        <div class="story__nav-item">
            @if($shortcode->nav_text_1)
                <span class="story__nav-text">{!! BaseHelper::clean($shortcode->nav_text_1) !!}</span>
            @endif
            <div class="story__nav-badge">
                <span class="story__nav-badge-top">SPECIAL<br>STORY</span>
                <span class="story__nav-badge-vol">VOL.1</span>
            </div>
        </div>
        <div class="story__nav-item">
            <div class="story__nav-badge">
                <span class="story__nav-badge-top">SPECIAL<br>STORY</span>
                <span class="story__nav-badge-vol">VOL.2</span>
            </div>
            @if($shortcode->nav_text_2)
                <span class="story__nav-text">{!! BaseHelper::clean($shortcode->nav_text_2) !!}</span>
            @endif
        </div>
    </div>

    {{-- CTA button --}}
    @if($shortcode->button_label && $shortcode->button_url)
        <a href="{{ $shortcode->button_url }}" class="story__btn">
            {{ $shortcode->button_label }}
            <span class="story__btn-arrow">▼</span>
        </a>
    @endif

</section>
