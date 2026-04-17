@php
    use Botble\Media\Facades\RvMedia;

    $title = $shortcode->title ?: 'Phở Avana';
    $description = $shortcode->description ?: '';
    $quoteText = $shortcode->quote_text ?: '';
    $leftImage = null;
    if (!empty($shortcode->left_image)) {
        $leftImage = RvMedia::getImageUrl($shortcode->left_image) ?: RvMedia::url($shortcode->left_image) ?: $shortcode->left_image;
    }

    $bottomImage = null;
    if (!empty($shortcode->bottom_image)) {
        $bottomImage = RvMedia::getImageUrl($shortcode->bottom_image) ?: RvMedia::url($shortcode->bottom_image) ?: $shortcode->bottom_image;
    }

    $backgroundColor = $shortcode->background_color ?: '#f3efeb';
    $accentColor = $shortcode->accent_color ?: '#9b6b43';
    $quoteColor = $shortcode->quote_color ?: '#9b6b43';
@endphp
@php
    $image = null;

    if (!empty($shortcode->background_image)) {
        $image = \Botble\Media\Facades\RvMedia::getImageUrl($shortcode->background_image) ?: \Botble\Media\Facades\RvMedia::url($shortcode->background_image) ?: $shortcode->background_image;
    } elseif (!empty($shortcode->background_url)) {
        $image = $shortcode->background_url;
    }
@endphp

@if($image)
    <img src="{{ $image }}" style="width:100%;">
@endif
<style>
.food-story-section {
    padding: 70px 0;
    background: {{ $backgroundColor }};
}

.food-story-container {
    max-width: 1320px;
    margin: 0 auto;
    padding: 0 24px;
}

.food-story-grid {
    display: grid;
    grid-template-columns: 1fr 1.4fr;
    grid-template-areas:
        "left content"
        "quote bottom";
    gap: 34px 34px;
    align-items: start;
}

.food-story-left {
    grid-area: left;
}

.food-story-content {
    grid-area: content;
    text-align: center;
    padding: 10px 30px 0;
}

.food-story-quote {
    grid-area: quote;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 18px 24px;
}

.food-story-bottom {
    grid-area: bottom;
}

.food-story-image {
    width: 100%;
    overflow: hidden;
}

.food-story-image img {
    width: 100%;
    height: auto;
    display: block;
    object-fit: cover;
}

.food-story-title {
    margin: 0;
    font-size: 56px;
    line-height: 1.15;
    font-weight: 700;
    color: #1b1b16;
}

.food-story-divider {
    width: 74px;
    height: 5px;
    background: {{ $accentColor }};
    margin: 24px auto 30px;
    border-radius: 999px;
}

.food-story-description {
    font-size: 20px;
    line-height: 1.8;
    color: #5a564f;
    max-width: 820px;
    margin: 0 auto;
}

.food-story-description p {
    margin: 0 0 24px;
}

.food-story-description p:last-child {
    margin-bottom: 0;
}

.food-story-quote-text {
    margin: 0;
    font-size: 26px;
    line-height: 1.7;
    font-style: italic;
    color: {{ $quoteColor }};
    max-width: 520px;
}

@media (max-width: 1199px) {
    .food-story-title {
        font-size: 46px;
    }

    .food-story-description {
        font-size: 18px;
    }

    .food-story-quote-text {
        font-size: 22px;
    }
}

@media (max-width: 991px) {
    .food-story-grid {
        grid-template-columns: 1fr;
        grid-template-areas:
            "left"
            "content"
            "bottom"
            "quote";
    }

    .food-story-content {
        padding: 0;
    }

    .food-story-title {
        font-size: 38px;
    }

    .food-story-description {
        font-size: 17px;
        line-height: 1.75;
    }

    .food-story-quote {
        padding: 0;
    }

    .food-story-quote-text {
        font-size: 20px;
        max-width: 100%;
    }
}

@media (max-width: 767px) {
    .food-story-section {
        padding: 50px 0;
    }

    .food-story-container {
        padding: 0 16px;
    }

    .food-story-grid {
        gap: 22px;
    }

    .food-story-title {
        font-size: 30px;
    }

    .food-story-divider {
        margin: 18px auto 22px;
    }

    .food-story-description {
        font-size: 16px;
    }

    .food-story-quote-text {
        font-size: 18px;
        line-height: 1.6;
    }
}
</style>

<section class="food-story-section">
    <div class="food-story-container">
        <div class="food-story-grid">
            <div class="food-story-left">
                @if ($leftImage)
                    <div class="food-story-image">
                        <img src="{{ $leftImage }}" alt="{{ $title }}">
                    </div>
                @endif
            </div>

            <div class="food-story-content">
                <h2 class="food-story-title">{{ $title }}</h2>
                <div class="food-story-divider"></div>

                @if ($description)
                    <div class="food-story-description">
                        {!! BaseHelper::clean(nl2br($description)) !!}
                    </div>
                @endif
            </div>

            <div class="food-story-quote">
                @if ($quoteText)
                    <p class="food-story-quote-text">
                        “{{ $quoteText }}”
                    </p>
                @endif
            </div>

            <div class="food-story-bottom">
                @if ($bottomImage)
                    <div class="food-story-image">
                        <img src="{{ $bottomImage }}" alt="{{ $title }}">
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>