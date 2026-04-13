@php
    $siteName = theme_option('site_name');
    $logo = theme_option('logo');
    $email = trim((string) theme_option('email'));
    $hotline = trim((string) theme_option('hotline'));
    $address = trim((string) theme_option('address'));
    $openingHours = trim((string) theme_option('opening_hours'));

    $addressLines = preg_split('/\r\n|\r|\n/', $address) ?: [];
    $footerSidebar = trim(dynamic_sidebar('footer_sidebar'));
    $footerPanelOneSidebar = trim(dynamic_sidebar('footer_panel_1_sidebar'));
    $footerPanelTwoSidebar = trim(dynamic_sidebar('footer_panel_2_sidebar'));
@endphp

<style>
    .onsen-footer {
        background:
            radial-gradient(circle at top left, rgba(125, 67, 47, 0.12), transparent 28%),
            linear-gradient(180deg, #190705 0%, #120403 100%);
        color: #f7ede5;
        padding: 0;
        position: relative;
    }

    .onsen-footer a {
        color: inherit;
        text-decoration: none;
    }

    .onsen-footer__inner {
        margin: 0 auto;
        max-width: 1480px;
        padding: 28px 28px 46px;
    }

    .onsen-footer__grid {
        align-items: start;
        display: grid;
        gap: 28px;
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .onsen-footer__brand {
        padding: 28px 10px 32px 0;
    }

    .onsen-footer__sidebar .col-xl-2,
    .onsen-footer__sidebar .col-lg-2,
    .onsen-footer__sidebar .col-xl-4,
    .onsen-footer__sidebar .col-lg-4,
    .onsen-footer__sidebar .col-sm-6 {
        flex: 0 0 100%;
        max-width: 100%;
        padding: 0;
        width: 100%;
    }

    .onsen-footer__sidebar .footer-widget {
        margin: 0 0 26px;
    }

    .onsen-footer__sidebar .footer-widget:last-child {
        margin-bottom: 0;
    }

    .onsen-footer__sidebar .f-widget-title {
        margin-bottom: 14px;
    }

    .onsen-footer__sidebar .f-widget-title img {
        display: block;
        max-width: 240px;
    }

    .onsen-footer__sidebar .f-widget-title h2 {
        color: #fff8f2;
        font-family: "Cormorant Garamond", Georgia, serif;
        font-size: 20px;
        font-weight: 300;
        letter-spacing: 0.08em;
        line-height: 1.2;
        margin: 0;
        text-transform: uppercase;
    }

    .onsen-footer__sidebar .f-contact ul,
    .onsen-footer__sidebar .footer-link ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .onsen-footer__sidebar .f-contact li {
        margin: 0;
    }

    .onsen-footer__sidebar .f-contact li + li {
        margin-top: 4px;
    }

    .onsen-footer__sidebar .f-contact .icon {
        display: none;
    }

    .onsen-footer__sidebar .f-contact span {
        color: #fff5ee;
        display: block;
        font-family: "Cormorant Garamond", Georgia, serif;
        font-size: 21px;
        letter-spacing: 0.04em;
        line-height: 1.45;
    }

    .onsen-footer__sidebar .footer-link li {
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        margin: 0;
    }

    .onsen-footer__sidebar .footer-link a {
        color: #fff8f2;
        display: block;
        font-family: "Cormorant Garamond", Georgia, serif;
        font-size: 23px;
        letter-spacing: 0.08em;
        line-height: 1.2;
        padding: 11px 0;
        text-transform: uppercase;
        transition: color 0.2s ease, padding-left 0.2s ease;
    }

    .onsen-footer__sidebar .footer-link a:hover {
        color: #d6b28a;
        padding-left: 8px;
    }

    .onsen-footer__sidebar .form-newsletter form {
        display: grid;
        gap: 12px;
    }

    .onsen-footer__sidebar .form-newsletter input {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.18);
        color: #fff8f2;
        min-height: 50px;
        padding: 0 16px;
        width: 100%;
    }

    .onsen-footer__sidebar .form-newsletter input::placeholder {
        color: rgba(255, 248, 242, 0.65);
    }

    .onsen-footer__sidebar .form-newsletter button,
    .onsen-footer__sidebar .form-newsletter .btn {
        background: #d6b28a;
        border: 0;
        color: #190705;
        min-height: 50px;
        padding: 0 20px;
    }

    .onsen-footer__logo-wrap {
        align-items: flex-start;
        display: flex;
        gap: 18px;
        margin-bottom: 22px;
    }

    .onsen-footer__logo {
        flex: 0 0 auto;
        max-width: 240px;
    }

    .onsen-footer__logo img {
        display: block;
        max-width: 100%;
    }

    .onsen-footer__brand-copy {
        min-width: 0;
        padding-top: 6px;
    }

    .onsen-footer__eyebrow {
        color: #fff7ef;
        font-family: "Cormorant Garamond", Georgia, serif;
        font-size: clamp(22px, 2vw, 28px);
        letter-spacing: 0.22em;
        margin: 0 0 8px;
        text-transform: uppercase;
    }

    .onsen-footer__subcopy {
        color: rgba(255, 247, 239, 0.88);
        font-family: "Cormorant Garamond", Georgia, serif;
        font-size: 20px;
        letter-spacing: 0.08em;
        line-height: 1.3;
        margin: 0;
    }

    .onsen-footer__contact {
        margin-bottom: 28px;
    }

    .onsen-footer__contact-line {
        color: #fff5ee;
        font-family: "Cormorant Garamond", Georgia, serif;
        font-size: 21px;
        letter-spacing: 0.04em;
        line-height: 1.45;
        margin: 0;
    }

    .onsen-footer__contact-line + .onsen-footer__contact-line {
        margin-top: 4px;
    }

    .onsen-footer__menu .onsen-footer__menu-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .onsen-footer__menu .onsen-footer__menu-list li {
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        margin: 0;
    }

    .onsen-footer__menu .onsen-footer__menu-list a {
        color: #fff8f2;
        display: block;
        font-family: "Cormorant Garamond", Georgia, serif;
        font-size: 27px;
        letter-spacing: 0.08em;
        line-height: 1.2;
        padding: 11px 0;
        text-transform: uppercase;
        transition: color 0.2s ease, padding-left 0.2s ease;
    }

    .onsen-footer__menu .onsen-footer__menu-list a:hover {
        color: #d6b28a;
        padding-left: 8px;
    }

    .onsen-footer__panel {
        border: 1px solid rgba(255, 255, 255, 0.22);
        min-height: 100%;
        padding: 28px 30px 32px;
    }

    .onsen-footer__panel-sidebar .col-xl-2,
    .onsen-footer__panel-sidebar .col-lg-2,
    .onsen-footer__panel-sidebar .col-xl-4,
    .onsen-footer__panel-sidebar .col-lg-4,
    .onsen-footer__panel-sidebar .col-sm-6 {
        flex: 0 0 100%;
        max-width: 100%;
        padding: 0;
        width: 100%;
    }

    .onsen-footer__panel-sidebar .footer-widget,
    .onsen-footer__panel-sidebar .widget {
        margin: 0 0 24px;
    }

    .onsen-footer__panel-sidebar .footer-widget:last-child,
    .onsen-footer__panel-sidebar .widget:last-child {
        margin-bottom: 0;
    }

    .onsen-footer__panel-sidebar .f-widget-title,
    .onsen-footer__panel-sidebar .widget-title {
        margin: 0 0 16px;
    }

    .onsen-footer__panel-sidebar .f-widget-title h2,
    .onsen-footer__panel-sidebar .widget-title {
        color: #fff8f2;
        font-family: "Cormorant Garamond", Georgia, serif;
        font-size: 20px;
        font-weight: 300;
        letter-spacing: 0.08em;
        line-height: 1.25;
        text-transform: uppercase;
    }

    .onsen-footer__panel-sidebar .textwidget,
    .onsen-footer__panel-sidebar p,
    .onsen-footer__panel-sidebar li,
    .onsen-footer__panel-sidebar span,
    .onsen-footer__panel-sidebar label {
        color: rgba(255, 247, 239, 0.86);
    }

    .onsen-footer__panel-sidebar a {
        color: #fff8f2;
    }

    .onsen-footer__panel-sidebar .widget-social {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .onsen-footer__panel-sidebar .widget-social a {
        align-items: center;
        border: 1px solid rgba(255, 255, 255, 0.18);
        display: inline-flex;
        height: 54px;
        justify-content: center;
        width: 54px;
    }

    .onsen-footer__panel-sidebar .widget-social i {
        font-size: 22px;
    }

    .onsen-footer__panel-sidebar input,
    .onsen-footer__panel-sidebar textarea,
    .onsen-footer__panel-sidebar select {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.18);
        color: #fff8f2;
        padding: 12px 14px;
        width: 100%;
    }

    .onsen-footer__panel-sidebar input::placeholder,
    .onsen-footer__panel-sidebar textarea::placeholder {
        color: rgba(255, 248, 242, 0.65);
    }

    .onsen-footer__panel-sidebar button,
    .onsen-footer__panel-sidebar .btn {
        background: #d6b28a;
        border: 0;
        color: #190705;
    }

    @media (max-width: 1199.98px) {
        .onsen-footer__grid {
            grid-template-columns: 1fr;
        }

        .onsen-footer__brand {
            padding: 0;
        }
    }

    @media (max-width: 767.98px) {
        .onsen-footer__inner {
            padding: 24px 18px 34px;
        }

        .onsen-footer__logo-wrap {
            flex-direction: column;
        }

        .onsen-footer__sidebar .f-contact span,
        .onsen-footer__sidebar .footer-link a,
        .onsen-footer__panel-sidebar .f-widget-title h2,
        .onsen-footer__panel-sidebar .widget-title,
        .onsen-footer__menu .onsen-footer__menu-list a {
            font-size: 22px;
        }

        .onsen-footer__panel {
            padding: 20px 18px 22px;
        }
    }
</style>

<footer class="onsen-footer">
    <div class="onsen-footer__inner">
        <div class="onsen-footer__grid">
            <div class="onsen-footer__brand">
                @if ($footerSidebar !== '')
                    <div class="onsen-footer__sidebar">
                        {!! $footerSidebar !!}
                    </div>
                @else
                    <div class="onsen-footer__logo-wrap">
                        @if ($logo)
                            <div class="onsen-footer__logo">
                                <a href="{{ route('public.index') }}">
                                    <img src="{{ RvMedia::getImageUrl($logo) }}" alt="{{ $siteName }}">
                                </a>
                            </div>
                        @endif

                        <div class="onsen-footer__brand-copy">
                            <p class="onsen-footer__eyebrow">{{ $siteName }}</p>
                            <p class="onsen-footer__subcopy">Japanese Style Onsen Ryokan</p>
                        </div>
                    </div>

                    <div class="onsen-footer__contact">
                        @foreach ($addressLines as $line)
                            @if (filled(trim($line)))
                                <p class="onsen-footer__contact-line">{{ trim($line) }}</p>
                            @endif
                        @endforeach

                        @if ($hotline)
                            <p class="onsen-footer__contact-line">Tel {{ $hotline }}</p>
                        @endif

                        @if ($email)
                            <p class="onsen-footer__contact-line">{{ $email }}</p>
                        @endif

                        @if ($openingHours)
                            <p class="onsen-footer__contact-line">{{ $openingHours }}</p>
                        @endif
                    </div>

                    <div class="onsen-footer__menu">
                        {!! Menu::renderMenuLocation('main-menu', [
                            'view' => 'footer-menu',
                            'options' => ['class' => 'onsen-footer__menu-list'],
                        ]) !!}
                    </div>
                @endif
            </div>

            <div class="onsen-footer__panel">
                <div class="onsen-footer__panel-sidebar">
                    {!! $footerPanelOneSidebar !!}
                </div>
            </div>

            <div class="onsen-footer__panel">
                <div class="onsen-footer__panel-sidebar">
                    {!! $footerPanelTwoSidebar !!}
                </div>
            </div>
        </div>
    </div>
</footer>
