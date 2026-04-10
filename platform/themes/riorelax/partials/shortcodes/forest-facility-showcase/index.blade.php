@php
    $leftImage = $shortcode->left_image ? RvMedia::getImageUrl($shortcode->left_image) : null;
    $rightImage1 = $shortcode->right_image_1 ? RvMedia::getImageUrl($shortcode->right_image_1) : null;
    $rightImage2 = $shortcode->right_image_2 ? RvMedia::getImageUrl($shortcode->right_image_2) : null;
@endphp

<section class="rfs">
    <style>
        /* ── SECTION: single cell, 3 layers stacked ──────────── */
        .rfs {
            display: grid;
            grid-template: 1fr / 1fr;
            min-height: 88vh;
            overflow: hidden;
            background: #f4eddc;
            font-family: 'Yu Mincho', 'Noto Serif JP', 'Times New Roman', Georgia, serif;
        }

        /* ── LAYER 1: images (bottom) ────────────────────────── */
        .rfs__images {
            grid-area: 1 / 1;
            display: flex;
            z-index: 1;
        }

        /* left big image */
        .rfs__img-left {
            flex: 0 0 35%;
            position: relative;
            overflow: hidden;
        }
        .rfs__img-left img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: 65% center;
        }
        /* fog overlay on left image */
        .rfs__img-left::after {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(to right,
                    rgba(244,237,220,0) 30%,
                    rgba(244,237,220,0.88) 100%),
                linear-gradient(to bottom,
                    rgba(244,237,220,0) 55%,
                    rgba(244,237,220,0.45) 100%);
            pointer-events: none;
        }

        /* right gallery: 2 images side by side */
        .rfs__img-right {
            flex: 0 0 45%;
            display: flex;
            align-items: center;
            margin-left: auto;
            padding: 12% 0;
            overflow: hidden;
        }
        .rfs__img-right-item {
            flex: 1;
            height: 100%;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }
        .rfs__img-right-item:last-child {
            flex: 0 0 55%;
        }
        .rfs__img-right-item::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom,
                rgba(244,237,220,0) 60%,
                rgba(244,237,220,0.3) 100%);
            pointer-events: none;
        }

        /* ── LAYER 2: title (middle) ─────────────────────────── */
        .rfs__title-layer {
            grid-area: 1 / 1;
            z-index: 2;
            pointer-events: none;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding-top: 56px;
            padding-left: 45%;
            padding-right: 40px;
        }
        .rfs__heading {
            pointer-events: auto;
            color: #8a6518;
            font-size: clamp(16px, 1.6vw, 26px);
            font-weight: 400;
            letter-spacing: 0.12em;
            line-height: 2;
            margin: 0;
            text-align: center;
        }

        /* ── LAYER 3: info block (top) ───────────────────────── */
        .rfs__info-layer {
            grid-area: 1 / 1;
            z-index: 3;
            pointer-events: none;
            display: flex;
            align-items: center;
            padding-left: 38%;
            padding-right: 55%;
        }
        .rfs__info {
            pointer-events: auto;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .rfs__desc {
            color: #4f4639;
            font-size: 13px;
            line-height: 2.1;
            margin: 0 0 32px;
            max-width: 320px;
        }
        .rfs__line {
            background: #b19143;
            height: 1px;
            width: 220px;
            margin-bottom: 12px;
        }
        .rfs__label {
            color: #b19143;
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 11px;
            letter-spacing: 0.22em;
            margin-bottom: 18px;
            text-transform: uppercase;
        }
        .rfs__btn {
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
        .rfs__btn:hover {
            background: #9a7a32;
            color: #fff;
        }

        /* ── RESPONSIVE ──────────────────────────────────────── */
        @media (max-width: 991px) {
            .rfs {
                display: flex;
                flex-direction: column;
                min-height: auto;
            }
            .rfs__images {
                flex-direction: column;
            }
            .rfs__img-left {
                flex: none;
                width: 100%;
                height: 60vw;
                min-height: 260px;
            }
            .rfs__img-right {
                flex: none;
                width: 100%;
                padding: 0;
                min-height: 220px;
            }
            .rfs__title-layer,
            .rfs__info-layer {
                position: relative;
                grid-area: auto;
                padding: 32px 24px;
            }
            .rfs__title-layer {
                padding-left: 24px;
                justify-content: flex-start;
            }
            .rfs__info-layer {
                padding-left: 24px;
                padding-right: 24px;
            }
        }
        @media (max-width: 640px) {
            .rfs__heading {
                font-size: clamp(14px, 5vw, 20px);
            }
            .rfs__img-right {
                flex-direction: column;
            }
            .rfs__img-right-item,
            .rfs__img-right-item:last-child {
                flex: none;
                width: 100%;
                min-height: 200px;
            }
        }
    </style>

    {{-- LAYER 1: All images --}}
    <div class="rfs__images">
        <div class="rfs__img-left">
            @if($leftImage)
                <img src="{{ $leftImage }}" alt="">
            @endif
        </div>
        <div class="rfs__img-right">
            @if($rightImage1)
                <div class="rfs__img-right-item" style="background-image:url('{{ $rightImage1 }}')"></div>
            @endif
            @if($rightImage2)
                <div class="rfs__img-right-item" style="background-image:url('{{ $rightImage2 }}')"></div>
            @endif
        </div>
    </div>

    {{-- LAYER 2: Title --}}
    <div class="rfs__title-layer">
        @if($shortcode->title)
            <h2 class="rfs__heading">{!! BaseHelper::clean($shortcode->title) !!}</h2>
        @endif
    </div>

    {{-- LAYER 3: Info (desc + line + label + button) --}}
    <div class="rfs__info-layer">
        <div class="rfs__info">
            @if($shortcode->description)
                <p class="rfs__desc">{!! BaseHelper::clean($shortcode->description) !!}</p>
            @endif
            <div class="rfs__line"></div>
            @if($shortcode->section_label)
                <span class="rfs__label">{!! BaseHelper::clean($shortcode->section_label) !!}</span>
            @endif
            @if($shortcode->button_label && $shortcode->button_url)
                <a href="{{ $shortcode->button_url }}" class="rfs__btn">{{ $shortcode->button_label }}</a>
            @endif
        </div>
    </div>

</section>