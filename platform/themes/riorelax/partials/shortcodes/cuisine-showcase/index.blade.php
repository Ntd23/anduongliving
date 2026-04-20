@php
    $image1 = $shortcode->image_1 ? RvMedia::getImageUrl($shortcode->image_1) : null;
    $image2 = $shortcode->image_2 ? RvMedia::getImageUrl($shortcode->image_2) : null;
@endphp

<section class="shortcode-cuisine-showcase cuisine">
    <style>
        /* ── SECTION ──────────────────────────────────────────── */
        .cuisine {
            background: #fff;
            padding: 80px 48px 72px;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-family: 'Yu Mincho', 'Noto Serif JP', 'Times New Roman', Georgia, serif;
        }

        /* ── TITLE ─────────────────────────────────────────────── */
        .cuisine__title {
            color: #8a6518;
            font-size: clamp(16px, 1.6vw, 26px);
            font-weight: 400;
            letter-spacing: 0.12em;
            line-height: 2;
            margin: 0 0 40px;
            text-align: center;
            max-width: 600px;
            text-wrap: balance;
        }

        /* ── DESCRIPTION ──────────────────────────────────────── */
        .cuisine__desc {
            color: #4f4639;
            font-size: 13px;
            line-height: 2.2;
            margin: 0 0 48px;
            max-width: 820px;
            text-align: center;
            text-wrap: balance;
        }

        /* ── IMAGES (overlapping stagger) ─────────────────────── */
        .cuisine__images {
            position: relative;
            width: 100%;
            max-width: 700px;
            height: 380px;
            margin-bottom: 56px;
        }

        .cuisine__img {
            position: absolute;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        }

        .cuisine__img:first-child {
            left: 8%;
            top: 0;
            width: 48%;
            z-index: 1;
        }

        .cuisine__img:last-child {
            right: 8%;
            bottom: 0;
            width: 48%;
            z-index: 2;
        }

        .cuisine__img img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            aspect-ratio: 4 / 3;
        }

        /* ── DIVIDER + LABEL + BUTTON ─────────────────────────── */
        .cuisine__footer {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .cuisine__line {
            background: #b19143;
            height: 1px;
            width: 200px;
            margin-bottom: 14px;
        }

        .cuisine__label {
            display: block;
            color: #b19143;
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 11px;
            letter-spacing: 0.22em;
            margin-bottom: 18px;
            text-transform: uppercase;
        }

        .cuisine__btn {
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
        }

        .cuisine__btn:hover {
            background: #9a7a32;
            color: #fff;
        }

        /* ── RESPONSIVE ───────────────────────────────────────── */
        @media (max-width: 768px) {
            .cuisine {
                padding: 56px 24px 48px;
            }

            .cuisine__images {
                flex-direction: column;
                max-width: 400px;
            }

            .cuisine__img img {
                aspect-ratio: 16 / 10;
            }
        }
    </style>

    {{-- Title --}}
    @if($shortcode->title)
        <h2 class="cuisine__title">{!! BaseHelper::clean($shortcode->title) !!}</h2>
    @endif

    {{-- Description --}}
    @if($shortcode->description)
        <p class="cuisine__desc">{!! BaseHelper::clean($shortcode->description) !!}</p>
    @endif

    {{-- 2 images side by side --}}
    <div class="cuisine__images">
        @if($image1)
            <div class="cuisine__img">
                <img src="{{ $image1 }}" alt="">
            </div>
        @endif
        @if($image2)
            <div class="cuisine__img">
                <img src="{{ $image2 }}" alt="">
            </div>
        @endif
    </div>

    {{-- Footer: line + label + button --}}
    <div class="cuisine__footer">
        <div class="cuisine__line"></div>
        @if($shortcode->section_label)
            <span class="cuisine__label">{!! BaseHelper::clean($shortcode->section_label) !!}</span>
        @endif
        @if($shortcode->button_label && $shortcode->button_url)
            <a href="{{ $shortcode->button_url }}" class="cuisine__btn">{{ $shortcode->button_label }}</a>
        @endif
    </div>

</section>
