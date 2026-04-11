@php
    $backgroundImage = $shortcode->background_image
        ? RvMedia::getImageUrl($shortcode->background_image)
        : Theme::asset()->url('images/logo-showcase-banner/logo-show.jpeg');
    $logoImage = $shortcode->logo_image ? RvMedia::getImageUrl($shortcode->logo_image) : null;
    $topLines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $shortcode->top_text))));
    $bottomLines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $shortcode->bottom_text))));
@endphp

<section class="logo-showcase-banner" style="background-image: url('{{ $backgroundImage }}');">
    <style>
        .logo-showcase-banner {
            align-items: center;
            background-color: #050505;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            display: flex;
            justify-content: center;
            min-height: min(100vh, 1300px);
            overflow: hidden;
            padding: 80px 24px 60px;
            position: relative;
        }

        .logo-showcase-banner::before {
            background: rgba(0, 0, 0, 0.46);
            content: "";
            inset: 0;
            position: absolute;
            z-index: 0;
        }

        .logo-showcase-banner__inner {
            align-items: center;
            color: #fff;
            display: flex;
            flex-direction: column;
            min-height: min(88vh, 1140px);
            position: relative;
            text-align: center;
            width: min(100%, 1180px);
            z-index: 1;
        }

        .logo-showcase-banner__top,
        .logo-showcase-banner__bottom {
            width: min(100%, 980px);
        }

        .logo-showcase-banner__top {
            margin-bottom: auto;
            padding-top: 18px;
        }

        .logo-showcase-banner__top p,
        .logo-showcase-banner__bottom p {
            color: rgba(255, 255, 255, 0.95);
            font-size: clamp(11.04px, 0.7245vw, 15.87px);
            letter-spacing: 0.08em;
            line-height: 2;
            margin: 0;
            text-shadow: 0 5px 18px rgba(0, 0, 0, 0.5);
        }

        .logo-showcase-banner__top p + p,
        .logo-showcase-banner__bottom p + p {
            margin-top: 8px;
        }

        .logo-showcase-banner__center {
            align-items: center;
            display: flex;
            flex: 1 1 auto;
            justify-content: center;
            width: 100%;
        }

        .logo-showcase-banner__logo {
            display: block;
            filter: drop-shadow(0 12px 32px rgba(0, 0, 0, 0.35));
            max-width: min(20.48vw, 205px);
            width: 100%;
        }

        .logo-showcase-banner__bottom {
            margin-top: auto;
            padding-bottom: 10px;
        }

        @media (max-width: 991px) {
            .logo-showcase-banner {
                min-height: min(92vh, 980px);
                padding: 48px 16px 36px;
            }

            .logo-showcase-banner__inner {
                min-height: min(82vh, 900px);
            }

            .logo-showcase-banner__top p,
            .logo-showcase-banner__bottom p {
                letter-spacing: 0.04em;
                line-height: 1.8;
            }

            .logo-showcase-banner__logo {
                max-width: min(34.56vw, 154px);
            }
        }
    </style>

    <div class="logo-showcase-banner__inner">
        @if ($topLines)
            <div class="logo-showcase-banner__top">
                @foreach ($topLines as $line)
                    <p>{!! BaseHelper::clean($line) !!}</p>
                @endforeach
            </div>
        @endif

        <div class="logo-showcase-banner__center">
            @if ($logoImage)
                <img class="logo-showcase-banner__logo" src="{{ $logoImage }}" alt="Logo showcase">
            @endif
        </div>

        @if ($bottomLines)
            <div class="logo-showcase-banner__bottom">
                @foreach ($bottomLines as $line)
                    <p>{!! BaseHelper::clean($line) !!}</p>
                @endforeach
            </div>
        @endif
    </div>
</section>
