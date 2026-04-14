<?php

use Botble\ACL\Forms\ProfileForm;
use Botble\Base\Forms\FieldOptions\EditorFieldOption;
use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\EditorField;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Hotel\Forms\AmenityForm;
use Botble\Media\Facades\RvMedia;
use Botble\Page\Forms\PageForm;
use Botble\SimpleSlider\Forms\SimpleSliderItemForm;
use Botble\Theme\Supports\ThemeSupport;

register_page_template([
    'default' => __('Default'),
    'side-menu' => __('Side menu'),
    'full-menu' => __('Full menu'),
    'blog-sidebar' => __('Blog sidebar'),
    'blog-onsen' => __('Blog Onsen'),
    'full-width' => __('Full width'),
]);

register_sidebar([
    'id' => 'footer_sidebar',
    'name' => 'Footer sidebar',
    'description' => __('Area for footer widgets'),
]);

register_sidebar([
    'id' => 'footer_panel_1_sidebar',
    'name' => 'Footer panel 1',
    'description' => __('Area for the middle footer panel widgets'),
]);

register_sidebar([
    'id' => 'footer_panel_2_sidebar',
    'name' => 'Footer panel 2',
    'description' => __('Area for the right footer panel widgets'),
]);

register_sidebar([
    'id' => 'blog_sidebar',
    'name' => __('Blog sidebar'),
    'description' => __('Sidebar on the right of the blog detail site.'),
]);

register_sidebar([
    'id' => 'room_sidebar',
    'name' => __('Room details sidebar'),
    'description' => __('Sidebar in the room page'),
]);

register_sidebar([
    'id' => 'rooms_sidebar',
    'name' => __('Rooms sidebar'),
    'description' => __('Sidebar in the rooms page'),
]);

register_sidebar([
    'id' => 'service_sidebar',
    'name' => __('Service sidebar'),
    'description' => __('Sidebar in the service page'),
]);

$getBlogOnsenSectionChoices = static function (?string $content): array {
    $content = (string) $content;
    $defaultSectionIds = [
        'page-lead' => 'pageLead',
        'onsen-spa-item' => 'onsen_spa01_li01',
        'onsen-detail-info' => 'onsen_detail_info',
        'onsen-video-instagram' => 'onsen_video_instagram',
    ];

    if ($content === '') {
        return [];
    }

    preg_match_all('/\[([a-z0-9_-]+)([^\]]*)\]/i', $content, $matches, PREG_SET_ORDER);

    $choices = [];

    foreach ($matches as $match) {
        $shortcode = trim((string) ($match[1] ?? ''));
        $attributes = (string) ($match[2] ?? '');

        preg_match('/\bsection_id\s*=\s*(?:"([^"]+)"|\'([^\']+)\'|([^\s\]]+))/i', $attributes, $sectionIdMatch);

        $sectionId = trim((string) (($sectionIdMatch[1] ?? '') ?: ($sectionIdMatch[2] ?? '') ?: ($sectionIdMatch[3] ?? '')));
        $sectionId = $sectionId !== '' ? $sectionId : ($defaultSectionIds[$shortcode] ?? '');

        if ($sectionId === '') {
            continue;
        }

        preg_match('/\btitle\s*=\s*(?:"([^"]+)"|\'([^\']+)\'|([^\s\]]+))/i', $attributes, $titleMatch);

        $title = trim((string) ($titleMatch[1] ?? $titleMatch[2] ?? $titleMatch[3] ?? ''));
        $title = strip_tags(html_entity_decode($title, ENT_QUOTES, 'UTF-8'));

        $choices[$sectionId] = $title !== ''
            ? sprintf('%s (#%s)', $title, $sectionId)
            : sprintf('[%s] #%s', $shortcode, $sectionId);
    }

    return $choices;
};

RvMedia::setUploadPathAndURLToPublic()
    ->addSize('medium', 440, 340)
    ->addSize('small', 300, 340)
    ->addSize('room-image', 850, 460);

