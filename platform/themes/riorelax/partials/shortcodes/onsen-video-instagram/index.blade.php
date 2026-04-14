@php
    $sectionId = trim((string) ($shortcode->section_id ?: 'onsen_video_instagram'));
    $backgroundImage = $shortcode->background_image ? RvMedia::getImageUrl($shortcode->background_image) : null;
    $backgroundColor = $shortcode->background_color ?: '#080202';
    $youtubeVideoId = $shortcode->youtube_video_id;
    $socials = [
        'instagram' => [
            'label' => 'Instagram',
            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><circle cx="17.5" cy="6.5" r="1.2"></circle></svg>',
        ],
        'facebook' => [
            'label' => 'Facebook',
            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8.5V6.8c0-.8.4-1.3 1.4-1.3H17V3h-2.4C12.1 3 11 4.5 11 6.5v2H9v3h2V21h3v-9.5h2.4l.4-3H14Z"></path></svg>',
        ],
        'x' => [
            'label' => 'X',
            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m4 4 6.9 8.7L4.4 20H7l5.1-5.8 4.6 5.8H20l-7-8.9L19.2 4h-2.6l-4.8 5.4L7.5 4H4Z"></path></svg>',
        ],
    ];
    $gallerySocialKey = $shortcode->gallery_social ?: 'instagram';
    $leftSocialKey = $shortcode->left_social ?: 'instagram';
    $rightSocialKey = $shortcode->right_social ?: 'instagram';
    $gallerySocial = $socials[$gallerySocialKey] ?? $socials['instagram'];
    $leftSocial = $socials[$leftSocialKey] ?? $socials['instagram'];
    $rightSocial = $socials[$rightSocialKey] ?? $socials['instagram'];
    $leftCaption = $shortcode->left_caption ?: 'Guest post images';
    $rightCaption = $shortcode->right_caption ?: 'Official Instagram';
    $galleryImages = collect($images ?? [])
        ->map(fn ($item) => $item['image'] ?? null)
        ->filter()
        ->map(fn ($image) => RvMedia::getImageUrl($image))
        ->filter()
        ->values();
@endphp

<section
    id="{{ $sectionId }}"
    class="onsen-video-instagram"
    style="
        --onsen-video-instagram-bg: {{ $backgroundColor }};
        @if ($backgroundImage) background-image: url('{{ $backgroundImage }}'); @endif
    "
