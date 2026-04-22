@php
    use Botble\Media\Facades\RvMedia;

    $backgroundImage = $shortcode->background_image
        ? RvMedia::getImageUrl($shortcode->background_image)
        : null;

    $title = trim((string) $shortcode->title);
    $description = trim((string) $shortcode->description);
    $buttonText = trim((string) $shortcode->button_text);
    $buttonUrl = trim((string) $shortcode->button_url);
@endphp

<section
    style="
        position: relative;
        min-height: 100vh;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        padding: 80px 20px;
        background-image: url('{{ $backgroundImage }}');
        background-position: center center;
        background-repeat: no-repeat;
        background-size: cover;
    "
>
    <div
        style="
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.42);
            z-index: 1;
        "
    ></div>

    <div
        style="
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 1280px;
            text-align: center;
            color: #fff;
            padding: 0 20px;
            margin-top: 120px;
        "
    >
        @if($title)
            <h1
                style="
                    max-width: 1100px;
                    margin: 0 auto 24px;
                    font-size: clamp(36px, 4vw, 84px);
                    line-height: 1.06;
                    font-weight: 700;
                    letter-spacing: -0.03em;
                    color: #ffffff;
                    text-shadow: 0 10px 28px rgba(0,0,0,0.28);
                "
            >
                {!! BaseHelper::clean($title) !!}
            </h1>
        @endif

        @if($description)
            <div
                style="
                    max-width: 900px;
                    margin: 0 auto;
                    font-size: clamp(18px, 1.45vw, 30px);
                    line-height: 1.7;
                    font-weight: 500;
                    color: rgba(255,255,255,0.96);
                    text-shadow: 0 6px 18px rgba(0,0,0,0.28);
                "
            >
                {!! BaseHelper::clean(nl2br($description)) !!}
            </div>
        @endif

        @if($buttonText && $buttonUrl)
            <div style="margin-top: 38px;">
                <a
                    href="{{ $buttonUrl }}"
                    style="
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        min-width: 220px;
                        padding: 16px 34px;
                        border-radius: 999px;
                        background: #8b5e3c;
                        color: #ffffff;
                        text-decoration: none;
                        font-size: 16px;
                        font-weight: 700;
                        letter-spacing: 0.03em;
                    "
                >
                    {!! BaseHelper::clean($buttonText) !!}
                </a>
            </div>
        @endif
    </div>
</section>