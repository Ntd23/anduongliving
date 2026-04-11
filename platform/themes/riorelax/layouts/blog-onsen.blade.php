@extends(Theme::getThemeNamespace('layouts.base'))

@section('main')
    <header class="header-area header-three">
        @if (theme_option('header_top_enabled', true))
            {!! Theme::partial('header-top') !!}
        @endif

        {!! Theme::partial('header') !!}
    </header>

    @php
        $heroImage = Theme::get('breadcrumbBackgroundImage');
        $page = Theme::get('page');
        $eyebrow = $page?->getMetaData('blog_onsen_eyebrow', true);
        $onsenMenus = collect([
            [
                'label' => $page?->getMetaData('blog_onsen_nav_label_1', true),
                'target' => $page?->getMetaData('blog_onsen_nav_target_1', true),
            ],
            [
                'label' => $page?->getMetaData('blog_onsen_nav_label_2', true),
                'target' => $page?->getMetaData('blog_onsen_nav_target_2', true),
            ],
            [
                'label' => $page?->getMetaData('blog_onsen_nav_label_3', true),
                'target' => $page?->getMetaData('blog_onsen_nav_target_3', true),
            ],
            [
                'label' => $page?->getMetaData('blog_onsen_nav_label_4', true),
                'target' => $page?->getMetaData('blog_onsen_nav_target_4', true),
            ],
            [
                'label' => $page?->getMetaData('blog_onsen_nav_label_5', true),
                'target' => $page?->getMetaData('blog_onsen_nav_target_5', true),
            ],
        ])
            ->map(function ($item) {
                $target = trim((string) ($item['target'] ?? ''));

                if ($target === '') {
                    return null;
                }

                $fragment = parse_url($target, PHP_URL_FRAGMENT);
                $target = $fragment ?: ltrim($target, '#');

                return [
                    'label' => $item['label'],
                    'target' => '#' . $target,
                ];
            })
            ->filter(fn ($item) => filled($item['label']) && filled($item['target']) && $item['target'] !== '#')
            ->values();
    @endphp

    <style>
        .blog-onsen-page {
            background: #030303;
            color: #f2ead8;
            overflow: hidden;
            --onsen-gold: #aa8a3a;
            --onsen-gold-bright: #c3a24d;
            --onsen-gold-dark: #8c712f;
            --onsen-ivory: #fff7e2;
        }

        .blog-onsen-stage {
            margin: 0 auto;
            max-width: 1720px;
            padding: 0 18px;
        }

        .blog-onsen-hero {
            align-items: center;
            background-color: #090909;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            display: flex;
            justify-content: center;
            min-height: clamp(220px, 28vw, 420px);
            padding: 124px 24px 40px;
            position: relative;
            text-align: center;
        }

        .blog-onsen-hero::before {
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.42) 0%, rgba(0, 0, 0, 0.58) 100%);
            content: "";
            inset: 0;
            position: absolute;
        }

        .blog-onsen-hero__inner {
            position: relative;
            z-index: 1;
        }

        .blog-onsen-hero__eyebrow {
            color: rgba(255, 248, 232, 0.92);
            font-family: "Cormorant Garamond", Georgia, serif;
            font-size: clamp(22px, 2vw, 38px);
            letter-spacing: 0.12em;
            margin: 0 0 14px;
            text-transform: uppercase;
        }

        .blog-onsen-hero__title {
            color: #fff8ea;
            font-size: clamp(26px, 3vw, 52px);
            letter-spacing: 0.1em;
            line-height: 1.25;
            margin: 0;
            text-shadow: 0 10px 26px rgba(0, 0, 0, 0.42);
        }

        .blog-onsen-content {
            margin: 0 auto;
            max-width: 1440px;
            padding: 28px 24px 88px;
            position: relative;
        }

        .blog-onsen-nav {
            margin: -6px auto 36px;
            max-width: 1120px;
            position: relative;
            z-index: 2;
        }

        .blog-onsen-nav__row {
            display: grid;
            gap: 20px;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .blog-onsen-nav__row--top {
            grid-template-columns: repeat(3, minmax(220px, 360px));
            max-width: 1120px;
        }

        .blog-onsen-nav__row--bottom {
            grid-template-columns: repeat(2, minmax(220px, 360px));
            max-width: 740px;
            margin-bottom: 0;
        }

        .blog-onsen-nav__item {
            align-items: center;
            background: linear-gradient(180deg, var(--onsen-gold-bright) 0%, var(--onsen-gold) 100%);
            box-shadow: inset 0 1px 0 rgba(255, 248, 225, 0.18);
            color: var(--onsen-ivory);
            display: flex;
            font-size: clamp(15px, 1vw, 22px);
            justify-content: center;
            letter-spacing: 0.08em;
            min-height: 52px;
            padding: 14px 52px 14px 20px;
            position: relative;
            text-align: center;
            text-decoration: none;
            transition: background 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
        }

        .blog-onsen-nav__item:hover {
            background: linear-gradient(180deg, #cfaf58 0%, #b79640 100%);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.22), inset 0 1px 0 rgba(255, 248, 225, 0.2);
            color: #fff;
            transform: translateY(-1px);
        }

        .blog-onsen-nav__item::after {
            border-bottom: 2px solid rgba(255, 247, 226, 0.95);
            border-right: 2px solid rgba(255, 247, 226, 0.95);
            content: "";
            height: 12px;
            position: absolute;
            right: 22px;
            top: 50%;
            transform: translateY(-65%) rotate(45deg);
            width: 12px;
        }

        .blog-onsen-shell {
            margin: 0 auto;
            max-width: 1520px;
        }

        .blog-onsen-page .ck-content {
            color: rgba(245, 238, 224, 0.92);
            font-family: 'Yu Mincho', 'Noto Serif JP', 'Times New Roman', Georgia, serif;
            font-size: 16px;
            line-height: 1.95;
            text-align: center;
        }

        .blog-onsen-page .ck-content > *:first-child {
            margin-top: 0;
        }

        .blog-onsen-page .ck-content > *:last-child {
            margin-bottom: 0;
        }

        .blog-onsen-page .ck-content [id] {
            scroll-margin-top: 24px;
        }

        .blog-onsen-page .ck-content img {
            display: block;
            margin: 0 auto 42px;
            max-width: 100%;
        }

        .blog-onsen-page .ck-content h2,
        .blog-onsen-page .ck-content h3,
        .blog-onsen-page .ck-content h4 {
            color: #f7edd3;
            letter-spacing: 0.08em;
        }

        .blog-onsen-page .ck-content img {
            display: block;
            margin: 0 auto 42px;
            max-width: 100%;
        }

        .blog-onsen-page .ck-content .onsen-menu {
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(3, minmax(220px, 360px));
            justify-content: center;
            margin: 0 auto 22px;
            max-width: 1120px;
        }

        .blog-onsen-page .ck-content .onsen-menu--two {
            grid-template-columns: repeat(2, minmax(220px, 360px));
            max-width: 760px;
        }

        .blog-onsen-page .ck-content .onsen-panel {
            margin: 0 0 18px;
        }

        .blog-onsen-page .ck-content .onsen-panel summary {
            align-items: center;
            background: linear-gradient(180deg, var(--onsen-gold-bright) 0%, var(--onsen-gold) 100%);
            box-shadow: inset 0 1px 0 rgba(255, 248, 225, 0.18);
            color: var(--onsen-ivory);
            cursor: pointer;
            display: flex;
            font-size: clamp(15px, 1vw, 22px);
            justify-content: center;
            letter-spacing: 0.08em;
            list-style: none;
            min-height: 52px;
            padding: 18px 64px 18px 24px;
            position: relative;
            text-align: center;
            transition: background 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
        }

        .blog-onsen-page .ck-content .onsen-panel summary::-webkit-details-marker {
            display: none;
        }

        .blog-onsen-page .ck-content .onsen-panel summary:hover {
            background: linear-gradient(180deg, #cfaf58 0%, #b79640 100%);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.22), inset 0 1px 0 rgba(255, 248, 225, 0.2);
            transform: translateY(-1px);
        }

        .blog-onsen-page .ck-content .onsen-panel[open] summary {
            background: linear-gradient(180deg, #d2b05a 0%, #a98634 100%);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.24), inset 0 1px 0 rgba(255, 248, 225, 0.22);
        }

        .blog-onsen-page .ck-content .onsen-panel summary::after {
            border-bottom: 2px solid rgba(255, 247, 226, 0.95);
            border-right: 2px solid rgba(255, 247, 226, 0.95);
            content: "";
            height: 12px;
            position: absolute;
            right: 26px;
            top: 50%;
            transform: translateY(-65%) rotate(45deg);
            transition: transform 0.2s ease;
            width: 12px;
        }

        .blog-onsen-page .ck-content .onsen-panel[open] summary::after {
            transform: translateY(-35%) rotate(225deg);
        }

        .blog-onsen-page .ck-content .onsen-panel > *:not(summary) {
            background: #11110f;
            border: 1px solid rgba(195, 162, 77, 0.42);
            border-top: 0;
            box-shadow: inset 0 1px 0 rgba(255, 248, 225, 0.04);
            margin: 0 auto;
            max-width: 1040px;
            padding: 24px 28px;
            text-align: left;
        }

        .blog-onsen-page .ck-content p {
            margin-bottom: 20px;
        }

        .blog-onsen-page .ck-content a {
            color: #e2c36d;
        }

        @media (max-width: 991px) {
            .blog-onsen-hero {
                padding-top: 124px;
            }

            .blog-onsen-content {
                padding: 40px 16px 64px;
            }

            .blog-onsen-nav {
                margin-bottom: 28px;
            }

            .blog-onsen-nav__row--top,
            .blog-onsen-nav__row--bottom,
            .blog-onsen-page .ck-content .onsen-menu,
            .blog-onsen-page .ck-content .onsen-menu--two {
                grid-template-columns: 1fr;
                max-width: 460px;
            }

            .blog-onsen-page .ck-content .onsen-panel summary {
                min-height: 60px;
                padding-right: 52px;
            }

            .blog-onsen-page .ck-content .onsen-panel > *:not(summary) {
                padding: 18px 20px;
            }
        }
    </style>

    <section class="blog-onsen-page">
        <div class="blog-onsen-stage">
            <section class="blog-onsen-hero" @if ($heroImage) style="background-image: url('{{ RvMedia::getImageUrl($heroImage) }}');" @endif>
                <div class="blog-onsen-hero__inner">
                    @if (filled($eyebrow))
                        <p class="blog-onsen-hero__eyebrow">{{ $eyebrow }}</p>
                    @endif
                    <h1 class="blog-onsen-hero__title">{{ Theme::get('pageTitle') }}</h1>
                </div>
            </section>

            <section class="blog-onsen-content">
                @if ($onsenMenus->isNotEmpty())
                    <div class="blog-onsen-nav">
                        @if ($onsenMenus->take(3)->isNotEmpty())
                            <div class="blog-onsen-nav__row blog-onsen-nav__row--top">
                                @foreach ($onsenMenus->take(3) as $item)
                                    <a href="{{ $item['target'] }}" class="blog-onsen-nav__item">{{ $item['label'] }}</a>
                                @endforeach
                            </div>
                        @endif

                        @if ($onsenMenus->slice(3, 2)->isNotEmpty())
                            <div class="blog-onsen-nav__row blog-onsen-nav__row--bottom">
                                @foreach ($onsenMenus->slice(3, 2) as $item)
                                    <a href="{{ $item['target'] }}" class="blog-onsen-nav__item">{{ $item['label'] }}</a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                <div class="blog-onsen-shell">
                    {!! Theme::content() !!}
                </div>
            </section>
        </div>
    </section>

    {!! Theme::partial('footer') !!}
@endsection