if (class_exists(PageForm::class)) {
    PageForm::extend(function (PageForm $form) use ($getBlogOnsenSectionChoices): void {
        $currentTemplate = $form->getModel()?->template ?: request()->input('template');

        $form
            ->addAfter(
                'template',
                'breadcrumb_background',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(__('Breadcrumb background'))
                    ->metadata()
                    ->toArray()
            )
            ->addAfter(
                'template',
                'breadcrumb',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Breadcrumb'))
                    ->choices(['0' => __('No'), '1' => __('Yes')])
                    ->metadata()
                    ->toArray()
            );

        if ($currentTemplate !== 'blog-onsen') {
            return;
        }

        $pageContent = request()->input('content');

        if (! is_string($pageContent) || $pageContent === '') {
            $pageContent = $form->getModel()?->content;
        }

        $blogOnsenTargetChoices = $getBlogOnsenSectionChoices($pageContent);

        $form->addAfter(
            'template',
            'blog_onsen_background',
            MediaImageField::class,
            MediaImageFieldOption::make()
                ->label(__('Blog Onsen background image'))
                ->metadata()
                ->toArray()
        );

        $form->addAfter(
            'template',
            'blog_onsen_eyebrow',
            TextField::class,
            TextFieldOption::make()
                ->label(__('Blog Onsen eyebrow text'))
                ->metadata()
                ->placeholder('ONSEN')
                ->toArray()
        );

        for ($i = 1; $i <= 5; $i++) {
            $form
                ->addAfter(
                    'template',
                    'blog_onsen_nav_label_' . $i,
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Blog Onsen button label ' . $i))
                        ->metadata()
                        ->placeholder(__('Button text ' . $i))
                        ->toArray()
                )
                ->addAfter(
                    'template',
                    'blog_onsen_nav_target_' . $i,
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('Blog Onsen button target ' . $i))
                        ->choices(array_merge(
                            ['' => __('Select a section')],
                            $blogOnsenTargetChoices,
                            collect([
                                $form->getModel()?->getMetaData('blog_onsen_nav_target_' . $i, true),
                                request()->input('blog_onsen_nav_target_' . $i),
                            ])
                                ->filter()
                                ->mapWithKeys(function ($value) {
                                    $value = trim((string) $value);
                                    $fragment = parse_url($value, PHP_URL_FRAGMENT);
                                    $value = $fragment ?: ltrim($value, '#');

                                    return $value !== '' ? [$value => '#' . $value] : [];
                                })
                                ->all()
                        ))
                        ->metadata()
                        ->helperText(__('This list is taken from shortcode section_id values in the current Blog Onsen page content.'))
                        ->toArray()
                );
        }
    }, 99);
}

app()->booted(function (): void {
    ThemeSupport::registerDateFormatOption();

    if (is_plugin_active('simple-slider')) {
        SimpleSliderItemForm::extend(function (SimpleSliderItemForm $form): void {
            $form
                ->addBefore(
                    'order',
                    'subtitle',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Text under slide'))
                        ->metadata()
                        ->placeholder(__('Enter one line of text'))
                        ->toArray()
                );

            foreach ([
                'title',
                'link',
                'description',
                'button_primary_url',
                'button_primary_label',
                'button_play_label',
                'youtube_url',
            ] as $field) {
                if ($form->has($field)) {
                    $form->remove($field);
                }
            }
        }, 99);
    }

    if (is_plugin_active('hotel')) {
        AmenityForm::extend(function (AmenityForm $form): void {
            $form
                ->addAfter(
                    'icon',
                    'icon_image',
                    MediaImageField::class,
                    MediaImageFieldOption::make()
                        ->metadata()
                        ->label(__('Icon Image (It will replace Font Icon if it is present)'))
                        ->toArray()
                )
                ->addAfter(
                    'name',
                    'description',
                    TextareaField::class,
                    DescriptionFieldOption::make()
                        ->metadata()
                        ->toArray()
                );
        }, 99);
    }

    ProfileForm::extend(function (ProfileForm $form): void {
        $form->getModel()->loadMissing('metadata');

        $form
            ->addAfter('email', 'display_name', TextField::class, TextFieldOption::make()->label(__('Display name'))->metadata()->colspan(2)->toArray())
            ->addAfter('display_name', 'bio', EditorField::class, EditorFieldOption::make()->label(__('Bio'))->metadata()->colspan(2)->toArray())
            ->addAfter('bio', 'facebook', TextField::class, TextFieldOption::make()->label(__('Facebook'))->metadata()->placeholder('https://www.facebook.com')->toArray())
            ->addAfter('facebook', 'twitter', TextField::class, TextFieldOption::make()->label(__('X (Twitter)'))->metadata()->placeholder('https://x.com')->toArray())
            ->addAfter('twitter', 'instagram', TextField::class, TextFieldOption::make()->label(__('Instagram'))->metadata()->placeholder('https://www.instagram.com')->toArray())
            ->addAfter('instagram', 'behance', TextField::class, TextFieldOption::make()->label(__('Behance'))->metadata()->placeholder('https://www.behance.net')->toArray())
            ->addAfter('behance', 'linkedin', TextField::class, TextFieldOption::make()->label(__('Linkedin'))->metadata()->placeholder('https://www.linkedin.com')->toArray());
    });
});
