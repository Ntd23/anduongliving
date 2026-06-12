<section class="shortcode-services feature-area2 p-relative fix" style="background: #f7f5f1;">
    @if($leftImage = $shortcode->left_image)
        <div class="feature-img shortcode-services__background">
            <img src="{{ RvMedia::getImageURL($leftImage) }}" alt="{{ $shortcode->title }}" class="img" />
        </div>
    @endif
    <div class="container">
        <div @class([
            'row justify-content-center align-items-center shortcode-services__row',
            'shortcode-services__row--single' => ! $shortcode->right_floating_image,
        ])>
            <div class="shortcode-services__content">
                <div class="feature-content s-about-content">
                    @if($shortcode->title || $shortcode->subtitle)
                        <div class="feature-title pb-20">
                            @if($subtitle = $shortcode->subtitle)
                                <h5>{{ $subtitle }}</h5>
                            @endif

                            @if($title = $shortcode->title)
                                <h2>
                                    {!! BaseHelper::clean($title) !!}
                                </h2>
                            @endif
                        </div>
                    @endif

                    @if($description = $shortcode->description)
                        <p>{!! BaseHelper::clean($description) !!}</p>
                    @endif

                    @if($shortcode->button_label && $shortcode->button_url)
                        <div class="slider-btn mt-15">
                            <a href="{{ $shortcode->button_url }}" class="btn ss-btn smoth-scroll">{{ $shortcode->button_label }}</a>
                        </div>
                    @endif
                </div>
            </div>

            @if($floatingImage = $shortcode->right_floating_image)
                <div class="shortcode-services__media">
                    <img src="{{ RvMedia::getImageURL($floatingImage) }}" alt="{{ $shortcode->title }}" />
                </div>
            @endif
        </div>
    </div>
</section>
