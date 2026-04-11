@php
    $fallbackImage = Theme::asset()->url('images/Spa-Collage-Showcase/Spa Collage Showcase.jpeg');
    $leftPanelImage = $shortcode->left_panel_image ? RvMedia::getImageUrl($shortcode->left_panel_image) : $fallbackImage;
    $rightPanelImage = $shortcode->right_panel_image ? RvMedia::getImageUrl($shortcode->right_panel_image) : $fallbackImage;
    $bottomImages = collect([
        $shortcode->bottom_image_1,
        $shortcode->bottom_image_2,
        $shortcode->bottom_image_3,
        $shortcode->bottom_image_4,
    ])->filter()->map(fn ($image) => RvMedia::getImageUrl($image))->values();
@endphp

<section class="spa-collage-showcase">
    <style>
        .spa-collage-showcase {
            background: #0c0908;
            padding: 0;
        }

        .spa-collage-showcase__inner {
            margin: 0 auto;
            max-width: min(100%, 3000px);
            width: 100%;
        }

        .spa-collage-showcase__top {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 924px;
        }

        .spa-collage-showcase__left,
        .spa-collage-showcase__right,
        .spa-collage-showcase__bottom-item {
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            position: relative;
        }

        .spa-collage-showcase__left::before,
        .spa-collage-showcase__right::before {
            content: "";
            inset: 0;
            position: absolute;
        }

        .spa-collage-showcase__left::before {
            background: linear-gradient(180deg, rgba(15, 10, 9, 0.56) 0%, rgba(15, 10, 9, 0.7) 100%);
        }

        .spa-collage-showcase__right::before {
            background: linear-gradient(180deg, rgba(16, 14, 11, 0.12) 0%, rgba(16, 14, 11, 0.24) 100%);
        }

        .spa-collage-showcase__left {
            align-items: flex-start;
            display: flex;
            justify-content: flex-start;
            padding: 220px 120px 140px;
        }

        .spa-collage-showcase__text {
            color: #fff;
            margin-left: auto;
            margin-right: 7%;
            max-width: 520px;
            position: relative;
            z-index: 1;
        }

        .spa-collage-showcase__title {
            color: #fff;
            font-size: clamp(24px, 1.45vw, 42px);
            letter-spacing: 0.12em;
            line-height: 1.55;
            margin: 0 0 10px;
        }

        .spa-collage-showcase__subtitle {
            color: rgba(255, 255, 255, 0.92);
            font-size: clamp(18px, 1vw, 28px);
            letter-spacing: 0.12em;
            line-height: 1.5;
            margin: 0 0 96px;
        }

        .spa-collage-showcase__description {
            color: rgba(255, 255, 255, 0.94);
            font-size: clamp(15px, 0.72vw, 21px);
            line-height: 2.15;
            max-width: 500px;
            white-space: pre-line;
        }

        .spa-collage-showcase__bottom {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            min-height: 434px;
        }

        .spa-collage-showcase__bottom-item::before {
            background: linear-gradient(180deg, rgba(8, 7, 6, 0.06) 0%, rgba(8, 7, 6, 0.18) 100%);
            content: "";
            inset: 0;
            position: absolute;
        }

        @media (max-width: 1199px) {
            .spa-collage-showcase__top {
                min-height: 686px;
            }

            .spa-collage-showcase__left {
                padding: 120px 56px 100px;
            }

            .spa-collage-showcase__bottom {
                min-height: 294px;
            }
        }

        @media (max-width: 991px) {
            .spa-collage-showcase__top {
                grid-template-columns: 1fr;
            }

            .spa-collage-showcase__left,
            .spa-collage-showcase__right {
                min-height: 392px;
            }

            .spa-collage-showcase__left {
                padding: 72px 28px 64px;
            }

            .spa-collage-showcase__text {
                margin-right: 0;
                max-width: 100%;
            }

            .spa-collage-showcase__bottom {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                min-height: auto;
            }

            .spa-collage-showcase__bottom-item {
                min-height: 182px;
            }
        }

        @media (max-width: 640px) {
            .spa-collage-showcase__left {
                padding: 56px 20px 48px;
            }

            .spa-collage-showcase__bottom {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="spa-collage-showcase__inner">
        <div class="spa-collage-showcase__top">
            <div class="spa-collage-showcase__left" style="background-image: url('{{ $leftPanelImage }}');">
                <div class="spa-collage-showcase__text">
                    @if ($shortcode->title)
                        <h2 class="spa-collage-showcase__title">{!! BaseHelper::clean($shortcode->title) !!}</h2>
                    @endif

                    @if ($shortcode->subtitle)
                        <div class="spa-collage-showcase__subtitle">{!! BaseHelper::clean($shortcode->subtitle) !!}</div>
                    @endif

                    @if ($shortcode->description)
                        <div class="spa-collage-showcase__description">{!! BaseHelper::clean($shortcode->description) !!}</div>
                    @endif
                </div>
            </div>

            <div class="spa-collage-showcase__right" style="background-image: url('{{ $rightPanelImage }}');"></div>
        </div>

        @if ($bottomImages->isNotEmpty())
            <div class="spa-collage-showcase__bottom">
                @foreach ($bottomImages as $image)
                    <div class="spa-collage-showcase__bottom-item" style="background-image: url('{{ $image }}');"></div>
                @endforeach
            </div>
        @endif
    </div>
</section>
