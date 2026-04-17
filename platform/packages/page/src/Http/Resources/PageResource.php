<?php

namespace Botble\Page\Http\Resources;

use Botble\Base\Facades\BaseHelper;
use Botble\Media\Facades\RvMedia;
use Botble\Page\Models\Page;
use Botble\Shortcode\Facades\Shortcode;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * @mixin Page
 */
class PageResource extends JsonResource
{
    public function toArray($request): array
    {
        $compiledContent = Shortcode::compile((string) $this->content, true)->toHtml();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'content' => $compiledContent,
            'image' => $this->image ? RvMedia::url($this->image) : null,
            'template' => $this->template,
            'status' => $this->status,
            'seo' => $this->resolveSeo($compiledContent),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    protected function resolveSeo(string $compiledContent): array
    {
        return BaseHelper::isHomepage($this->getKey())
            ? $this->resolveHomepageSeo($compiledContent)
            : $this->resolvePageSeo($compiledContent);
    }

    protected function resolvePageSeo(string $compiledContent): array
    {
        $meta = $this->getMetaData('seo_meta', true);
        $meta = is_array($meta) ? $meta : [];

        $title = $meta['seo_title'] ?? $this->name;
        $description = $meta['seo_description'] ?? $this->description ?: $this->excerptFromContent($compiledContent);
        $image = ! empty($meta['seo_image'])
            ? RvMedia::getImageUrl($meta['seo_image'])
            : ($this->image ? RvMedia::url($this->image) : null);
        $robots = ($meta['index'] ?? 'index') === 'noindex'
            ? 'noindex, nofollow'
            : 'index, follow';

        return [
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'robots' => $robots,
            'canonical_path' => $this->buildPagePath((string) $this->slug),
            'localizations' => $this->resolveLocalizations(),
            'received' => (bool) (
                ! empty($meta['seo_title']) ||
                ! empty($meta['seo_description']) ||
                ! empty($meta['seo_image']) ||
                ($meta['index'] ?? 'index') === 'noindex'
            ),
        ];
    }

    protected function resolveHomepageSeo(string $compiledContent): array
    {
        $themeSeoTitle = function_exists('theme_option') ? theme_option('seo_title') : null;
        $themeSeoDescription = function_exists('theme_option') ? theme_option('seo_description') : null;
        $themeSeoImage = function_exists('theme_option') ? theme_option('seo_og_image') : null;
        $themeSeoIndex = function_exists('theme_option') ? theme_option('seo_index', true) : true;
        $siteTitle = function_exists('theme_option') ? theme_option('site_title') : null;

        $title = $themeSeoTitle ?: $siteTitle ?: $this->name;
        $description = $themeSeoDescription ?: $this->description ?: $this->excerptFromContent($compiledContent);
        $image = $themeSeoImage
            ? RvMedia::getImageUrl($themeSeoImage)
            : ($this->image ? RvMedia::url($this->image) : null);
        $robots = filter_var($themeSeoIndex, FILTER_VALIDATE_BOOLEAN)
            ? 'index, follow'
            : 'noindex, nofollow';

        return [
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'robots' => $robots,
            'canonical_path' => '/',
            'localizations' => $this->resolveLocalizations(),
            'received' => (bool) ($themeSeoTitle || $themeSeoDescription || $themeSeoImage || ! filter_var($themeSeoIndex, FILTER_VALIDATE_BOOLEAN)),
        ];
    }

    protected function resolveLocalizations(): array
    {
        if (! Schema::hasTable('languages')) {
            return [];
        }

        $languageQuery = DB::table('languages')
            ->select(['lang_code', 'lang_locale']);

        if (Schema::hasColumn('languages', 'lang_is_default')) {
            $languageQuery->addSelect('lang_is_default');
        }

        $languages = $languageQuery->get()->keyBy('lang_code');

        if ($languages->isEmpty()) {
            return [];
        }

        $defaultLanguage = $languages->first(fn ($language) => (bool) ($language->lang_is_default ?? false));
        $defaultLocale = $defaultLanguage->lang_locale ?? null;

        $localizations = [];

        if ($defaultLocale) {
            $localizations[$defaultLocale] = [
                'locale' => $defaultLocale,
                'path' => BaseHelper::isHomepage($this->getKey())
                    ? '/'
                    : $this->buildLocalizedPath($defaultLocale, (string) $this->slug, true),
            ];
        }

        if (Schema::hasTable('pages_translations')) {
            $translationQuery = DB::table('pages_translations')
                ->where('pages_id', $this->getKey())
                ->select(['lang_code']);

            if (Schema::hasColumn('pages_translations', 'slug')) {
                $translationQuery->addSelect('slug');
            }

            foreach ($translationQuery->get() as $translation) {
                $language = $languages->get($translation->lang_code);

                if (! $language?->lang_locale) {
                    continue;
                }

                $localizedSlug = $translation->slug ?? $this->slug;

                $localizations[$language->lang_locale] = [
                    'locale' => $language->lang_locale,
                    'path' => BaseHelper::isHomepage($this->getKey())
                        ? $this->buildLocalizedPath($language->lang_locale, '', (bool) ($language->lang_is_default ?? false))
                        : $this->buildLocalizedPath($language->lang_locale, (string) $localizedSlug, (bool) ($language->lang_is_default ?? false)),
                ];
            }
        }

        return array_values($localizations);
    }

    protected function buildLocalizedPath(string $locale, string $slug = '', bool $isDefaultLocale = false): string
    {
        $pagePath = $this->buildPagePath($slug);

        if ($isDefaultLocale) {
            return $pagePath;
        }

        if ($pagePath === '/') {
            return '/' . $locale;
        }

        return '/' . $locale . $pagePath;
    }

    protected function buildPagePath(string $slug): string
    {
        if (BaseHelper::isHomepage($this->getKey()) || $slug === '') {
            return '/';
        }

        return '/' . ltrim($slug, '/');
    }

    protected function excerptFromContent(string $compiledContent): ?string
    {
        $plainText = trim(strip_tags(BaseHelper::cleanShortcodes($compiledContent)));

        return $plainText !== '' ? Str::limit($plainText, 160) : null;
    }
}
