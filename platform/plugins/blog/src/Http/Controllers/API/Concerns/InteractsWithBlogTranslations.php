<?php

namespace Botble\Blog\Http\Controllers\API\Concerns;

use Botble\Blog\Models\Category;
use Botble\Blog\Models\Post;
use Botble\Blog\Models\Tag;
use Botble\Language\Facades\Language;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait InteractsWithBlogTranslations
{
    protected function isDefaultLanguageCode(?string $languageCode): bool
    {
        if (! $languageCode || ! Schema::hasTable('languages')) {
            return false;
        }

        $defaultLanguageCode = DB::table('languages')
            ->where('lang_is_default', 1)
            ->value('lang_code');

        return $defaultLanguageCode === $languageCode;
    }

    protected function resolveLanguageCodeFromRequest($request): ?string
    {
        $language = (string) $request->input('lang', $request->input('locale', ''));

        if ($language === '' || ! Schema::hasTable('languages')) {
            return null;
        }

        $normalized = str_replace('-', '_', trim($language));
        $candidates = array_values(array_unique(array_filter([
            $normalized,
            str_replace('_', '-', $normalized),
        ])));

        $languageCode = DB::table('languages')
            ->whereIn('lang_code', $candidates)
            ->value('lang_code');

        if ($languageCode) {
            return $languageCode;
        }

        $languageCode = DB::table('languages')
            ->whereIn('lang_locale', $candidates)
            ->value('lang_code');

        if ($languageCode) {
            return $languageCode;
        }

        $shortCodes = array_values(array_unique(array_filter(array_map(
            static fn (string $value): string => preg_split('/[-_]/', $value)[0] ?? '',
            $candidates
        ))));

        if (! $shortCodes) {
            return null;
        }

        return DB::table('languages')
            ->whereIn('lang_code', $shortCodes)
            ->orWhereIn('lang_locale', $shortCodes)
            ->value('lang_code') ?: null;
    }

    protected function setLanguageContext(?string $languageCode): void
    {
        if (! $languageCode || ! Schema::hasTable('languages')) {
            return;
        }

        $languageLocale = DB::table('languages')
            ->where('lang_code', $languageCode)
            ->value('lang_locale');

        if ($languageLocale) {
            App::setLocale($languageLocale);
            Language::setCurrentLocale($languageLocale);
        }

        Language::setCurrentLocaleCode($languageCode);
    }

    protected function applyTranslationFilter(Builder $query, string $table, string $keyColumn, ?string $languageCode): void
    {
        if (! $languageCode || $this->isDefaultLanguageCode($languageCode) || ! Schema::hasTable($table)) {
            return;
        }

        $qualifiedPrimaryKey = $this->qualifyPrimaryKeyForTranslationTable($keyColumn);

        $query->whereExists(function ($subQuery) use ($table, $keyColumn, $languageCode, $qualifiedPrimaryKey): void {
            $subQuery->selectRaw('1')
                ->from($table)
                ->whereColumn($table . '.' . $keyColumn, $qualifiedPrimaryKey)
                ->where($table . '.lang_code', $languageCode);
        });
    }

    protected function qualifyPrimaryKeyForTranslationTable(string $keyColumn): string
    {
        return match ($keyColumn) {
            'posts_id' => 'posts.id',
            'categories_id' => 'categories.id',
            'tags_id' => 'tags.id',
            default => $keyColumn,
        };
    }

    protected function translatePost(Post $post, ?string $languageCode): Post
    {
        if (! $languageCode || $this->isDefaultLanguageCode($languageCode) || ! Schema::hasTable('posts_translations')) {
            return $post;
        }

        $translation = DB::table('posts_translations')
            ->where('posts_id', $post->getKey())
            ->where('lang_code', $languageCode)
            ->first();

        if ($translation) {
            $post->setAttribute('name', $translation->name ?? $post->name);
            $post->setAttribute('description', $translation->description ?? $post->description);
            $post->setAttribute('content', $translation->content ?? $post->content);
        }

        if ($post->relationLoaded('categories')) {
            $post->setRelation(
                'categories',
                $post->categories->map(fn (Category $category) => $this->translateCategory($category, $languageCode))
            );
        }

        if ($post->relationLoaded('tags')) {
            $post->setRelation(
                'tags',
                $post->tags->map(fn (Tag $tag) => $this->translateTag($tag, $languageCode))
            );
        }

        return $post;
    }

    protected function translateCategory(Category $category, ?string $languageCode): Category
    {
        if (! $languageCode || $this->isDefaultLanguageCode($languageCode) || ! Schema::hasTable('categories_translations')) {
            return $category;
        }

        $translation = DB::table('categories_translations')
            ->where('categories_id', $category->getKey())
            ->where('lang_code', $languageCode)
            ->first();

        if (! $translation) {
            return $category;
        }

        $category->setAttribute('name', $translation->name ?? $category->name);
        $category->setAttribute('description', $translation->description ?? $category->description);

        return $category;
    }

    protected function translateTag(Tag $tag, ?string $languageCode): Tag
    {
        if (! $languageCode || $this->isDefaultLanguageCode($languageCode) || ! Schema::hasTable('tags_translations')) {
            return $tag;
        }

        $translation = DB::table('tags_translations')
            ->where('tags_id', $tag->getKey())
            ->where('lang_code', $languageCode)
            ->first();

        if (! $translation) {
            return $tag;
        }

        $tag->setAttribute('name', $translation->name ?? $tag->name);
        $tag->setAttribute('description', $translation->description ?? $tag->description);

        return $tag;
    }
}
