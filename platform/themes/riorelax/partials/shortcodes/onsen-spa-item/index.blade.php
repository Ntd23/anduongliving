@php
    $sectionId = trim((string) ($shortcode->section_id ?: 'onsen_spa01_li01'));
    $mainImage = $shortcode->main_image ? RvMedia::getImageUrl($shortcode->main_image) : null;
    $topImage = $shortcode->top_image ? RvMedia::getImageUrl($shortcode->top_image) : null;
    $bottomImage = $shortcode->bottom_image ? RvMedia::getImageUrl($shortcode->bottom_image) : null;
    $backgroundImage = $shortcode->background_image ? RvMedia::getImageUrl($shortcode->background_image) : $mainImage;
    $backgroundColor = $shortcode->background_color ?: '#001514';
    $accentColor = $shortcode->accent_color ?: '#b49a47';
    $title = $shortcode->title ?: '露天風呂 すずむしの湯';
    $description = $shortcode->description ?: "自然の中で身も心もゆっくり温まり、\n解放感に満たされるひとときをお過ごしください。";
    $buttonLabel = $shortcode->button_label ?: 'お問い合わせ';
    $buttonUrl = $shortcode->button_url ?: '#';
@endphp

<section
    id="{{ $sectionId }}"
    class="onsen-spa-item"
    style="
        --onsen-spa-item-bg: {{ $backgroundColor }};
        --onsen-spa-item-accent: {{ $accentColor }};
        @if ($backgroundImage) background-image: url('{{ $backgroundImage }}'); @endif
    "
>
    <style>
        .onsen-spa-item {
            background-color: var(--onsen-spa-item-bg);
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            box-sizing: border-box;
            color: #fff;
            left: 50%;
            margin-left: -50vw;
            min-height: clamp(680px, 78svh, 900px);
            overflow: hidden;
            padding: clamp(54px, 5.4vw, 92px) clamp(22px, 4.5vw, 72px) clamp(62px, 5vw, 96px);
            position: relative;
            width: 100vw;
        }

        .onsen-spa-item::before {
            background:
                linear-gradient(90deg, rgba(0, 19, 18, 0.94) 0%, rgba(0, 19, 18, 0.83) 48%, rgba(0, 19, 18, 0.9) 100%),
                rgba(0, 0, 0, 0.36);
            content: "";
            inset: 0;
            position: absolute;
        }

        .onsen-spa-item__inner {
            margin: 0 auto;
            max-width: 1600px;
            position: relative;
            z-index: 1;
        }

        .onsen-spa-item__head {
            align-items: center;
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            margin: 0 0 clamp(38px, 4vw, 66px);
        }

        .onsen-spa-item__title {
            color: #fff;
            font-family: 'Yu Mincho', 'Noto Serif JP', Georgia, serif;
            font-size: clamp(20px, 1.45vw, 28px);
            font-weight: 400;
            grid-column: 2;
            letter-spacing: 0.18em;
            line-height: 1.4;
            margin: 0;
            text-align: center;
        }

        .onsen-spa-item__button {
            align-items: center;
            background: var(--onsen-spa-item-accent);
            border-radius: 999px;
            color: #fff;
            display: inline-flex;
            gap: 10px;
            grid-column: 3;
            justify-self: end;
            min-height: 54px;
            min-width: 170px;
            padding: 0 28px;
            text-decoration: none;
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        .onsen-spa-item__button:hover {
            background: #9c843b;
            color: #fff;
            transform: translateY(-1px);
        }

        .onsen-spa-item__button i {
            color: inherit;
            font-size: 18px;
        }

        .onsen-spa-item__layout {
            align-items: start;
            display: grid;
            gap: clamp(0px, 1vw, 16px);
            grid-template-columns: minmax(0, 2.04fr) minmax(300px, 1fr);
            margin: 0 auto;
            max-width: 1380px;
        }

        .onsen-spa-item__main {
            aspect-ratio: 1.52;
            min-height: 0;
            overflow: hidden;
        }

        .onsen-spa-item__side {
            display: grid;
            gap: 8px;
            padding-top: 0;
        }

        .onsen-spa-item__image {
            background: rgba(255, 255, 255, 0.04);
            overflow: hidden;
        }

        .onsen-spa-item__image img {
            display: block;
            height: 100%;
            object-fit: cover;
            width: 100%;
        }

        .onsen-spa-item__side-top {
            aspect-ratio: 2.05;
        }

        .onsen-spa-item__side-bottom {
            aspect-ratio: 1.55;
            justify-self: start;
            width: 66%;
        }

        .onsen-spa-item__description {
            color: rgba(255, 255, 255, 0.92);
            font-family: 'Yu Mincho', 'Noto Serif JP', Georgia, serif;
            font-size: clamp(15px, 0.95vw, 18px);
            font-weight: 400;
            letter-spacing: 0.08em;
            line-height: 2;
            margin: 22px 0 0 clamp(28px, 3vw, 54px);
            max-width: 420px;
        }

        .onsen-spa-item__description p {
            margin: 0;
        }

        @media (max-width: 1199px) {
            .onsen-spa-item__layout {
                grid-template-columns: minmax(0, 1.45fr) minmax(280px, 1fr);
            }

            .onsen-spa-item__side-bottom {
                width: 78%;
            }
        }

        @media (max-width: 991px) {
            .onsen-spa-item {
                min-height: 0;
            }

            .onsen-spa-item__head {
                gap: 22px;
                grid-template-columns: 1fr;
                justify-items: center;
            }

            .onsen-spa-item__title,
            .onsen-spa-item__button {
                grid-column: auto;
                justify-self: center;
            }

            .onsen-spa-item__layout {
                grid-template-columns: 1fr;
                max-width: 720px;
            }

            .onsen-spa-item__side-bottom {
                justify-self: center;
                width: 82%;
            }

            .onsen-spa-item__description {
                margin-left: 0;
                max-width: none;
                text-align: center;
            }
        }

        @media (max-width: 575px) {
            .onsen-spa-item {
                padding-left: 18px;
                padding-right: 18px;
            }

            .onsen-spa-item__button {
                min-width: 0;
                width: 100%;
                justify-content: center;
            }

            .onsen-spa-item__side-bottom {
                width: 100%;
            }
        }
    </style>

    <div class="onsen-spa-item__inner">
        <div class="onsen-spa-item__head">
            @if ($title)
                <h2 class="onsen-spa-item__title">{!! BaseHelper::clean($title) !!}</h2>
            @endif

            @if ($buttonLabel)
                <a class="onsen-spa-item__button" href="{{ $buttonUrl }}">
                    <i class="far fa-comment-alt" aria-hidden="true"></i>
                    <span>{!! BaseHelper::clean($buttonLabel) !!}</span>
                </a>
            @endif
        </div>

        <div class="onsen-spa-item__layout">
            @if ($mainImage)
                <div class="onsen-spa-item__image onsen-spa-item__main">
                    <img src="{{ $mainImage }}" alt="{{ strip_tags($title) }}">
                </div>
            @endif

            <div class="onsen-spa-item__side">
                @if ($topImage)
                    <div class="onsen-spa-item__image onsen-spa-item__side-top">
                        <img src="{{ $topImage }}" alt="{{ strip_tags($title) }}">
                    </div>
                @endif

                @if ($bottomImage)
                    <div class="onsen-spa-item__image onsen-spa-item__side-bottom">
                        <img src="{{ $bottomImage }}" alt="{{ strip_tags($title) }}">
                    </div>
                @endif

                @if ($description)
                    <div class="onsen-spa-item__description">
                        {!! BaseHelper::clean(nl2br($description)) !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
