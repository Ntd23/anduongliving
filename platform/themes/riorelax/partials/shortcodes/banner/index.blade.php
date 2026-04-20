@php
    use Botble\Media\Facades\RvMedia;

    $backgroundImage = '';
    $imagePath = trim((string) ($shortcode->background_image ?? ''), '/');

    if ($imagePath !== '') {
        $backgroundImage = asset('storage/' . $imagePath);
    }

    $title = $shortcode->title ?: 'Ẩm Thực';
    $subtitle = $shortcode->subtitle ?: '';
@endphp

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Noto+Serif+JP:wght@400;600&display=swap" rel="stylesheet">

<style>
.banner-shortcode-wrapper {
    position: relative;
    width: 100vw;
    min-height: 100vh;
    margin-left: calc(50% - 50vw);
    margin-right: calc(50% - 50vw);
    overflow: hidden;
}

.banner-shortcode {
    position: relative;
    width: 100%;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #999;
    background-size: cover;
    background-position: center;
}

.banner-shortcode::before {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.35);
    z-index: 1;
}

.banner-shortcode-content {
    position: relative;
    z-index: 2;
    text-align: center;
    color: #fff;
    max-width: 900px;
    padding: 20px;
}

/* TITLE */
.banner-shortcode-title {
    font-family: 'Playfair Display', serif;
    font-size: 80px;
    font-weight: 600;
    margin: 0;
    color: #fff;
    letter-spacing: 2px;
}

/* SUBTITLE (đưa xuống dưới + nhẹ nhàng hơn) */
.banner-shortcode-subtitle {
    font-family: 'Noto Serif JP', serif;
    font-size: 18px;
    margin-top: 20px;
    color: #f5f5f5;
    letter-spacing: 2px;
    opacity: 0.9;
}

/* Responsive */
@media (max-width: 991px) {
    .banner-shortcode-title {
        font-size: 48px;
    }

    .banner-shortcode-subtitle {
        font-size: 16px;
    }
}

@media (max-width: 767px) {
    .banner-shortcode-title {
        font-size: 36px;
    }

    .banner-shortcode-subtitle {
        font-size: 14px;
    }
}
</style>

<div class="banner-shortcode-wrapper">
    <section
        class="banner-shortcode"
        @if(!empty($backgroundImage))
            style="background-image: url('{{ $backgroundImage }}');"
        @endif
    >
        <div class="banner-shortcode-content">

            <h1 class="banner-shortcode-title">
                {{ $title }}
            </h1>

            @if ($subtitle)
                <div class="banner-shortcode-subtitle">
                    {{ $subtitle }}
                </div>
            @endif

        </div>
    </section>
</div>