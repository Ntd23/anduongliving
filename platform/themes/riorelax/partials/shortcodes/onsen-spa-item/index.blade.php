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
    $isReversed = $shortcode->image_layout === 'large_right';
@endphp

<section
    id="{{ $sectionId }}"
    @class(['onsen-spa-item', 'onsen-spa-item--reverse' => $isReversed])
    style="
        --onsen-spa-item-bg: {{ $backgroundColor }};
        --onsen-spa-item-accent: {{ $accentColor }};
        --onsen-spa-item-text: #fff;
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
                linear-gradient(90deg, rgba(0, 19, 18, 0.72) 0%, rgba(0, 19, 18, 0.48) 48%, rgba(0, 19, 18, 0.7) 100%),
                rgba(0, 0, 0, 0.12);
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
            display: flex;
            justify-content: center;
            margin: 0 0 clamp(38px, 4vw, 66px);
        }

        .onsen-spa-item__title {
            color: var(--onsen-spa-item-text);
            font-family: 'Yu Mincho', 'Noto Serif JP', Georgia, serif;
            font-size: clamp(20px, 1.45vw, 28px);
            font-weight: 500;
            letter-spacing: 0.18em;
            line-height: 1.4;
            margin: 0;
            text-align: center;
        }

        .onsen-spa-item__layout {
            align-items: start;
            display: grid;
            gap: clamp(0px, 1vw, 16px);
            grid-template-columns: minmax(0, 2.04fr) minmax(300px, 1fr);
            margin: 0 auto;
            max-width: 1380px;
        }

        .onsen-spa-item--reverse .onsen-spa-item__layout {
            grid-template-columns: minmax(300px, 1fr) minmax(0, 2.04fr);
        }

        .onsen-spa-item--reverse .onsen-spa-item__main {
            grid-column: 2;
            grid-row: 1;
        }

        .onsen-spa-item--reverse .onsen-spa-item__side {
            grid-column: 1;
            grid-row: 1;
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

        .onsen-spa-item--reverse .onsen-spa-item__side-bottom {
            justify-self: end;
        }

        .onsen-spa-item__description {
            color: var(--onsen-spa-item-text);
            font-family: 'Yu Mincho', 'Noto Serif JP', Georgia, serif;
            font-size: clamp(15px, 0.95vw, 18px);
            font-weight: 500;
            letter-spacing: 0.08em;
            line-height: 2;
            margin: 22px 0 0 clamp(28px, 3vw, 54px);
            max-width: 420px;
        }

        .onsen-spa-item--reverse .onsen-spa-item__description {
            margin-left: auto;
            margin-right: clamp(28px, 3vw, 54px);
        }

        .onsen-spa-item__description p {
            margin: 0;
        }

        @media (max-width: 1199px) {
            .onsen-spa-item__layout {
                grid-template-columns: minmax(0, 1.45fr) minmax(280px, 1fr);
            }

            .onsen-spa-item--reverse .onsen-spa-item__layout {
                grid-template-columns: minmax(280px, 1fr) minmax(0, 1.45fr);
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
                justify-items: center;
            }

            .onsen-spa-item__layout {
                grid-template-columns: 1fr;
                max-width: 720px;
            }

            .onsen-spa-item--reverse .onsen-spa-item__main,
            .onsen-spa-item--reverse .onsen-spa-item__side {
                grid-column: auto;
                grid-row: auto;
            }

            .onsen-spa-item__side-bottom {
                justify-self: center;
                width: 82%;
            }

            .onsen-spa-item--reverse .onsen-spa-item__side-bottom {
                justify-self: center;
            }

            .onsen-spa-item__description {
                margin-left: 0;
                margin-right: 0;
                max-width: none;
                text-align: center;
            }
        }

        @media (max-width: 575px) {
            .onsen-spa-item {
                padding-left: 18px;
                padding-right: 18px;
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
