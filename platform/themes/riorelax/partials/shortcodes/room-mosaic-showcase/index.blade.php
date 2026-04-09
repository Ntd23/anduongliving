@php
    $backgroundImage = $shortcode->background_image ? RvMedia::getImageUrl($shortcode->background_image) : null;
    $mainImage = $shortcode->main_image ? RvMedia::getImageUrl($shortcode->main_image) : null;
    $sideTextLines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $shortcode->side_text))));
    $rooms = collect($rooms)->filter(fn ($room) => ! empty($room['title']) || ! empty($room['image']))->take(8)->values();
@endphp

<section class="room-mosaic-showcase">
    <style>
        .room-mosaic-showcase {
            background: #f7f4ef;
            padding: 0 0 80px;
        }

        .room-mosaic-showcase__hero {
            background-color: #22150f;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            min-height: 560px;
            padding: 70px 24px 180px;
            position: relative;
        }

        .room-mosaic-showcase__hero::before {
            background: rgba(31, 19, 13, 0.7);
            content: "";
            inset: 0;
            position: absolute;
            z-index: 0;
        }

        .room-mosaic-showcase__hero-inner {
            color: #fff;
            margin: 0 auto;
            max-width: 1180px;
            position: relative;
            text-align: center;
            z-index: 1;
        }

        .room-mosaic-showcase__subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            letter-spacing: 0.24em;
            margin: 0 0 10px;
            text-transform: uppercase;
        }

        .room-mosaic-showcase__title {
            color: #fff;
            font-size: clamp(28px, 2.6vw, 46px);
            letter-spacing: 0.08em;
            margin: 0 0 18px;
        }

        .room-mosaic-showcase__description {
            color: rgba(255, 255, 255, 0.92);
            font-size: clamp(13px, 0.95vw, 17px);
            line-height: 1.9;
            margin: 0 auto;
            max-width: 760px;
        }

        .room-mosaic-showcase__collage {
            display: block;
            margin: -140px auto 40px;
            max-width: 1138px;
            position: relative;
            width: min(calc(100% - 32px), 1138px);
            z-index: 2;
        }

        .room-mosaic-showcase__main,
        .room-mosaic-showcase__side-image {
            overflow: hidden;
        }

        .room-mosaic-showcase__main img,
        .room-mosaic-showcase__side-image img,
        .room-mosaic-showcase__card img {
            display: block;
            height: 100%;
            object-fit: cover;
            width: 100%;
        }

        .room-mosaic-showcase__main {
            min-height: 288px;
        }

        .room-mosaic-showcase__content {
            display: grid;
            gap: 30px;
            grid-template-columns: minmax(0, 2fr) minmax(260px, 0.95fr);
            margin: 0 auto;
            max-width: 1180px;
            padding: 0 24px;
        }

        .room-mosaic-showcase__grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .room-mosaic-showcase__card {
            background: #2b160f;
            color: #fff;
            overflow: hidden;
            position: relative;
        }

        .room-mosaic-showcase__card-image {
            aspect-ratio: 1.58;
        }

        .room-mosaic-showcase__card-title {
            background: linear-gradient(180deg, rgba(41, 17, 11, 0.78) 0%, rgba(41, 17, 11, 0.96) 100%);
            color: #fff8eb;
            font-size: 13px;
            letter-spacing: 0.04em;
            line-height: 1.4;
            padding: 10px 14px;
            text-align: center;
        }

        .room-mosaic-showcase__sidebar {
            align-self: start;
            padding-top: 8px;
        }

        .room-mosaic-showcase__side-text {
            color: #2d241d;
            font-size: 14px;
            line-height: 1.9;
            margin: 0 0 18px;
            white-space: pre-line;
        }

        .room-mosaic-showcase__room-list {
            display: grid;
            gap: 10px;
        }

        .room-mosaic-showcase__room-item {
            align-items: center;
            background: #f1ece4;
            color: #4a3a2d;
            display: flex;
            font-size: 13px;
            gap: 10px;
            min-height: 38px;
            padding: 10px 14px;
        }

        .room-mosaic-showcase__room-item::before {
            color: #9d8a76;
            content: ">";
            font-size: 16px;
            line-height: 1;
        }

        @media (max-width: 991px) {
            .room-mosaic-showcase {
                padding-bottom: 48px;
            }

            .room-mosaic-showcase__hero {
                min-height: 460px;
                padding: 48px 16px 140px;
            }

            .room-mosaic-showcase__collage {
                margin: -96px 16px 28px;
                width: auto;
            }

            .room-mosaic-showcase__content {
                gap: 24px;
                grid-template-columns: 1fr;
                padding: 0 16px;
            }
        }

        @media (max-width: 640px) {
            .room-mosaic-showcase__grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="room-mosaic-showcase__hero" @if ($backgroundImage) style="background-image: url('{{ $backgroundImage }}');" @endif>
        <div class="room-mosaic-showcase__hero-inner">
            @if ($shortcode->subtitle)
                <p class="room-mosaic-showcase__subtitle">{!! BaseHelper::clean($shortcode->subtitle) !!}</p>
            @endif

            @if ($shortcode->title)
                <h2 class="room-mosaic-showcase__title">{!! BaseHelper::clean($shortcode->title) !!}</h2>
            @endif

            @if ($shortcode->description)
                <div class="room-mosaic-showcase__description">{!! BaseHelper::clean($shortcode->description) !!}</div>
            @endif
        </div>
    </div>

    @if ($mainImage)
        <div class="room-mosaic-showcase__collage">
            <div class="room-mosaic-showcase__main">
                <img src="{{ $mainImage }}" alt="{{ __('Main image') }}">
            </div>
        </div>
    @endif

    <div class="room-mosaic-showcase__content">
        <div class="room-mosaic-showcase__grid">
            @foreach ($rooms as $room)
                <div class="room-mosaic-showcase__card">
                    @if (! empty($room['image']))
                        <div class="room-mosaic-showcase__card-image">
                            <img src="{{ RvMedia::getImageUrl($room['image']) }}" alt="{{ strip_tags($room['title'] ?: __('Room image')) }}">
                        </div>
                    @endif

                    @if (! empty($room['title']))
                        <div class="room-mosaic-showcase__card-title">{!! BaseHelper::clean($room['title']) !!}</div>
                    @endif
                </div>
            @endforeach
        </div>

        <aside class="room-mosaic-showcase__sidebar">
            @if ($sideTextLines)
                <div class="room-mosaic-showcase__side-text">
                    @foreach ($sideTextLines as $line)
                        <div>{!! BaseHelper::clean($line) !!}</div>
                    @endforeach
                </div>
            @endif

            <div class="room-mosaic-showcase__room-list">
                @foreach ($rooms as $room)
                    @if (! empty($room['title']))
                        <div class="room-mosaic-showcase__room-item">{!! BaseHelper::clean($room['title']) !!}</div>
                    @endif
                @endforeach
            </div>
        </aside>
    </div>
</section>
