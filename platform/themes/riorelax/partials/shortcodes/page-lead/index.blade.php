@php
    $sectionId = trim((string) ($shortcode->section_id ?: 'pageLead'));
    $backgroundImage = $shortcode->background_image ? RvMedia::getImageUrl($shortcode->background_image) : null;
    $backgroundColor = $shortcode->background_color ?: '#f7f7f5';
    $accentColor = $shortcode->accent_color ?: '#8b6f2f';
    $scriptColor = $shortcode->script_color ?: '#dedbd5';
    $textColor = $shortcode->text_color ?: '#111111';
    $title = $shortcode->title ?: '上質なリラクゼーションを体感';
    $decorativeText = $shortcode->decorative_text ?: 'Onsen';
    $subtitle = $shortcode->subtitle ?: '贅沢な時間を過ごす';
    $description = $shortcode->description ?: "水上温泉郷の西方に位置する谷川温泉は江戸時代からこんこんと湧き続け、\n豪雪地帯のみなさまに古くから愛されてきました。\n仙寿庵の湯は、肌にやさしい低張性弱アルカリ性温泉でございます。谷川の清流と緑の中で新鮮な空気とともに\n愉しめる露天風呂、檜の香りが愉しめる内湯、談笑しながらほっとしたひとときを過ごせる歩行湯など、\n何よりも贅沢で心地いい時間を愉しめます。";
@endphp

<section
    id="{{ $sectionId }}"
    class="page-lead"
    style="
        --page-lead-bg: {{ $backgroundColor }};
        --page-lead-accent: {{ $accentColor }};
        --page-lead-script: {{ $scriptColor }};
        --page-lead-text: {{ $textColor }};
        @if ($backgroundImage) background-image: url('{{ $backgroundImage }}'); @endif
    "
>
    <style>
        .page-lead {
            align-items: center;
            background-color: var(--page-lead-bg);
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            box-sizing: border-box;
            color: var(--page-lead-text);
            display: flex;
            left: 50%;
            margin-left: -50vw;
            min-height: clamp(560px, 72svh, 760px);
            overflow: hidden;
            padding: clamp(48px, 4vw, 76px) 24px clamp(60px, 5vw, 84px);
            position: relative;
            text-align: center;
            width: 100vw;
        }

        .page-lead::before {
            background: rgba(255, 255, 255, 0.82);
            content: "";
            inset: 0;
            opacity: 0;
            pointer-events: none;
            position: absolute;
        }

        .page-lead[style*="background-image"]::before {
            opacity: 1;
        }

        .page-lead__inner {
            margin: 0 auto;
            max-width: 1480px;
            position: relative;
            z-index: 1;
            width: 100%;
        }

        .page-lead__header {
            margin: 0 auto;
            min-height: 120px;
            position: relative;
        }

        .page-lead__title {
            color: #8b6f2f;
            color: color-mix(in srgb, var(--page-lead-accent) 62%, #4d3913 38%);
            font-family: 'Yu Mincho', 'Noto Serif JP', Georgia, serif;
            font-size: clamp(22px, 1.78vw, 34px);
            font-weight: 600;
            letter-spacing: 0.16em;
            line-height: 1.35;
            margin: 0;
            position: relative;
            text-shadow: 0 1px 0 rgba(255, 255, 255, 0.45);
            z-index: 2;
        }

        .page-lead__script {
            color: var(--page-lead-script);
            font-family: "Cormorant Garamond", "Playfair Display", Georgia, serif;
            font-size: clamp(58px, 6vw, 112px);
            font-style: italic;
            font-weight: 300;
            left: 50%;
            letter-spacing: 0.02em;
            line-height: 0.72;
            opacity: 0.78;
            pointer-events: none;
            position: absolute;
            top: 30px;
            transform: translateX(-50%);
            white-space: nowrap;
            z-index: 1;
        }

        .page-lead__subtitle {
            color: #8b6f2f;
            color: color-mix(in srgb, var(--page-lead-accent) 68%, #4d3913 32%);
            font-family: 'Yu Mincho', 'Noto Serif JP', Georgia, serif;
            font-size: clamp(15px, 1.05vw, 19px);
            font-weight: 500;
            letter-spacing: 0.08em;
            line-height: 1.6;
            margin: 42px 0 0;
            position: relative;
            z-index: 2;
        }

        .page-lead__description {
            font-family: 'Yu Mincho', 'Noto Serif JP', "Times New Roman", serif;
            font-size: clamp(16px, 1vw, 18px);
            font-weight: 500;
            letter-spacing: 0.035em;
            line-height: 2.15;
            margin: clamp(28px, 2.2vw, 40px) auto 0;
            max-width: min(1120px, 74vw);
        }

        .page-lead__description p {
            margin: 0;
        }

        .page-lead__description p + p {
            margin-top: 10px;
        }

        @media (max-width: 991px) {
            .page-lead {
                padding-left: 18px;
                padding-right: 18px;
            }

            .page-lead__description {
                letter-spacing: 0;
                line-height: 2;
                text-align: left;
            }

        }

        @media (max-width: 575px) {
            .page-lead__title {
                letter-spacing: 0.08em;
            }

            .page-lead__script {
                top: 36px;
            }

            .page-lead__subtitle {
                margin-top: 34px;
            }
        }
    </style>

    <div class="page-lead__inner">
        <div class="page-lead__header">
            @if ($title)
                <h2 class="page-lead__title">{!! BaseHelper::clean($title) !!}</h2>
            @endif

            @if ($decorativeText)
                <div class="page-lead__script">{!! BaseHelper::clean($decorativeText) !!}</div>
            @endif

            @if ($subtitle)
                <p class="page-lead__subtitle">{!! BaseHelper::clean($subtitle) !!}</p>
            @endif
        </div>

        @if ($description)
            <div class="page-lead__description">
                {!! BaseHelper::clean(nl2br($description)) !!}
            </div>
        @endif

    </div>
</section>
