<style>
    .media-showcase__grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.media-showcase__card {
    display: flex;
    flex-direction: column;
    transition: 0.3s;
}

.media-showcase__item {
    position: relative;
    overflow: hidden;
    aspect-ratio: 1 / 1;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
}

.media-showcase__item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .4s ease;
}

/* Hover effect */
.media-showcase__card:hover .media-showcase__item img {
    transform: scale(1.07);
}

.media-showcase__card:hover {
    transform: translateY(-5px);
}

/* Icon copy */
.media-showcase__copy {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 26px;
    height: 26px;
    cursor: pointer;
    z-index: 3;
    opacity: 0.8;
}

.media-showcase__copy:hover {
    opacity: 1;
}

/* TÊN ẢNH */
.media-showcase__name {
    margin-top: 10px;
    font-size: 18px;
    line-height: 1.4;
    color: #222;
    text-align: center;
    font-weight: 600;
    padding: 0 6px;
}

@media (max-width: 991px) {
    .media-showcase__grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 576px) {
    .media-showcase__grid {
        grid-template-columns: 1fr;
    }

    .media-showcase__name {
        font-size: 16px;
    }
}
.media-showcase {
    position: relative;
    padding: 60px 0;
    color: #222;
    background-color: transparent;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

.media-showcase::before {
    display: none;
}

.media-showcase .container {
    position: relative;
    z-index: 2;
}

.media-showcase__video {
    margin-bottom: 40px;
}

.media-showcase__video iframe {
    width: 100%;
    height: 520px;
    border: none;
    display: block;
}

.media-showcase__heading {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 24px;
}

.media-showcase__logo {
    flex: 0 0 auto;
}

.media-showcase__logo img {
    width: 54px;
    height: 54px;
    object-fit: contain;
    display: block;
}

.media-showcase__title {
    font-size: 56px;
    line-height: 1.1;
    margin: 0;
    color: #111;
    font-family: serif;
    font-weight: 600;
}

.media-showcase__grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
}

.media-showcase__card {
    display: flex;
    flex-direction: column;
}

.media-showcase__item {
    position: relative;
    overflow: hidden;
    aspect-ratio: 1 / 1;
    background: transparent;
    padding: 0;
}

.media-showcase__item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .3s ease;
}

.media-showcase__item:hover img {
    transform: scale(1.03);
}

.media-showcase__copy {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 28px;
    height: 28px;
    cursor: pointer;
    z-index: 3;
}

.media-showcase__copy::before,
.media-showcase__copy::after {
    content: "";
    position: absolute;
    width: 18px;
    height: 18px;
    border: 2px solid #fff;
    box-sizing: border-box;
    background: transparent;
}

.media-showcase__copy::before {
    top: 4px;
    left: 4px;
}

.media-showcase__copy::after {
    top: 0;
    left: 0;
}

.media-showcase__name {
    margin-top: 8px;
    font-size: 16px;
    line-height: 1.4;
    color: #333;
    text-align: center;
    font-weight: 500;
}

.media-showcase__buttons {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-top: 36px;
}

.media-showcase__buttons a {
    display: block;
    background: #b69149;
    padding: 18px 20px;
    text-align: center;
    color: #fff;
    font-size: 28px;
    text-decoration: none;
    transition: .3s;
}

.media-showcase__buttons a:hover {
    background: #9d7c3f;
    color: #fff;
}

.media-showcase__notice {
    position: fixed;
    right: 20px;
    bottom: 20px;
    background: #111;
    color: #fff;
    padding: 10px 14px;
    font-size: 14px;
    border-radius: 6px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(10px);
    transition: .25s ease;
    z-index: 9999;
}

.media-showcase__notice.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

@media (max-width: 991px) {
    .media-showcase__grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .media-showcase__title {
        font-size: 40px;
    }
}

