@php
    $backgroundImage = $shortcode->background_image ? RvMedia::getImageUrl($shortcode->background_image) : null;
    $items = collect(range(1, 6))->map(function ($index) use ($shortcode) {
        return [
            'title' => $shortcode->{'item_title_' . $index},
            'image' => $shortcode->{'item_image_' . $index} ? RvMedia::getImageUrl($shortcode->{'item_image_' . $index}) : null,
        ];
    })->filter(fn ($item) => $item['title'] || $item['image'])->values();
@endphp

<section class="onsen-spa-gallery" @if ($backgroundImage) style="background-image: url('{{ $backgroundImage }}');" @endif>
    <style>
        .onsen-spa-gallery {
            background-color: #050505;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            overflow: hidden;
            padding: 34px 24px 52px;
            position: relative;
        }

        .onsen-spa-gallery::before {
            background: rgba(0, 0, 0, 0.62);
            content: "";
            inset: 0;
            position: absolute;
            z-index: 0;
        }

        .onsen-spa-gallery__inner {
            margin: 0 auto;
            max-width: 1040px;
            position: relative;
            text-align: center;
            z-index: 1;
        }

        .onsen-spa-gallery__bg-title {
            color: rgba(255, 255, 255, 0.16);
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(44px, 5.8vw, 108px);
            font-style: italic;
            left: 50%;
            line-height: 0.9;
            pointer-events: none;
            position: absolute;
            top: -8px;
            transform: translateX(-50%);
            white-space: nowrap;
        }

        .onsen-spa-gallery__subtitle {
            color: rgba(255, 255, 255, 0.95);
            font-size: clamp(16px, 1.1vw, 24px);
            letter-spacing: 0.12em;
            margin: 34px 0 26px;
            position: relative;
        }

        .onsen-spa-gallery__grid {
            display: grid;
            gap: 18px 12px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            justify-content: center;
            margin: 0 auto 30px;
            max-width: 874px;
        }

        .onsen-spa-gallery__item {
            text-align: center;
        }

        .onsen-spa-gallery__item:nth-child(5) {
            grid-column: 2 / 3;
        }

        .onsen-spa-gallery__item:nth-child(6) {
            grid-column: 3 / 4;
        }

        .onsen-spa-gallery__item-title {
            color: #fff;
            font-size: 13.8px;
            letter-spacing: 0.08em;
            line-height: 1.5;
            margin: 0 0 10px;
            min-height: 18px;
        }

        .onsen-spa-gallery__item-image {
            aspect-ratio: 1.2348;
            overflow: hidden;
        }

        .onsen-spa-gallery__item-image img {
            display: block;
            height: 100%;
            object-fit: cover;
            width: 100%;
        }

        .onsen-spa-gallery__footer {
            margin: 0 auto;
            max-width: 235px;
            text-align: center;
        }

        .onsen-spa-gallery__line {
            background: #8f7534;
            height: 1px;
            margin: 0 auto 8px;
            width: 166px;
        }

        .onsen-spa-gallery__label {
            color: #8f7534;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 9.66px;
            margin: 0 0 9px;
            text-transform: uppercase;
        }

        .onsen-spa-gallery__buttons {
            display: grid;
            gap: 8px;
        }

        .onsen-spa-gallery__button {
            background: #b19143;
            color: #fff;
            display: inline-flex;
            justify-content: center;
            font-size: 0.69em;
            padding: 9px 14px;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.2s ease;
        }

        .onsen-spa-gallery__button:hover {
            background: #9a7c35;
            color: #fff;
        }

        @media (max-width: 991px) {
            .onsen-spa-gallery {
                padding: 28px 16px 40px;
            }

            .onsen-spa-gallery__grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                max-width: 520px;
            }

            .onsen-spa-gallery__item:nth-child(5),
            .onsen-spa-gallery__item:nth-child(6) {
                grid-column: auto;
            }
        }

        @media (max-width: 575px) {
            .onsen-spa-gallery__grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="onsen-spa-gallery__inner">
        @if ($shortcode->background_title)
            <div class="onsen-spa-gallery__bg-title">{!! BaseHelper::clean($shortcode->background_title) !!}</div>
        @endif

        @if ($shortcode->subtitle)
            <div class="onsen-spa-gallery__subtitle">{!! BaseHelper::clean($shortcode->subtitle) !!}</div>
        @endif

        @if ($items->isNotEmpty())
            <div class="onsen-spa-gallery__grid">
                @foreach ($items as $item)
                    <div class="onsen-spa-gallery__item">
                        @if ($item['title'])
                            <div class="onsen-spa-gallery__item-title">{!! BaseHelper::clean($item['title']) !!}</div>
                        @endif

                        @if ($item['image'])
                            <div class="onsen-spa-gallery__item-image">
                                <img src="{{ $item['image'] }}" alt="{{ strip_tags($item['title'] ?: __('Gallery image')) }}">
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <div class="onsen-spa-gallery__footer">
            @if ($shortcode->section_label)
                <div class="onsen-spa-gallery__line"></div>
                <div class="onsen-spa-gallery__label">{!! BaseHelper::clean($shortcode->section_label) !!}</div>
            @endif

            <div class="onsen-spa-gallery__buttons">
                @if ($shortcode->button_label_1)
                    <a class="onsen-spa-gallery__button" href="{{ $shortcode->button_url_1 ?: 'javascript:void(0)' }}">
                        {!! BaseHelper::clean($shortcode->button_label_1) !!}
                    </a>
                @endif

                @if ($shortcode->button_label_2)
                    <a class="onsen-spa-gallery__button" href="{{ $shortcode->button_url_2 ?: 'javascript:void(0)' }}">
                        {!! BaseHelper::clean($shortcode->button_label_2) !!}
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>
