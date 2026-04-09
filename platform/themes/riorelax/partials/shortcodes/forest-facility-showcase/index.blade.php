@php
    $leftImage = $shortcode->left_image ? RvMedia::getImageUrl($shortcode->left_image) : null;
    $rightImage1 = $shortcode->right_image_1 ? RvMedia::getImageUrl($shortcode->right_image_1) : null;
    $rightImage2 = $shortcode->right_image_2 ? RvMedia::getImageUrl($shortcode->right_image_2) : null;
@endphp

<section class="forest-facility-showcase">
    <style>
        .forest-facility-showcase {
            background: #f4eddc;
            overflow: hidden;
            padding: 0;
        }

        .forest-facility-showcase__layout {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(0, 1.45fr);
            margin: 0 auto;
            max-width: 2048px;
            min-height: 660px;
        }

        .forest-facility-showcase__left {
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            min-height: 660px;
            position: relative;
        }

        .forest-facility-showcase__left::after {
            background:
                linear-gradient(180deg, rgba(244, 237, 220, 0.02) 0%, rgba(244, 237, 220, 0.14) 70%, rgba(244, 237, 220, 0.74) 100%),
                linear-gradient(90deg, rgba(244, 237, 220, 0) 0%, rgba(244, 237, 220, 0.2) 48%, rgba(244, 237, 220, 0.98) 100%);
            content: "";
            inset: 0;
            position: absolute;
        }

        .forest-facility-showcase__stage {
            background: #f4eddc;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 58px 0 46px;
            position: relative;
            z-index: 1;
        }

        .forest-facility-showcase__content {
            color: #7e5d17;
            margin: 0 auto;
            max-width: min(100%, 920px);
            padding: 0 42px 36px;
            text-align: center;
            width: 100%;
        }

        .forest-facility-showcase__title {
            color: #8a6518;
            font-size: clamp(28px, 2.1vw, 48px);
            font-weight: 700;
            letter-spacing: 0.08em;
            line-height: 1.65;
            margin: 0 0 44px;
        }

        .forest-facility-showcase__description {
            color: #4f4639;
            font-size: clamp(14px, 0.92vw, 18px);
            line-height: 2;
            margin: 0 0 40px;
            text-align: center;
        }

        .forest-facility-showcase__line {
            background: #b19143;
            height: 1px;
            margin: 0 auto 10px;
            width: 240px;
        }

        .forest-facility-showcase__label {
            color: #b19143;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 15px;
            margin: 0 0 14px;
            text-transform: uppercase;
        }

        .forest-facility-showcase__button {
            background: #b19143;
            color: #fff;
            display: inline-flex;
            justify-content: center;
            min-width: 290px;
            padding: 13px 22px;
            text-align: center;
            text-decoration: none;
            transition: background-color .2s ease;
        }

        .forest-facility-showcase__button:hover {
            background: #9b7d33;
            color: #fff;
        }

        .forest-facility-showcase__right {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            overflow: hidden;
            width: 100%;
        }

        .forest-facility-showcase__right-item {
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            min-height: 414px;
            position: relative;
        }

        .forest-facility-showcase__right-item::after {
            background: linear-gradient(180deg, rgba(244, 237, 220, 0) 0%, rgba(244, 237, 220, 0.04) 76%, rgba(244, 237, 220, 0.72) 100%);
            content: "";
            inset: 0;
            position: absolute;
        }

        @media (max-width: 1199px) {
            .forest-facility-showcase__layout {
                grid-template-columns: minmax(0, 1fr) minmax(0, 1.15fr);
                min-height: 540px;
            }

            .forest-facility-showcase__left,
            .forest-facility-showcase__right-item {
                min-height: 540px;
            }

            .forest-facility-showcase__right-item {
                min-height: 340px;
            }
        }

        @media (max-width: 991px) {
            .forest-facility-showcase__layout {
                grid-template-columns: 1fr;
            }

            .forest-facility-showcase__left,
            .forest-facility-showcase__right-item {
                min-height: 360px;
            }

            .forest-facility-showcase__stage {
                padding: 40px 0 0;
            }

            .forest-facility-showcase__content {
                max-width: 100%;
                padding: 0 20px 28px;
            }
        }

        @media (max-width: 640px) {
            .forest-facility-showcase__right {
                grid-template-columns: 1fr;
            }

            .forest-facility-showcase__right-item {
                min-height: 280px;
            }
        }
    </style>

    <div class="forest-facility-showcase__layout">
        <div class="forest-facility-showcase__left" @if ($leftImage) style="background-image: url('{{ $leftImage }}');" @endif></div>

        <div class="forest-facility-showcase__stage">
            <div class="forest-facility-showcase__content">
                @if ($shortcode->title || $shortcode->subtitle)
                    <h2 class="forest-facility-showcase__title">
                        @if ($shortcode->title)
                            {!! BaseHelper::clean($shortcode->title) !!}
                        @endif
                        @if ($shortcode->subtitle)
                            <br>{!! BaseHelper::clean($shortcode->subtitle) !!}
                        @endif
                    </h2>
                @endif

                @if ($shortcode->description)
                    <div class="forest-facility-showcase__description">{!! BaseHelper::clean($shortcode->description) !!}</div>
                @endif

                @if ($shortcode->section_label)
                    <div class="forest-facility-showcase__line"></div>
                    <div class="forest-facility-showcase__label">{!! BaseHelper::clean($shortcode->section_label) !!}</div>
                @endif

                @if ($shortcode->button_label)
                    <a class="forest-facility-showcase__button" href="{{ $shortcode->button_url ?: 'javascript:void(0)' }}">
                        {!! BaseHelper::clean($shortcode->button_label) !!}
                    </a>
                @endif
            </div>

            <div class="forest-facility-showcase__right">
                <div class="forest-facility-showcase__right-item" @if ($rightImage1) style="background-image: url('{{ $rightImage1 }}');" @endif></div>
                <div class="forest-facility-showcase__right-item" @if ($rightImage2) style="background-image: url('{{ $rightImage2 }}');" @endif></div>
            </div>
        </div>
    </div>
</section>
