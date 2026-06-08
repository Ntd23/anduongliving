<section class="shortcode-horizontal-articles blog-area p-relative fix pt-90 pb-90">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-12">
                <div class="section-title center-align mb-50 text-center">
                    @if ($subtitle = $shortcode->subtitle)
                        <h5>{!! BaseHelper::clean($subtitle) !!}</h5>
                    @endif

                    @if ($title = $shortcode->title)
                        <h2>{!! BaseHelper::clean($title) !!}</h2>
                    @endif

                    @if ($description = $shortcode->description)
                        <p>{!! BaseHelper::clean($description) !!}</p>
                    @endif
                </div>
            </div>
        </div>

        @if ($posts->isNotEmpty())
            <div class="horizontal-articles-wrapper">
                <div class="horizontal-articles-list">
                    @foreach($posts as $post)
                        <article class="horizontal-article-item wow fadeInUp animated">
                            <div class="horizontal-article-thumb">
                                <a href="{{ $post->url }}" title="{{ $post->name }}">
                                    @if ($image = $post->image)
                                        <img src="{{ RvMedia::getImageUrl($image, 'medium') }}" alt="{{ $post->name }}">
                                    @else
                                        <div class="horizontal-article-no-image"></div>
                                    @endif
                                </a>
                            </div>
                            <div class="horizontal-article-content">
                                <div class="horizontal-article-date">{{ Theme::formatDate($post->created_at) }}</div>
                                <h3><a href="{{ $post->url }}">{{ $post->name }}</a></h3>

                                @if ($description = $post->description)
                                    <p>{!! BaseHelper::clean($description) !!}</p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>

<style>
    .shortcode-horizontal-articles .horizontal-articles-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scroll-snap-type: x mandatory;
        padding-bottom: 10px;
        margin-left: -15px;
        margin-right: -15px;
    }

    .shortcode-horizontal-articles .horizontal-articles-list {
        display: flex;
        gap: 30px;
        padding: 0 15px;
    }

    .shortcode-horizontal-articles .horizontal-article-item {
        flex: 0 0 calc(25% - 22.5px);
        scroll-snap-align: start;
        min-width: 280px;
        background: #fff;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
    }

    .shortcode-horizontal-articles .horizontal-article-thumb {
        width: 100%;
        overflow: hidden;
        min-height: 225px;
    }

    .shortcode-horizontal-articles .horizontal-article-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .35s ease;
    }

    .shortcode-horizontal-articles .horizontal-article-item:hover .horizontal-article-thumb img {
        transform: scale(1.03);
    }

    .shortcode-horizontal-articles .horizontal-article-no-image {
        width: 100%;
        height: 225px;
        background: #f4f4f4;
    }

    .shortcode-horizontal-articles .horizontal-article-content {
        padding: 25px;
    }

    .shortcode-horizontal-articles .horizontal-article-date {
        color: #9a9a9a;
        font-size: 13px;
        margin-bottom: 10px;
        display: inline-block;
    }

    .shortcode-horizontal-articles .horizontal-article-content h3 {
        font-size: 20px;
        margin-bottom: 15px;
        line-height: 1.3;
    }

    .shortcode-horizontal-articles .horizontal-article-content h3 a {
        color: #1f1f1f;
        transition: color .3s ease;
    }

    .shortcode-horizontal-articles .horizontal-article-content h3 a:hover {
        color: #d69f2f;
    }

    .shortcode-horizontal-articles .horizontal-article-content p {
        color: #6b6b6b;
        line-height: 1.75;
        max-height: 96px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
    }

    @media (max-width: 1199px) {
        .shortcode-horizontal-articles .horizontal-article-item {
            flex: 0 0 calc(33.3333% - 20px);
        }
    }

    @media (max-width: 991px) {
        .shortcode-horizontal-articles .horizontal-article-item {
            flex: 0 0 calc(50% - 15px);
        }
    }

    @media (max-width: 767px) {
        .shortcode-horizontal-articles .horizontal-article-item {
            flex: 0 0 100%;
        }

        .shortcode-horizontal-articles .horizontal-articles-wrapper {
            margin-left: 0;
            margin-right: 0;
        }
    }
</style>
