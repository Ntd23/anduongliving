<?php

namespace Botble\Theme\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\Language\Facades\Language;
use Botble\Media\Facades\RvMedia;
use Botble\Theme\Supports\ThemeSupport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ThemeOptionController extends BaseApiController
{
    public function publicOptions(Request $request)
    {
        $language = $this->resolveLanguage($request);

        if ($language['requested'] && ! $language['code']) {
            return $this->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Theme options language is not supported.');
        }

        $this->setLanguageContext($language['code'], $language['locale']);

        $logo = theme_option('logo');
        $socialLinks = ThemeSupport::getSocialLinks();

        return $this
            ->httpResponse()
            ->setData([
                'locale' => $language['locale'],
                'site_title' => theme_option('site_title') ?: theme_option('site_name'),
                'logo' => $logo,
                'logo_url' => $logo ? RvMedia::getImageUrl($logo) : null,
                'breadcrumb_background_image' => theme_option('breadcrumb_background_image'),
                'breadcrumb_background_image_url' => theme_option('breadcrumb_background_image')
                    ? RvMedia::getImageUrl(theme_option('breadcrumb_background_image'))
                    : null,
                'background_footer' => theme_option('background_footer'),
                'background_footer_url' => theme_option('background_footer')
                    ? RvMedia::getImageUrl(theme_option('background_footer'))
                    : null,
                'header_top_enabled' => theme_option('header_top_enabled', true),
                'header_sticky_enabled' => theme_option('header_sticky_enabled', 'yes'),
                'header_button_label' => theme_option('header_button_label'),
                'header_button_url' => theme_option('header_button_url'),
                'opening_hours' => theme_option('opening_hours'),
                'hotline' => theme_option('hotline'),
                'email' => theme_option('email'),
                'social_links' => collect($socialLinks)
                    ->map(fn ($socialLink) => [
                        'name' => $socialLink->getName(),
                        'url' => $socialLink->getUrl(),
                        'icon' => $socialLink->getIcon(),
                        'image' => $socialLink->getImage(),
                        'image_url' => $socialLink->getImage() ? RvMedia::getImageUrl($socialLink->getImage()) : null,
                        'color' => $socialLink->getColor(),
                        'background_color' => $socialLink->getBackgroundColor(),
                    ])
                    ->filter(fn (array $socialLink) => ! empty($socialLink['url']))
                    ->values()
                    ->all(),
            ])
            ->toApiResponse();
    }

    protected function setLanguageContext(?string $languageCode, ?string $languageLocale): void
    {
        if ($languageLocale) {
            App::setLocale($languageLocale);
            Language::setCurrentLocale($languageLocale);
        }

        if ($languageCode) {
            Language::setCurrentLocaleCode($languageCode);
        }
    }

    protected function resolveLanguage(Request $request): array
    {
        $rawLanguage = trim((string) $request->input('lang', $request->input('locale', '')));
        $defaultLanguage = $this->defaultLanguage();

        if (! Schema::hasTable('languages')) {
            return [
                'requested' => $rawLanguage !== '',
                'code' => $rawLanguage === '' ? $defaultLanguage['code'] : null,
                'locale' => $rawLanguage === '' ? $defaultLanguage['locale'] : null,
            ];
        }

        if ($rawLanguage === '') {
            return [
                'requested' => false,
                'code' => $defaultLanguage['code'],
                'locale' => $defaultLanguage['locale'],
            ];
        }

        $normalized = str_replace('-', '_', $rawLanguage);
        $candidates = array_values(array_unique(array_filter([
            $normalized,
            str_replace('_', '-', $normalized),
        ])));

        $language = DB::table('languages')
            ->select('lang_code', 'lang_locale')
            ->whereIn('lang_code', $candidates)
            ->orWhereIn('lang_locale', $candidates)
            ->first();

        if (! $language) {
            $shortCodes = array_values(array_unique(array_filter(array_map(
                static fn (string $value): string => preg_split('/[-_]/', $value)[0] ?? '',
                $candidates
            ))));

            if ($shortCodes) {
                $language = DB::table('languages')
                    ->select('lang_code', 'lang_locale')
                    ->whereIn('lang_code', $shortCodes)
                    ->orWhereIn('lang_locale', $shortCodes)
                    ->first();
            }
        }

        return [
            'requested' => true,
            'code' => $language->lang_code ?? null,
            'locale' => $language->lang_locale ?? null,
        ];
    }

    protected function defaultLanguage(): array
    {
        if (! Schema::hasTable('languages')) {
            return [
                'code' => null,
                'locale' => null,
            ];
        }

        $language = DB::table('languages')
            ->select('lang_code', 'lang_locale')
            ->where('lang_is_default', 1)
            ->first();

        if (! $language) {
            $language = DB::table('languages')
                ->select('lang_code', 'lang_locale')
                ->orderByDesc('lang_is_default')
                ->orderBy('lang_id')
                ->first();
        }

        return [
            'code' => $language->lang_code ?? null,
            'locale' => $language->lang_locale ?? null,
        ];
    }
}
