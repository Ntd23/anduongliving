@php
    $sectionId = trim((string) ($shortcode->section_id ?: 'onsen_detail_info'));
    $backgroundColor = $shortcode->background_color ?: '#ffffff';
    $accentColor = $shortcode->accent_color ?: '#7a5c1f';
    $titleColor = $shortcode->title_color ?: '#6b4d16';
    $mainImage = $shortcode->main_image ? RvMedia::getImageUrl($shortcode->main_image) : null;
    $infoBackgroundImage = $shortcode->info_background_image ? RvMedia::getImageUrl($shortcode->info_background_image) : null;
    $bottomLogoImage = $shortcode->bottom_logo_image ? RvMedia::getImageUrl($shortcode->bottom_logo_image) : null;
    $bottomLogoUrl = $shortcode->bottom_logo_url;
    $title = $shortcode->title ?: 'Seasonal Walking Bath';
    $description = $shortcode->description ?: "Beside the spa room, a walking bath faces the flowing Tanikawa stream.\nRelax with forest bathing and comfortably warm water between 38 and 41 degrees Celsius.";
    $infoTitle = $shortcode->info_title ?: 'About the Private Hot Spring';

    $defaultRows = [
        ['Spring Quality', 'Calcium sodium sulfate chloride spring. Hypotonic, mildly alkaline hot spring.'],
        ['pH Value', '8.04. Mildly alkaline.'],
        ['Bathing Indications', 'Cuts, peripheral circulation disorders, sensitivity to cold, depressed mood, dry skin.'],
        ['Water Usage', '100% free-flowing source water. Heated in winter when the spring temperature drops.'],
        ['Spring Features', 'Warming water, salt mineral benefits, gentle cleansing from mild alkalinity.'],
        ['Benefits', 'Digestive discomfort, neuralgia, and sensitivity to cold.'],
    ];

    $rows = collect($infoRows ?? [])
        ->map(function ($row) {
            return [
                'label' => $row['label'] ?? '',
                'text' => $row['text'] ?? '',
            ];
        })
        ->filter(fn ($row) => filled($row['label']) || filled($row['text']))
        ->values();

    if ($rows->isEmpty()) {
        $rows = collect(range(1, 6))->map(function ($index) use ($shortcode, $defaultRows) {
            $label = $shortcode->{'info_label_' . $index} ?: $defaultRows[$index - 1][0];
            $text = $shortcode->{'info_text_' . $index} ?: $defaultRows[$index - 1][1];

            return compact('label', 'text');
        })->filter(fn ($row) => filled($row['label']) || filled($row['text']))->values();
    }
@endphp

<section
    id="{{ $sectionId }}"
    class="onsen-detail-info"
    style="
        --onsen-detail-bg: {{ $backgroundColor }};
        --onsen-detail-accent: {{ $accentColor }};
        --onsen-detail-title: {{ $titleColor }};
    "
