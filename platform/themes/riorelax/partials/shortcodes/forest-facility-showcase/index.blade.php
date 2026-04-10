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
            grid-template-columns: minmax(0, 1fr) minmax(0, 1.2fr) minmax(0, 1.4fr);
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
            justify-content: space-between;
            padding: 58px 40px;
            position: relative;
            z-index: 1;
        }

        .forest-facility-showcase__top {
            display: flex;
            justify-content: center;
            text-align: center;
            width: 100%;
            padding-bottom: 32px;
            transform: translateX(280px);
        }

        .forest-facility-showcase__bottom {
            align-items: flex-start;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            width: 100%;
            transform: translateX(0px);
        }

        .forest-facility-showcase__bottom-text {
            color: #7e5d17;
            padding: 0;
            text-align: left;
            width: 100%;
        }

        .forest-facility-showcase__title {
            color: #8a6518;
            font-size: clamp(18px, 1.5vw, 23px);
            font-weight: 700;
            letter-spacing: 0.08em;
            line-height: 1.65;
            margin: 0 auto;
            text-align: center;
        }

        .forest-facility-showcase__description {
            color: #4f4639;
            font-size: clamp(13px, 0.85vw, 16px);
            line-height: 2;
            margin: 0 0 36px;
            text-align: left;
        }

        .forest-facility-showcase__line {
            background: #b19143;
            height: 1px;
            margin: 0 0 10px;
            width: 240px;
        }

        .forest-facility-showcase__label {
            color: #b19143;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 15px;
            margin: 0 0 14px;
            text-align: left;
            text-transform: uppercase;
        }

        .forest-facility-showcase__button {
            background: #b19143;
            color: #fff;
            display: inline-flex;
            justify-content: center;
            min-width: 220px;
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
            align-self: center;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0;
            overflow: hidden;
            width: 100%;
        }

        .forest-facility-showcase__right-item {
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            min-height: 336px;
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
                grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) minmax(0, 1fr);
                min-height: 540px;
            }

            .forest-facility-showcase__left,
            .forest-facility-showcase__right-item {
                min-height: 540px;
            }
        }

        @media (max-width: 991px) {
            .forest-facility-showcase__layout {
                grid-template-columns: 1fr;
            }

            .forest-facility-showcase__left {
                min-height: 360px;
            }

            .forest-facility-showcase__right-item {
                min-height: 300px;
            }

            .forest-facility-showcase__stage {
                padding: 40px 24px;
            }

            .forest-facility-showcase__right {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .forest-facility-showcase__right {
                grid-template-columns: 1fr;
            }

            .forest-facility-showcase__right-item {
                min-height: 250px;
            }
        }
    </style>

    <div class="forest-facility-showcase__layout">
        <div class="forest-facility-showcase__left" @if ($leftImage) style="background-image: url('{{ $leftImage }}');"
        @endif></div>

        <div class="forest-facility-showcase__stage">
            <div class="forest-facility-showcase__top">
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
            </div>

            <div class="forest-facility-showcase__bottom">
                <div class="forest-facility-showcase__bottom-text">
                    @if ($shortcode->description)
                        <div class="forest-facility-showcase__description">
                            {!! BaseHelper::clean($shortcode->description) !!}
                        </div>
                    @endif

                    @if ($shortcode->section_label)
                        <div class="forest-facility-showcase__line"></div>
                        <div class="forest-facility-showcase__label">{!! BaseHelper::clean($shortcode->section_label) !!}
                        </div>
                    @endif

                    @if ($shortcode->button_label)
                        <a class="forest-facility-showcase__button"
                            href="{{ $shortcode->button_url ?: 'javascript:void(0)' }}">
                            {!! BaseHelper::clean($shortcode->button_label) !!}
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="forest-facility-showcase__right">
            <div class="forest-facility-showcase__right-item" @if ($rightImage1)
            style="background-image: url('{{ $rightImage1 }}');" @endif></div>
            <div class="forest-facility-showcase__right-item" @if ($rightImage2)
            style="background-image: url('{{ $rightImage2 }}');" @endif></div>
        </div>
    </div>
</section>