<style>
    @media (max-width: 991.98px) {
        body {
            padding-bottom: calc(88px + env(safe-area-inset-bottom));
        }

        body.mobile-menu-open {
            overflow: hidden;
        }

        .header-area.header-three .header-top.second-header,
        .header-area.header-three .menu-area .second-menu,
        .btn-toggle-menu-mobile {
            display: none !important;
        }

        .header-area.header-three .menu-area,
        .header-area.header-three .menu-area.sticky-menu {
            background: transparent !important;
            box-shadow: none !important;
            margin-bottom: 0 !important;
            position: relative !important;
            top: auto !important;
        }

        .riorelax-mobile-nav {
            position: relative;
            z-index: 80;
        }

        .riorelax-mobile-bar {
            background: #050505;
            bottom: 0;
            box-shadow: 0 -10px 28px rgba(0, 0, 0, 0.22);
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            left: 0;
            padding-bottom: env(safe-area-inset-bottom);
            position: fixed;
            right: 0;
            z-index: 90;
        }

        .riorelax-mobile-bar__item {
            align-items: center;
            background: #050505;
            border: 0;
            border-inline-end: 1px solid rgba(255, 255, 255, 0.14);
            color: #fff;
            display: flex;
            flex-direction: column;
            gap: 10px;
            justify-content: center;
            min-height: 88px;
            padding: 10px 8px 14px;
            text-align: center;
            text-decoration: none;
            width: 100%;
        }

        .riorelax-mobile-bar__item:last-child {
            border-inline-end: 0;
        }

        .riorelax-mobile-bar__item--primary {
            background: #8b6b45;
        }

        .riorelax-mobile-bar__item:hover,
        .riorelax-mobile-bar__item:focus {
            color: #fff;
        }

        .riorelax-mobile-bar__icon {
            font-size: 28px;
            line-height: 1;
        }

        .riorelax-mobile-bar__label {
            display: -webkit-box;
            font-size: 11px;
            letter-spacing: 0.08em;
            line-height: 1.2;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
            text-transform: uppercase;
        }

        .riorelax-mobile-panel {
            display: none;
            inset: 0;
            position: fixed;
            z-index: 1100;
        }

        .riorelax-mobile-nav.is-open .riorelax-mobile-panel {
            display: block;
        }

        .riorelax-mobile-panel__backdrop {
            background: rgba(0, 0, 0, 0.5);
            border: 0;
            inset: 0;
            position: absolute;
            width: 100%;
        }

        .riorelax-mobile-panel__sheet {
            background: #f7f3ee;
            color: #141414;
            height: 100%;
            inset-inline-start: 0;
            overflow: hidden;
            position: relative;
            width: 100%;
        }

        .riorelax-mobile-panel__inner {
            height: 100%;
            overflow-y: auto;
            padding: 24px 20px calc(120px + env(safe-area-inset-bottom));
        }

        .riorelax-mobile-panel__head {
            align-items: flex-start;
            display: flex;
            gap: 16px;
            justify-content: space-between;
        }

        .riorelax-mobile-panel__brand a {
            color: #141414;
            display: inline-flex;
            text-decoration: none;
        }

        .riorelax-mobile-panel__brand img {
            height: auto;
            max-height: 64px;
            width: auto;
        }

        .riorelax-mobile-panel__brand-text {
            font-family: var(--heading-font), serif;
            font-size: 30px;
            letter-spacing: 0.06em;
            line-height: 1.1;
        }

        .riorelax-mobile-panel__close {
            align-items: center;
            background: transparent;
            border: 0;
            color: #141414;
            display: inline-flex;
            flex-direction: column;
            gap: 8px;
            padding: 0;
        }

        .riorelax-mobile-panel__close-label {
            font-family: var(--heading-font), serif;
            font-size: 20px;
            letter-spacing: 0.08em;
            line-height: 1;
            text-transform: lowercase;
        }

        .riorelax-mobile-panel__close-box {
            align-items: center;
            background: #5a4036;
            color: #fff;
            display: inline-flex;
            font-size: 24px;
            height: 54px;
            justify-content: center;
            width: 54px;
        }

        .riorelax-mobile-panel__divider {
            background: #141414;
            height: 2px;
            margin: 20px 0 28px;
            width: 100%;
        }

        .riorelax-mobile-menu-rows,
        .riorelax-mobile-links-grid,
        .riorelax-mobile-language-grid {
            display: grid;
            gap: 14px 18px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .riorelax-mobile-menu-rows {
            gap: 14px;
            grid-template-columns: 1fr;
        }

        .riorelax-mobile-menu-rows__row {
            display: grid;
            gap: 18px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .riorelax-mobile-menu-list,
        .riorelax-mobile-links-grid__column,
        .riorelax-mobile-language-grid {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .riorelax-mobile-menu-list {
            display: grid;
            gap: 12px;
        }

        .riorelax-mobile-menu-list--child {
            gap: 8px;
            margin-top: 10px;
        }

        .riorelax-mobile-menu-link {
            align-items: center;
            background: #0a0a0a;
            color: #fff;
            display: flex;
            gap: 12px;
            min-height: 52px;
            padding: 14px 16px;
            text-decoration: none;
        }

        .riorelax-mobile-menu-link:hover,
        .riorelax-mobile-menu-link:focus,
        .riorelax-mobile-menu-link.is-active {
            color: #fff;
        }

        .riorelax-mobile-menu-link--child {
            background: transparent;
            border-bottom: 1px solid rgba(20, 20, 20, 0.14);
            color: #141414;
            min-height: 0;
            padding: 0 0 10px;
        }

        .riorelax-mobile-menu-link__arrow,
        .riorelax-mobile-links-grid__arrow {
            border-bottom: 1.5px solid currentColor;
            border-right: 1.5px solid currentColor;
            flex: 0 0 10px;
            height: 10px;
            transform: rotate(-45deg);
            width: 10px;
        }

        .riorelax-mobile-menu-link__text {
            font-size: 16px;
            letter-spacing: 0.05em;
            line-height: 1.3;
            word-break: break-word;
        }

        .riorelax-mobile-links-grid {
            margin-top: 20px;
        }

        .riorelax-mobile-links-grid__column {
            display: grid;
            gap: 10px;
        }

        .riorelax-mobile-links-grid__column a,
        .riorelax-mobile-language-grid a {
            align-items: center;
            color: #141414;
            display: flex;
            gap: 12px;
            line-height: 1.35;
            padding: 4px 0;
            text-decoration: none;
        }

        .riorelax-mobile-links-grid__column a:hover,
        .riorelax-mobile-links-grid__column a:focus,
        .riorelax-mobile-language-grid a:hover,
        .riorelax-mobile-language-grid a:focus {
            color: #8b6b45;
        }

        .riorelax-mobile-section {
            margin-top: 24px;
        }

        .riorelax-mobile-section__title {
            background: #8b6b45;
            color: #fff;
            font-size: 15px;
            letter-spacing: 0.12em;
            margin-bottom: 16px;
            padding: 12px 16px;
            text-align: center;
            text-transform: uppercase;
        }

        .riorelax-mobile-contact {
            margin-top: 32px;
            text-align: center;
        }

        .riorelax-mobile-contact__title {
            color: #141414;
            font-family: var(--heading-font), serif;
            font-size: clamp(28px, 7vw, 46px);
            line-height: 1.2;
            margin: 0 0 14px;
        }

        .riorelax-mobile-contact__phone {
            align-items: center;
            color: #141414;
            display: inline-flex;
            font-family: var(--heading-font), serif;
            font-size: clamp(30px, 10vw, 66px);
            gap: 14px;
            line-height: 1;
            text-decoration: none;
        }

        .riorelax-mobile-contact__phone i {
            font-size: 0.65em;
        }

        .riorelax-mobile-contact__meta {
            color: rgba(20, 20, 20, 0.78);
            font-size: 15px;
            letter-spacing: 0.04em;
            line-height: 1.8;
            margin-top: 10px;
        }
    }

    @media (max-width: 575.98px) {
        .riorelax-mobile-panel__inner {
            padding-inline: 18px;
        }

        .riorelax-mobile-menu-rows__row,
        .riorelax-mobile-links-grid,
        .riorelax-mobile-language-grid {
            gap: 12px 14px;
        }

        .riorelax-mobile-menu-link {
            padding-inline: 14px;
        }

        .riorelax-mobile-menu-link__text,
        .riorelax-mobile-links-grid__column a,
        .riorelax-mobile-language-grid a {
            font-size: 15px;
        }
    }
</style>

@php
    $bookingLabel = trim(strip_tags((string) BaseHelper::clean(theme_option('header_button_label'))));
    $bookingLabel = $bookingLabel ?: __('Book now');
    $bookingUrl = theme_option('header_button_url') ?: (is_plugin_active('hotel') ? route('public.rooms') : route('public.index'));

    $hotline = trim((string) theme_option('hotline'));
    $openingHours = theme_option('opening_hours');
    $email = trim((string) theme_option('email'));
    $addressText = trim(preg_replace('/\s+/', ' ', strip_tags((string) theme_option('address'))));
    $mapUrl = $addressText
        ? 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($addressText)
        : route('public.index');

    $socialLinks = collect(json_decode(theme_option('social_links')))->map(function ($social) {
        return collect($social)->pluck('value', 'key');
    })->filter(fn ($social) => filled($social->get('url')));

    $utilityLinks = collect();

    if ($socialLinks->isNotEmpty()) {
        $utilityLinks = $utilityLinks->merge($socialLinks->map(function ($social) {
            return [
                'url' => $social->get('url'),
                'label' => strtoupper((string) $social->get('name')),
                'target' => '_blank',
            ];
        }));
    }

    if (is_plugin_active('hotel')) {
        if (auth('customer')->check()) {
            $utilityLinks->push([
                'url' => route('customer.overview'),
                'label' => __('My account'),
                'target' => null,
            ]);
        } else {
            $utilityLinks->push([
                'url' => route('customer.login'),
                'label' => __('Login'),
                'target' => null,
            ]);
            $utilityLinks->push([
                'url' => route('customer.register'),
                'label' => __('Register'),
                'target' => null,
            ]);
        }
    }

    if ($email) {
        $utilityLinks->push([
            'url' => 'mailto:' . $email,
            'label' => __('Email us'),
            'target' => null,
        ]);
    }

    if ($addressText) {
        $utilityLinks->push([
            'url' => $mapUrl,
            'label' => __('View map'),
            'target' => '_blank',
        ]);
    }

    $utilityColumns = [
        $utilityLinks->slice(0, (int) ceil($utilityLinks->count() / 2))->values(),
        $utilityLinks->slice((int) ceil($utilityLinks->count() / 2))->values(),
    ];
@endphp

<div class="riorelax-mobile-nav d-lg-none">
    <nav class="riorelax-mobile-bar" aria-label="{{ __('Mobile quick actions') }}">
        <a class="riorelax-mobile-bar__item riorelax-mobile-bar__item--primary" href="{{ $bookingUrl }}">
            <span class="riorelax-mobile-bar__icon" aria-hidden="true"><i class="fal fa-edit"></i></span>
            <span class="riorelax-mobile-bar__label">{{ $bookingLabel }}</span>
        </a>

        <a class="riorelax-mobile-bar__item" href="{{ $hotline ? 'tel:' . preg_replace('/\s+/', '', $hotline) : route('public.index') }}">
            <span class="riorelax-mobile-bar__icon" aria-hidden="true"><i class="fal fa-phone-alt"></i></span>
            <span class="riorelax-mobile-bar__label">{{ __('Call') }}</span>
        </a>

        <a class="riorelax-mobile-bar__item" href="{{ $mapUrl }}" @if ($addressText) target="_blank" rel="noopener noreferrer" @endif>
            <span class="riorelax-mobile-bar__icon" aria-hidden="true"><i class="fal fa-map-marker-alt"></i></span>
            <span class="riorelax-mobile-bar__label">{{ __('Access') }}</span>
        </a>

        <button
            type="button"
            class="riorelax-mobile-bar__item"
            aria-controls="riorelax-mobile-panel"
            aria-expanded="false"
            aria-label="{{ __('Open menu') }}"
            data-mobile-menu-toggle
        >
            <span class="riorelax-mobile-bar__icon" aria-hidden="true"><i class="fal fa-bars"></i></span>
            <span class="riorelax-mobile-bar__label">{{ __('Menu') }}</span>
        </button>
    </nav>

    <div class="riorelax-mobile-panel" id="riorelax-mobile-panel" aria-hidden="true">
        <button type="button" class="riorelax-mobile-panel__backdrop" aria-label="{{ __('Close menu') }}" data-mobile-menu-close></button>

        <div class="riorelax-mobile-panel__sheet" role="dialog" aria-modal="true" aria-labelledby="riorelax-mobile-menu-title">
            <div class="riorelax-mobile-panel__inner">
                <div class="riorelax-mobile-panel__head">
                    <div class="riorelax-mobile-panel__brand" id="riorelax-mobile-menu-title">
                        <a href="{{ route('public.index') }}">
                            @if ($logo = theme_option('logo'))
                                <img src="{{ RvMedia::getImageUrl($logo) }}" alt="{{ theme_option('site_name') }}">
                            @else
                                <span class="riorelax-mobile-panel__brand-text">{{ theme_option('site_name') }}</span>
                            @endif
                        </a>
                    </div>

                    <button type="button" class="riorelax-mobile-panel__close" aria-label="{{ __('Close menu') }}" data-mobile-menu-close>
                        <span class="riorelax-mobile-panel__close-label">{{ __('Close') }}</span>
                        <span class="riorelax-mobile-panel__close-box" aria-hidden="true">
                            <i class="fal fa-times"></i>
                        </span>
                    </button>
                </div>

                <div class="riorelax-mobile-panel__divider"></div>

                {!! Menu::renderMenuLocation('main-menu', [
                    'view' => 'menu-mobile',
                ]) !!}

                @if ($utilityLinks->isNotEmpty())
                    <div class="riorelax-mobile-links-grid">
                        @foreach ($utilityColumns as $columnLinks)
                            <ul class="riorelax-mobile-links-grid__column">
                                @foreach ($columnLinks as $link)
                                    <li>
                                        <a href="{{ $link['url'] }}" @if ($link['target']) target="{{ $link['target'] }}" rel="noopener noreferrer" @endif>
                                            <span class="riorelax-mobile-links-grid__arrow" aria-hidden="true"></span>
                                            <span>{{ $link['label'] }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endforeach
                    </div>
                @endif

                @if (is_plugin_active('language') && ($supportedLocales = Language::getSupportedLocales()) && count($supportedLocales) > 1)
                    <div class="riorelax-mobile-section">
                        <div class="riorelax-mobile-section__title">{{ __('Language') }}</div>
                        <ul class="riorelax-mobile-language-grid">
                            {!! Theme::partial('language-switcher-mobile') !!}
                        </ul>
                    </div>
                @endif

                @if ($hotline || $openingHours || $addressText)
                    <div class="riorelax-mobile-contact">
                        <p class="riorelax-mobile-contact__title">{{ __('Contact & Reservation') }}</p>

                        @if ($hotline)
                            <a class="riorelax-mobile-contact__phone" href="tel:{{ preg_replace('/\s+/', '', $hotline) }}">
                                <i class="fas fa-phone-alt" aria-hidden="true"></i>
                                <span>{{ $hotline }}</span>
                            </a>
                        @endif

                        @if ($openingHours)
                            <div class="riorelax-mobile-contact__meta">{!! BaseHelper::clean($openingHours) !!}</div>
                        @endif

                        @if ($addressText)
                            <div class="riorelax-mobile-contact__meta">{{ $addressText }}</div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    if (!window.__riorelaxMobileHeaderBound) {
        window.__riorelaxMobileHeaderBound = true;

        document.addEventListener('click', function (event) {
            var toggle = event.target.closest('[data-mobile-menu-toggle]');
            var close = event.target.closest('[data-mobile-menu-close]');
            var link = event.target.closest('.riorelax-mobile-panel a');
            var menu = document.querySelector('.riorelax-mobile-nav');

            if (!menu) {
                return;
            }

            var setState = function (isOpen) {
                menu.classList.toggle('is-open', isOpen);
                document.body.classList.toggle('mobile-menu-open', isOpen);

                var panel = menu.querySelector('.riorelax-mobile-panel');
                var button = menu.querySelector('[data-mobile-menu-toggle]');

                if (panel) {
                    panel.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
                }

                if (button) {
                    button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                }
            };

            if (toggle) {
                event.preventDefault();
                setState(!menu.classList.contains('is-open'));
                return;
            }

            if (close || link) {
                setState(false);
            }
        });

        document.addEventListener('keyup', function (event) {
            if (event.key === 'Escape') {
                var menu = document.querySelector('.riorelax-mobile-nav');

                if (!menu) {
                    return;
                }

                menu.classList.remove('is-open');
                document.body.classList.remove('mobile-menu-open');

                var panel = menu.querySelector('.riorelax-mobile-panel');
                var button = menu.querySelector('[data-mobile-menu-toggle]');

                if (panel) {
                    panel.setAttribute('aria-hidden', 'true');
                }

                if (button) {
                    button.setAttribute('aria-expanded', 'false');
                }
            }
        });
    }
</script>
