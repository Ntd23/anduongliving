<?php

namespace Botble\Blog\Http\Resources;

use Botble\Blog\Models\Post;
use Botble\Media\Facades\RvMedia;
use Botble\Shortcode\Facades\Shortcode;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Post
 */
class PostResource extends JsonResource
{
    public function toArray($request): array
    {
        $author = $this->author;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'url' => $this->url,
            'description' => $this->description,
            'content' => Shortcode::compile((string) $this->content, true)->toHtml(),
            'image' => $this->image ? RvMedia::url($this->image) : null,
            'categories' => CategoryResource::collection($this->categories),
            'tags' => TagResource::collection($this->tags),
            'views' => (int) $this->views,
            'author' => $author ? [
                'name' => method_exists($author, 'getMetaData')
                    ? ($author->getMetaData('display_name', true) ?: $author->name)
                    : $author->name,
                'avatar_url' => data_get($author, 'avatar_url'),
                'bio' => method_exists($author, 'getMetaData') ? $author->getMetaData('bio', true) : null,
                'socials' => collect(['facebook', 'twitter', 'instagram', 'behance', 'linkedin'])
                    ->map(function (string $social) use ($author): ?array {
                        if (! method_exists($author, 'getMetaData')) {
                            return null;
                        }

                        $url = $author->getMetaData($social, true);

                        if (! $url) {
                            return null;
                        }

                        return [
                            'name' => $social,
                            'url' => $url,
                        ];
                    })
                    ->filter()
                    ->values()
                    ->all(),
            ] : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
