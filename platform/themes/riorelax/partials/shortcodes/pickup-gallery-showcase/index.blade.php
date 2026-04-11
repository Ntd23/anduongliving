@php
    $fallbackImage = Theme::asset()->url('images/pickup-gallery-showcase/pickup-gallery-showcase .jpeg');
    $bgImage = $shortcode->background_image ? RvMedia::getImageUrl($shortcode->background_image) : $fallbackImage;
    $image1  = $shortcode->image_1 ? RvMedia::getImageUrl($shortcode->image_1) : $fallbackImage;
    $image2  = $shortcode->image_2 ? RvMedia::getImageUrl($shortcode->image_2) : $fallbackImage;
    $image3  = $shortcode->image_3 ? RvMedia::getImageUrl($shortcode->image_3) : $fallbackImage;
@endphp

<section class="pickup" style="background-image:url('{{ $bgImage }}')">
    <style>
        /* ── SECTION ──────────────────────────────────────────── */
        .pickup {
            position: relative;
            min-height: 80vh;
            background-color: #2a1f14;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 56px 48px 64px;
            font-family: 'Yu Mincho', 'Noto Serif JP', 'Times New Roman', Georgia, serif;
            overflow: hidden;
        }

        /* dark overlay on bg */
        .pickup::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(30, 20, 10, 0.55);
            pointer-events: none;
            z-index: 0;
        }

        .pickup > * { position: relative; z-index: 1; }

        /* ── HEADER ───────────────────────────────────────────── */
        .pickup__header {
            text-align: center;
            margin-bottom: 40px;
        }

        .pickup__pretitle {
            display: block;
            color: rgba(255, 255, 255, 0.7);
            font-family: 'Playfair Display', 'Georgia', serif;
            font-style: italic;
            font-size: clamp(28px, 3vw, 48px);
            font-weight: 400;
            letter-spacing: 0.04em;
            margin-bottom: 8px;
        }

        .pickup__title {
            color: #fff;
            font-size: clamp(14px, 1.3vw, 22px);
            font-weight: 400;
            letter-spacing: 0.14em;
            line-height: 1.8;
            margin: 0;
        }

        /* ── BODY (gallery + info) ────────────────────────────── */
        .pickup__body {
            display: flex;
            align-items: stretch;
            width: 100%;
            max-width: 1400px;
            gap: 0;
            flex: 1;
            min-height: 0;
        }

        /* ── GALLERY: 3 images ────────────────────────────────── */
        .pickup__gallery {
            flex: 0 0 62%;
            display: flex;
            gap: 4px;
            overflow: hidden;
        }

        .pickup__gallery-item {
            flex: 1;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 320px;
            position: relative;
        }

        /* middle image slightly larger */
        .pickup__gallery-item:nth-child(2) {
            flex: 0 0 38%;
        }

        .pickup__gallery-item::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom,
                rgba(30, 20, 10, 0) 55%,
                rgba(30, 20, 10, 0.35) 100%);
            pointer-events: none;
        }

        /* ── INFO ─────────────────────────────────────────────── */
        .pickup__info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 32px 0 32px 48px;
        }

        .pickup__desc {
            color: rgba(255, 255, 255, 0.85);
            font-size: 13px;
            line-height: 2.1;
            margin: 0 0 36px;
            max-width: 340px;
            text-align: center;
            text-wrap: balance;
        }

        .pickup__line {
            background: rgba(255, 255, 255, 0.25);
            height: 1px;
            width: 200px;
            margin-bottom: 12px;
        }

        .pickup__label {
            display: block;
            color: rgba(255, 255, 255, 0.6);
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 11px;
            letter-spacing: 0.22em;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .pickup__btn {
            background: #b19143;
            color: #fff;
            display: inline-block;
            font-size: 13px;
            letter-spacing: 0.06em;
            min-width: 190px;
            padding: 12px 28px;
            text-align: center;
            text-decoration: none;
            transition: background 0.22s;
            width: fit-content;
        }

        .pickup__btn:hover {
            background: #9a7a32;
            color: #fff;
        }

        /* ── RESPONSIVE ───────────────────────────────────────── */
        @media (max-width: 991px) {
            .pickup {
                padding: 40px 24px 48px;
            }

            .pickup__body {
                flex-direction: column;
                gap: 32px;
            }

            .pickup__gallery {
                flex: none;
                width: 100%;
                min-height: 240px;
            }

            .pickup__info {
                padding: 0;
            }
        }

        @media (max-width: 640px) {
            .pickup__gallery {
                flex-direction: column;
                gap: 4px;
            }

            .pickup__gallery-item,
            .pickup__gallery-item:nth-child(2) {
                flex: none;
                width: 100%;
                min-height: 200px;
            }
        }
    </style>

    {{-- Header: Pick up + JP title --}}
    <div class="pickup__header">
        @if($shortcode->pretitle)
            <span class="pickup__pretitle">{!! BaseHelper::clean($shortcode->pretitle) !!}</span>
        @endif
        @if($shortcode->title)
            <h2 class="pickup__title">{!! BaseHelper::clean($shortcode->title) !!}</h2>
        @endif
    </div>

    {{-- Body: gallery + info --}}
    <div class="pickup__body">

        {{-- 3 images gallery --}}
        <div class="pickup__gallery">
            <div class="pickup__gallery-item" style="background-image:url('{{ $image1 }}')"></div>
            <div class="pickup__gallery-item" style="background-image:url('{{ $image2 }}')"></div>
            <div class="pickup__gallery-item" style="background-image:url('{{ $image3 }}')"></div>
        </div>

        {{-- Info --}}
        <div class="pickup__info">
            @if($shortcode->description)
                <p class="pickup__desc">{!! BaseHelper::clean($shortcode->description) !!}</p>
            @endif
            <div class="pickup__line"></div>
            @if($shortcode->section_label)
                <span class="pickup__label">{!! BaseHelper::clean($shortcode->section_label) !!}</span>
            @endif
            @if($shortcode->button_label && $shortcode->button_url)
                <a href="{{ $shortcode->button_url }}" class="pickup__btn">{{ $shortcode->button_label }}</a>
            @endif
        </div>

    </div>

</section>
