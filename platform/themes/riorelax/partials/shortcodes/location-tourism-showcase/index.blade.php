@php
    $fallbackImage = Theme::asset()->url('images/location-tourism-showcase/location-tourism-showcase.png');
    $bgImage = $shortcode->background_image ? RvMedia::getImageUrl($shortcode->background_image) : $fallbackImage;
    $mapImage = $shortcode->map_image ? RvMedia::getImageUrl($shortcode->map_image) : $fallbackImage;
    $gridImages = [];
    for ($i = 1; $i <= 8; $i++) {
        $imgKey = 'grid_image_' . $i;
        if ($shortcode->$imgKey) {
            $gridImages[] = RvMedia::getImageUrl($shortcode->$imgKey);
        }
    }

    if (! $gridImages) {
        $gridImages = array_fill(0, 8, $fallbackImage);
    }
@endphp

<section class="loc" style="background-image: url('{{ $bgImage }}')">
    <style>
        .loc {
            position: relative;
            background-color: #000;
            background-repeat: no-repeat;
            background-position: center bottom;
            background-size: cover;
            padding: 80px 48px 100px;
            color: #fff;
            font-family: 'Yu Mincho', 'Noto Serif JP', serif;
            overflow: hidden;
        }

        /* Top dark gradient to blend background image */
        .loc::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, #000 0%, #000 30%, rgba(0,0,0,0.4) 60%, rgba(0,0,0,0.1) 100%);
            z-index: 0;
        }

        .loc > * {
            position: relative;
            z-index: 1;
        }

        /* ── HEADER ───────────────────────────────────────────── */
        .loc__header {
            text-align: center;
            margin-bottom: 60px;
        }

        .loc__deco {
            position: absolute;
            top: -40px;
            left: 50%;
            transform: translateX(-50%);
            font-family: 'Playfair Display', serif;
            font-style: italic;
            font-size: clamp(80px, 12vw, 180px);
            color: rgba(255, 255, 255, 0.12);
            white-space: nowrap;
            z-index: 0;
            pointer-events: none;
        }

        .loc__title {
            font-size: clamp(16px, 1.8vw, 24px);
            letter-spacing: 0.22em;
            line-height: 1.8;
            margin-top: 20px;
            color: #fff;
            font-weight: 500;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }

        /* ── TOP CONTENT: Map and Grid ──────────────────────── */
        .loc__main {
            display: flex;
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto 50px;
            align-items: flex-start;
        }

        .loc__map {
            flex: 1;
        }

        .loc__map img {
            width: 100%;
            height: auto;
            display: block;
        }

        .loc__grid {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 4px;
        }

        .loc__grid-item {
            aspect-ratio: 4 / 3;
            background-size: cover;
            background-position: center;
        }

        /* ── BOTTOM CONTENT: Two Columns Info ─────────────── */
        .loc__info {
            display: flex;
            gap: 24px;
            max-width: 760px;
            margin: 0 auto;
        }

        .loc__col {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .loc__desc {
            font-size: clamp(12px, 1.2vw, 14px);
            line-height: 2.2;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 20px;
            min-height: 80px;
            text-align: left;
            width: 100%;
            text-wrap: balance;
        }

        .loc__divider {
            width: 100%;
            height: 1px;
            background: #b19143;
            margin-bottom: 15px;
            opacity: 0.6;
        }

        .loc__col-label {
            font-family: Georgia, serif;
            font-size: 10px;
            letter-spacing: 0.2em;
            color: #b19143;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        .loc__btn {
            background: #b19143;
            color: #fff;
            padding: 14px 24px;
            min-width: 280px;
            text-align: center;
            text-decoration: none;
            font-size: clamp(16px, 1.8vw, 24px);
            font-weight: 500;
            transition: 0.3s;
            letter-spacing: 0.05em;
        }

        .loc__btn:hover {
            background: #9a7a32;
        }

        /* ── RESPONSIVE ─────────────────────────────────────── */
        @media (max-width: 991px) {
            .loc__main {
                flex-direction: column;
            }
            .loc__info {
                flex-direction: column;
                gap: 50px;
            }
            .loc__desc {
                min-height: auto;
            }
        }

        @media (max-width: 640px) {
            .loc__grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>

    <div class="loc__header">
        @if($shortcode->decorative_text)
            <div class="loc__deco">{{ $shortcode->decorative_text }}</div>
        @endif
        @if($shortcode->section_title)
            <h2 class="loc__title">{!! BaseHelper::clean(nl2br($shortcode->section_title)) !!}</h2>
        @endif
    </div>

    <div class="loc__main">
        <div class="loc__map">
            <img src="{{ $mapImage }}" alt="Map">
        </div>
        <div class="loc__grid">
            @foreach($gridImages as $img)
                <div class="loc__grid-item" style="background-image: url('{{ $img }}')"></div>
            @endforeach
        </div>
    </div>

    <div class="loc__info">
        {{-- Access Column --}}
        <div class="loc__col">
            <p class="loc__desc">
                {!! BaseHelper::clean(nl2br($shortcode->access_desc)) !!}
            </p>
            <div class="loc__divider"></div>
            @if($shortcode->access_label)
                <div class="loc__col-label">{{ $shortcode->access_label }}</div>
            @endif
            @if($shortcode->access_btn_label && $shortcode->access_btn_url)
                <a href="{{ $shortcode->access_btn_url }}" class="loc__btn">{{ $shortcode->access_btn_label }}</a>
            @endif
        </div>

        {{-- Tourism Column --}}
        <div class="loc__col">
            <p class="loc__desc">
                {!! BaseHelper::clean(nl2br($shortcode->tourism_desc)) !!}
            </p>
            <div class="loc__divider"></div>
            @if($shortcode->tourism_label)
                <div class="loc__col-label">{{ $shortcode->tourism_label }}</div>
            @endif
            @if($shortcode->tourism_btn_label && $shortcode->tourism_btn_url)
                <a href="{{ $shortcode->tourism_btn_url }}" class="loc__btn">{{ $shortcode->tourism_btn_label }}</a>
            @endif
        </div>
    </div>
</section>