>
    <style>
        .onsen-video-instagram {
            background-color: var(--onsen-video-instagram-bg);
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            box-sizing: border-box;
            color: #fff;
            left: 50%;
            margin-left: -50vw;
            overflow: hidden;
            padding: clamp(46px, 4.6vw, 78px) 24px clamp(54px, 5vw, 88px);
            position: relative;
            width: 100vw;
        }

        .onsen-video-instagram::before {
            background: rgba(0, 0, 0, 0.68);
            content: "";
            inset: 0;
            position: absolute;
        }

        .onsen-video-instagram__inner {
            margin: 0 auto;
            max-width: 1120px;
            position: relative;
            z-index: 1;
        }

        .onsen-video-instagram__video {
            aspect-ratio: 16 / 9;
            background: #111;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.42);
            margin: 0 auto clamp(38px, 4.4vw, 64px);
            overflow: hidden;
            position: relative;
            width: min(100%, 980px);
        }

        .onsen-video-instagram__video iframe {
            border: 0;
            display: block;
            height: 100%;
            width: 100%;
        }

        .onsen-video-instagram__heading {
            align-items: center;
            display: flex;
            justify-content: flex-start;
            margin: 0 auto 24px;
            max-width: 980px;
        }

        .onsen-video-instagram__title {
            align-items: center;
            color: #fff;
            display: inline-flex;
            font-family: 'Yu Mincho', 'Noto Serif JP', Georgia, serif;
            font-size: clamp(15px, 1vw, 24px);
            font-style: normal;
            font-weight: 700;
            letter-spacing: 0.04em;
            line-height: 1;
            margin: 0;
        }

        .onsen-video-instagram__social-icon {
            display: inline-flex;
            flex: 0 0 auto;
            height: 50px;
            margin-right: 9px;
            width: 28px;
        }

        .onsen-video-instagram__social-icon svg {
            display: block;
            fill: none;
            height: 100%;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 1.8;
            width: 100%;
        }

        .onsen-video-instagram__social-icon svg path {
            fill: currentColor;
            stroke: none;
        }

        .onsen-video-instagram__grid {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin: 0 auto;
            max-width: 980px;
        }

        .onsen-video-instagram__item {
            aspect-ratio: 1;
            background: rgba(255, 255, 255, 0.08);
            overflow: hidden;
            position: relative;
        }

        .onsen-video-instagram__item::after {
            border: 2px solid rgba(255, 255, 255, 0.88);
            content: "";
            height: 15px;
            position: absolute;
            right: 12px;
            top: 12px;
            width: 15px;
        }

        .onsen-video-instagram__item::before {
            border: 2px solid rgba(255, 255, 255, 0.62);
            content: "";
            height: 15px;
            position: absolute;
            right: 16px;
            top: 8px;
            width: 15px;
            z-index: 1;
        }

        .onsen-video-instagram__item img {
            display: block;
            height: 100%;
            object-fit: cover;
            transition: transform 0.28s ease;
            width: 100%;
        }

        .onsen-video-instagram__item:hover img {
            transform: scale(1.04);
        }

        .onsen-video-instagram__actions {
            display: grid;
            gap: 34px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            margin: clamp(24px, 2.6vw, 34px) auto 0;
            max-width: 980px;
        }

        .onsen-video-instagram__action {
            text-align: center;
        }

        .onsen-video-instagram__action::before {
            background: #a98e3d;
            content: "";
            display: block;
            height: 1px;
            margin: 0 0 12px;
            width: 100%;
        }

        .onsen-video-instagram__caption {
            color: #b49a47;
            font-family: 'Yu Mincho', 'Noto Serif JP', Georgia, serif;
            font-size: clamp(14px, 1vw, 17px);
            font-weight: 600;
            letter-spacing: 0.08em;
            line-height: 1.55;
            margin: 0 0 14px;
        }

        .onsen-video-instagram__button {
            align-items: center;
            background: #b49a47;
            color: #fff;
            display: flex;
            gap: 10px;
            font-family: 'Yu Mincho', 'Noto Serif JP', Georgia, serif;
            font-size: clamp(14px, 1vw, 18px);
            font-weight: 700;
            justify-content: center;
            letter-spacing: 0.04em;
            line-height: 1.4;
            min-height: 42px;
            padding: 10px 18px;
        }

        .onsen-video-instagram__button .onsen-video-instagram__social-icon {
            height: 22px;
            margin-right: 0;
            width: 22px;
        }

        @media (max-width: 767px) {
            .onsen-video-instagram {
                padding-left: 18px;
                padding-right: 18px;
            }

            .onsen-video-instagram__grid {
                gap: 8px;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .onsen-video-instagram__actions {
                gap: 18px;
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="onsen-video-instagram__inner">
        @if ($youtubeVideoId)
            <div class="onsen-video-instagram__video">
                <iframe
                    src="https://www.youtube.com/embed/{{ $youtubeVideoId }}?rel=0"
                    title="Youtube video"
                    allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    loading="lazy"
                ></iframe>
            </div>
        @endif

        <div class="onsen-video-instagram__heading">
            <h2 class="onsen-video-instagram__title">
                <span class="onsen-video-instagram__social-icon">{!! $gallerySocial['icon'] !!}</span>
                <span>{!! BaseHelper::clean($gallerySocial['label']) !!}</span>
            </h2>
        </div>

        @if ($galleryImages->isNotEmpty())
            <div class="onsen-video-instagram__grid">
                @foreach ($galleryImages as $image)
                    <div class="onsen-video-instagram__item">
                        <img src="{{ $image }}" alt="{{ strip_tags($gallerySocial['label']) }}">
                    </div>
                @endforeach
            </div>
        @endif

        <div class="onsen-video-instagram__actions">
            <div class="onsen-video-instagram__action">
                @if ($leftCaption)
                    <p class="onsen-video-instagram__caption">{!! BaseHelper::clean($leftCaption) !!}</p>
                @endif
                <div class="onsen-video-instagram__button">
                    <span class="onsen-video-instagram__social-icon">{!! $leftSocial['icon'] !!}</span>
                    <span>{!! BaseHelper::clean($leftSocial['label']) !!}</span>
                </div>
            </div>

            <div class="onsen-video-instagram__action">
                @if ($rightCaption)
                    <p class="onsen-video-instagram__caption">{!! BaseHelper::clean($rightCaption) !!}</p>
                @endif
                <div class="onsen-video-instagram__button">
                    <span class="onsen-video-instagram__social-icon">{!! $rightSocial['icon'] !!}</span>
                    <span>{!! BaseHelper::clean($rightSocial['label']) !!}</span>
                </div>
            </div>
        </div>
    </div>
</section>