@media (max-width: 767px) {
    .media-showcase__video iframe {
        height: 260px;
    }

    .media-showcase__logo img {
        width: 40px;
        height: 40px;
    }

    .media-showcase__title {
        font-size: 30px;
    }

    .media-showcase__buttons {
        grid-template-columns: 1fr;
    }

    .media-showcase__name {
        font-size: 15px;
    }
}
</style>

@php
    use Botble\Media\Facades\RvMedia;

    $items = [];

    for ($i = 1; $i <= 8; $i++) {
        $image = $shortcode->{"image_$i"} ?? null;
        $link = $shortcode->{"image_link_$i"} ?? '';
        $name = $shortcode->{"image_name_$i"} ?? '';

        if (! empty($image)) {
            $items[] = [
                'image' => $image,
                'link' => $link,
                'name' => $name,
            ];
        }
    }

    if (! function_exists('getYoutubeEmbedUrlMediaShowcase')) {
        function getYoutubeEmbedUrlMediaShowcase($url) {
            preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&]+)/', $url, $matches);
            return isset($matches[1]) ? 'https://www.youtube.com/embed/' . $matches[1] : '';
        }
    }

    $embedUrl = getYoutubeEmbedUrlMediaShowcase($shortcode->youtube_url ?? '');
    $backgroundImage = ! empty($shortcode->background_image) ? RvMedia::getImageUrl($shortcode->background_image) : '';
    $instagramLogo = ! empty($shortcode->instagram_logo) ? RvMedia::getImageUrl($shortcode->instagram_logo) : '';
@endphp

<section
    class="media-showcase"
    @if ($backgroundImage)
        style="background-image: url('{{ $backgroundImage }}');"
    @endif
>
    <div class="container">
        @if ($embedUrl)
            <div class="media-showcase__video">
                <iframe src="{{ $embedUrl }}" allowfullscreen></iframe>
            </div>
        @endif

        <div class="media-showcase__heading">
            @if ($instagramLogo)
                <div class="media-showcase__logo">
                    <img src="{{ $instagramLogo }}" alt="logo">
                </div>
            @endif

            <h2 class="media-showcase__title">
                {{ $shortcode->title ?: 'Instagram' }}
            </h2>
        </div>

        @if (count($items))
            <div class="media-showcase__grid">
                @foreach ($items as $item)
                    <div class="media-showcase__card">
                        <div class="media-showcase__item">
                            <span
                                class="media-showcase__copy"
                                data-copy="{{ $item['link'] ?: RvMedia::getImageUrl($item['image']) }}"
                                title="Copy link"
                            ></span>

                            <img
                                src="{{ RvMedia::getImageUrl($item['image']) }}"
                                alt="{{ $item['name'] ?: 'gallery image' }}"
                            >
                        </div>

                        <div class="media-showcase__name">
                            {{ $item['name'] ?? '' }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="media-showcase__buttons">
            @if (! empty($shortcode->button_left_text))
                <a href="{{ $shortcode->button_left_url ?: 'javascript:void(0)' }}" target="_blank">
                    {{ $shortcode->button_left_text }}
                </a>
            @endif

            @if (! empty($shortcode->button_right_text))
                <a href="{{ $shortcode->button_right_url ?: 'javascript:void(0)' }}" target="_blank">
                    {{ $shortcode->button_right_text }}
                </a>
            @endif
        </div>
    </div>

    <div class="media-showcase__notice" id="media-showcase-copy-notice">Đã copy link</div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.media-showcase__copy');
    const notice = document.getElementById('media-showcase-copy-notice');

    buttons.forEach(function (button) {
        button.addEventListener('click', async function (e) {
            e.preventDefault();
            e.stopPropagation();

            const text = this.getAttribute('data-copy') || '';
            if (!text) return;

            try {
                await navigator.clipboard.writeText(text);

                if (notice) {
                    notice.classList.add('show');
                    setTimeout(() => {
                        notice.classList.remove('show');
                    }, 1500);
                }
            } catch (error) {
                console.log('Copy failed', error);
            }
        });
    });
});
</script>