>
    <style>
        .onsen-detail-info {
            background: var(--onsen-detail-bg);
            box-sizing: border-box;
            color: #111;
            left: 50%;
            margin-left: -50vw;
            overflow: hidden;
            position: relative;
            width: 100vw;
        }

        .onsen-detail-info__top {
            margin: 0 auto;
            max-width: 1040px;
            padding: clamp(56px, 5.2vw, 88px) 24px clamp(62px, 5vw, 86px);
            text-align: center;
        }

        .onsen-detail-info .onsen-detail-info__title {
            color: var(--onsen-detail-title) !important;
            filter: none;
            font-family: 'Yu Mincho', 'Noto Serif JP', Georgia, serif;
            font-size: clamp(18px, 1.45vw, 26px);
            font-weight: 900;
            letter-spacing: 0.28em;
            line-height: 1.45;
            margin: 0 0 clamp(28px, 2.6vw, 42px);
            mix-blend-mode: normal;
            opacity: 1 !important;
            text-shadow: none;
        }

        .onsen-detail-info__main-image {
            margin: 0 auto;
            max-width: 1000px;
            overflow: hidden;
        }

        .onsen-detail-info__main-image img {
            display: block;
            height: auto;
            width: 100%;
        }

        .onsen-detail-info__lead {
            font-family: 'Yu Mincho', 'Noto Serif JP', Georgia, serif;
            font-size: clamp(15px, 0.96vw, 18px);
            font-weight: 500;
            letter-spacing: 0.06em;
            line-height: 2;
            margin: clamp(24px, 2.2vw, 36px) auto 0;
            max-width: 820px;
            text-align: left;
        }

        .onsen-detail-info__band {
            background-color: #eef0ef;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            padding: clamp(34px, 3vw, 50px) 24px;
            position: relative;
        }

        .onsen-detail-info__band::before {
            background: rgba(255, 255, 255, 0.68);
            content: "";
            inset: 0;
            position: absolute;
        }

        .onsen-detail-info__band-inner {
            margin: 0 auto;
            max-width: 1000px;
            position: relative;
            z-index: 1;
        }

        .onsen-detail-info .onsen-detail-info__info-title {
            color: var(--onsen-detail-title) !important;
            filter: none;
            font-family: 'Yu Mincho', 'Noto Serif JP', Georgia, serif;
            font-size: clamp(18px, 1.35vw, 26px);
            font-weight: 900;
            letter-spacing: 0.22em;
            line-height: 1.5;
            margin: 0 0 20px;
            mix-blend-mode: normal;
            opacity: 1 !important;
            position: relative;
            text-shadow: none;
            z-index: 2;
        }

        .onsen-detail-info__rows {
            display: grid;
            gap: 12px;
        }

        .onsen-detail-info__row {
            align-items: stretch;
            display: grid;
            gap: 18px;
            grid-template-columns: minmax(170px, 230px) minmax(0, 1fr);
        }

        .onsen-detail-info__label {
            align-items: center;
            background: rgba(163, 139, 82, 0.76);
            color: #fff;
            display: flex;
            font-family: 'Yu Mincho', 'Noto Serif JP', Georgia, serif;
            font-size: 14px;
            justify-content: center;
            letter-spacing: 0.12em;
            line-height: 1.45;
            min-height: 38px;
            padding: 8px 14px;
            text-align: center;
        }

        .onsen-detail-info__text {
            align-items: center;
            color: #111;
            display: flex;
            font-family: 'Yu Mincho', 'Noto Serif JP', Georgia, serif;
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.05em;
            line-height: 1.85;
            min-height: 38px;
            padding: 3px 0;
        }

        .onsen-detail-info__logo-wrap {
            background: #fff;
            padding: clamp(40px, 4vw, 70px) 24px clamp(58px, 5vw, 86px);
            text-align: center;
        }

        .onsen-detail-info__logo {
            display: inline-block;
            max-width: min(520px, 88vw);
        }

        .onsen-detail-info__logo img {
            display: block;
            height: auto;
            width: 100%;
        }

        @media (max-width: 767px) {
            .onsen-detail-info__top {
                padding-left: 18px;
                padding-right: 18px;
            }

            .onsen-detail-info__lead {
                letter-spacing: 0.02em;
            }

            .onsen-detail-info__info-title {
                letter-spacing: 0.1em;
            }

            .onsen-detail-info__row {
                gap: 8px;
                grid-template-columns: 1fr;
            }

            .onsen-detail-info__text {
                padding-bottom: 12px;
            }
        }
    </style>

    <div class="onsen-detail-info__top">
        @if ($title)
            <h2 class="onsen-detail-info__title">{!! BaseHelper::clean($title) !!}</h2>
        @endif

        @if ($mainImage)
            <div class="onsen-detail-info__main-image">
                <img src="{{ $mainImage }}" alt="{{ strip_tags($title) }}">
            </div>
        @endif

        @if ($description)
            <div class="onsen-detail-info__lead">
                {!! BaseHelper::clean(nl2br($description)) !!}
            </div>
        @endif
    </div>

    <div
        class="onsen-detail-info__band"
        @if ($infoBackgroundImage) style="background-image: url('{{ $infoBackgroundImage }}');" @endif
    >
        <div class="onsen-detail-info__band-inner">
            @if ($infoTitle)
                <h3 class="onsen-detail-info__info-title">{!! BaseHelper::clean($infoTitle) !!}</h3>
            @endif

            @if ($rows->isNotEmpty())
                <div class="onsen-detail-info__rows">
                    @foreach ($rows as $row)
                        <div class="onsen-detail-info__row">
                            <div class="onsen-detail-info__label">{!! BaseHelper::clean($row['label']) !!}</div>
                            <div class="onsen-detail-info__text">{!! BaseHelper::clean(nl2br($row['text'])) !!}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @if ($bottomLogoImage)
        <div class="onsen-detail-info__logo-wrap">
            @if ($bottomLogoUrl)
                <a class="onsen-detail-info__logo" href="{{ $bottomLogoUrl }}">
                    <img src="{{ $bottomLogoImage }}" alt="{{ strip_tags($title) }}">
                </a>
            @else
                <div class="onsen-detail-info__logo">
                    <img src="{{ $bottomLogoImage }}" alt="{{ strip_tags($title) }}">
                </div>
            @endif
        </div>
    @endif
</section>
