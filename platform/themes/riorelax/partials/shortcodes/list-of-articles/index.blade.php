@php
    $listingId = 'adlRoomListing_' . uniqid();
    $pageParam = 'rooms_page';
    $totalPages = max((int) ceil($rooms->count() / $perPage), 1);
    $currentPage = max((int) request()->query($pageParam, 1), 1);
    $currentPage = min($currentPage, $totalPages);
    $visibleRooms = $rooms->slice(($currentPage - 1) * $perPage, $perPage);
    $pagePath = trim(request()->path(), '/');
    $apiPagePrefix = 'api/v1/pages/';

    if (Illuminate\Support\Str::startsWith($pagePath, $apiPagePrefix)) {
        $pagePath = Illuminate\Support\Str::after($pagePath, $apiPagePrefix);
    }

    $pageUrl = function (int $page) use ($pageParam, $listingId, $pagePath): string {
        $query = request()->query();

        unset($query['lang']);

        $query[$pageParam] = $page;
        $queryString = http_build_query($query);

        return '/' . ltrim($pagePath, '/') . ($queryString ? '?' . $queryString : '') . '#' . $listingId;
    };
@endphp

<section id="{{ $listingId }}" class="adl-room-listing pt-90 pb-90" style="background: #f7f3ec; overflow: hidden;">
    <div class="adl-room-listing-container" style="width: min(1680px, calc(100% - 96px)); max-width: 1680px; margin-left: auto; margin-right: auto;">
        <div class="row justify-content-center mb-45">
            <div class="col-lg-9">
                <div class="section-title center-align text-center adl-room-listing-title">
                    @if ($subtitle = $shortcode->subtitle)
                        <h5>{!! BaseHelper::clean($subtitle) !!}</h5>
                    @endif

                    <h2>{!! BaseHelper::clean($shortcode->title ?: __('Các hạng phòng')) !!}</h2>

                    @if ($description = $shortcode->description)
                        <p>{!! BaseHelper::clean($description) !!}</p>
                    @else
                        <p>{{ __('Khám phá những không gian lưu trú được thiết kế tinh tế, phù hợp cho từng nhu cầu nghỉ dưỡng.') }}</p>
                    @endif
                </div>
            </div>
        </div>

        @if ($rooms->isNotEmpty())
            <div class="adl-room-listing-stage">
                <div class="adl-room-listing-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 360px), 1fr)); gap: 24px;">
                @foreach ($visibleRooms as $room)
                    @php
                        $roomUrl = $room->url . '?start_date=' . BaseHelper::stringify(request()->query('start_date', $startDate)) . '&end_date=' . BaseHelper::stringify(request()->query('end_date', $endDate)) . '&adults=' . BaseHelper::stringify(request()->query('adults', HotelHelper::getMinimumNumberOfGuests())) . '&children=' . BaseHelper::stringify(request()->query('children', 0));
                        $image = $room->image;
                        $imageUrl = $image ? RvMedia::getImageUrl($image, 'large', false, RvMedia::getDefaultImage()) : null;
                        $imagePath = $imageUrl ? parse_url($imageUrl, PHP_URL_PATH) : null;
                        $shouldShowImage = $imageUrl && (! $imagePath || ! Illuminate\Support\Str::startsWith($imagePath, '/storage/') || file_exists(public_path(ltrim($imagePath, '/'))));
                    @endphp

                    <div class="adl-room-listing-item" style="min-width: 0;">
                        <article class="adl-room-listing-card" style="height: 100%; background: #fffdf8; border: 1px solid rgba(80, 63, 45, 0.08); border-radius: 20px; overflow: hidden; box-shadow: 0 18px 42px rgba(66, 52, 36, 0.08); display: flex; flex-direction: column;">
                            <a class="adl-room-listing-media" href="{{ $roomUrl }}" title="{{ $room->name }}" style="position: relative; display: block; aspect-ratio: 1.35 / 1; overflow: hidden; background: linear-gradient(135deg, #d9d0c1 0%, #eee8dd 100%);">
                                <span class="adl-room-listing-no-image" style="position: absolute; inset: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; padding: 28px; color: rgba(47, 38, 31, 0.45); font-size: 18px; line-height: 1.35; text-align: center; background: linear-gradient(135deg, #d9d0c1 0%, #eee8dd 100%);">
                                    {{ $room->name }}
                                </span>

                                @if ($shouldShowImage)
                                    <img src="{{ $imageUrl }}" alt="{{ $room->name }}" loading="lazy" style="position: relative; z-index: 1; width: 100%; height: 100%; object-fit: cover; display: block;">
                                @endif

                                @if ($room->category->name)
                                    <span class="adl-room-listing-category" style="position: absolute; z-index: 2; left: 18px; bottom: 18px; max-width: calc(100% - 36px); padding: 7px 12px; color: #fff; background: rgba(47, 38, 31, 0.78); border-radius: 999px; font-size: 13px; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $room->category->name }}</span>
                                @endif
                            </a>

                            <div class="adl-room-listing-content" style="padding: 24px 24px 26px; display: flex; flex: 1; flex-direction: column; gap: 14px;">
                                <div class="adl-room-listing-meta" style="display: flex; flex-wrap: wrap; gap: 10px 16px; color: #8a704f; font-size: 14px; line-height: 1.4;">
                                    @if ($room->size)
                                        <span><i class="fas fa-ruler-combined"></i>{{ $room->size }}m²</span>
                                    @endif

                                    @if ($room->number_of_beds)
                                        <span><i class="fas fa-bed"></i>{{ trans_choice(':count giường|:count giường', $room->number_of_beds, ['count' => $room->number_of_beds]) }}</span>
                                    @endif
                                </div>

                                <h3 style="margin: 0; color: #2f261f; font-size: 24px; line-height: 1.25; font-weight: 500; letter-spacing: 0;"><a href="{{ $roomUrl }}">{{ $room->name }}</a></h3>

                                @if ($description = $room->description)
                                    <p style="color: #706761; font-size: 15px; line-height: 1.7; margin: 0;">{!! BaseHelper::clean($description) !!}</p>
                                @endif

                                <div class="adl-room-listing-footer" style="margin-top: auto; padding-top: 4px; display: flex; align-items: center; justify-content: space-between; gap: 14px;">
                                    <div class="adl-room-listing-price">
                                        <span>{{ __('Từ') }}</span>
                                        <strong>{{ format_price($room->price) }}</strong>
                                    </div>
                                    <a class="adl-room-listing-link" href="{{ $roomUrl }}" style="flex: 0 0 auto; color: #fff; background: #73794f; border-radius: 999px; padding: 10px 16px; font-size: 14px; font-weight: 700; line-height: 1; text-decoration: none;">{{ __('Xem phòng') }}</a>
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
                </div>

                @if ($currentPage < $totalPages)
                    <a class="adl-room-listing-next" href="{{ $pageUrl($currentPage + 1) }}" aria-label="{{ __('Next rooms') }}">
                        <i class="fal fa-chevron-right"></i>
                    </a>
                @else
                    <span class="adl-room-listing-next is-disabled" aria-hidden="true">
                        <i class="fal fa-chevron-right"></i>
                    </span>
                @endif
            </div>

            <div class="adl-room-listing-pagination" aria-label="{{ __('Rooms pagination') }}" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 10px; margin-top: 34px; line-height: 1;">
                @for ($page = 1; $page <= $totalPages; $page++)
                    <a
                        @class(['adl-room-listing-page', 'is-active' => $page === $currentPage])
                        href="{{ $pageUrl($page) }}"
                        aria-label="{{ __('Go to rooms page :page', ['page' => $page]) }}"
                        @if ($page === $currentPage) aria-current="page" @endif
                        style="width: 38px; height: 38px; border: 1px solid {{ $page === $currentPage ? '#73794f' : 'rgba(80, 63, 45, 0.16)' }}; border-radius: 50%; color: {{ $page === $currentPage ? '#fff' : '#73794f' }}; background: {{ $page === $currentPage ? '#73794f' : '#fffdf8' }}; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; line-height: 1; text-decoration: none; box-shadow: {{ $page === $currentPage ? '0 10px 24px rgba(66, 52, 36, 0.18)' : '0 6px 16px rgba(66, 52, 36, 0.08)' }};"
                    >
                        {{ $page }}
                    </a>
                @endfor
            </div>
        @endif
    </div>
</section>

<style>
    .adl-room-listing {
        background: #f7f3ec;
        overflow: hidden;
    }

    .adl-room-listing-container {
        position: relative;
        width: min(1680px, calc(100% - 96px));
        max-width: 1680px;
        margin-left: auto;
        margin-right: auto;
    }

    .adl-room-listing-title {
        text-align: center;
        margin-bottom: 0;
    }

    .adl-room-listing-title h2 {
        color: #2f261f;
        letter-spacing: 0;
    }

    .adl-room-listing-title p {
        max-width: 720px;
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 0;
    }

    .adl-room-listing-stage {
        position: relative;
    }

    .adl-room-listing-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 360px), 1fr));
        gap: 24px;
    }

    .adl-room-listing-item {
        min-width: 0;
    }

    .adl-room-listing-card {
        height: 100%;
        background: #fffdf8;
        border: 1px solid rgba(80, 63, 45, 0.08);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 18px 42px rgba(66, 52, 36, 0.08);
        display: flex;
        flex-direction: column;
    }

    .adl-room-listing-media {
        position: relative;
        display: block;
        aspect-ratio: 1.35 / 1;
        overflow: hidden;
        background: #e7dfd3;
    }

    .adl-room-listing-media img,
    .adl-room-listing-no-image {
        width: 100%;
        height: 100%;
        display: block;
    }

    .adl-room-listing-media img {
        object-fit: cover;
        transition: transform 0.45s ease;
    }

    .adl-room-listing-card:hover .adl-room-listing-media img {
        transform: scale(1.06);
    }

    .adl-room-listing-no-image {
        background: linear-gradient(135deg, #eee7dc 0%, #d8caba 100%);
    }

    .adl-room-listing-category {
        position: absolute;
        left: 18px;
        bottom: 18px;
        max-width: calc(100% - 36px);
        padding: 7px 12px;
        color: #fff;
        background: rgba(47, 38, 31, 0.78);
        border-radius: 999px;
        font-size: 13px;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .adl-room-listing-content {
        padding: 24px 24px 26px;
        display: flex;
        flex: 1;
        flex-direction: column;
        gap: 14px;
    }

    .adl-room-listing-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px 16px;
        color: #8a704f;
        font-size: 14px;
        line-height: 1.4;
    }

    .adl-room-listing-meta span {
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .adl-room-listing-content h3 {
        margin: 0;
        color: #2f261f;
        font-size: 24px;
        line-height: 1.25;
        font-weight: 500;
        letter-spacing: 0;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .adl-room-listing-content h3 a {
        color: inherit;
        text-decoration: none;
    }

    .adl-room-listing-content p {
        color: #706761;
        font-size: 15px;
        line-height: 1.7;
        margin: 0;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
    }

    .adl-room-listing-footer {
        margin-top: auto;
        padding-top: 4px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }

    .adl-room-listing-price span {
        display: block;
        color: #8c8278;
        font-size: 12px;
        line-height: 1.2;
    }

    .adl-room-listing-price strong {
        display: block;
        color: #73794f;
        font-size: 18px;
        line-height: 1.3;
    }

    .adl-room-listing-link {
        flex: 0 0 auto;
        color: #fff;
        background: #73794f;
        border-radius: 999px;
        padding: 10px 16px;
        font-size: 14px;
        font-weight: 700;
        line-height: 1;
        text-decoration: none;
        transition: background 0.25s ease, transform 0.25s ease;
    }

    .adl-room-listing-link:hover {
        color: #fff;
        background: #916d45;
        transform: translateY(-2px);
    }

    .adl-room-listing-next {
        position: absolute;
        top: 50%;
        right: -26px;
        width: 52px;
        height: 52px;
        color: #73794f;
        background: #fffdf8;
        border: 0;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 14px 32px rgba(66, 52, 36, 0.18);
        cursor: pointer;
        text-decoration: none;
        transform: translateY(-50%);
        transition: background 0.25s ease, color 0.25s ease, opacity 0.25s ease;
        z-index: 4;
    }

    .adl-room-listing-next:hover {
        color: #fff;
        background: #73794f;
    }

    .adl-room-listing-next.is-disabled {
        opacity: 0.35;
        pointer-events: none;
    }

    .adl-room-listing-pagination {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 28px;
    }

    .adl-room-listing-page {
        width: 36px;
        height: 36px;
        border: 1px solid rgba(80, 63, 45, 0.16);
        border-radius: 50%;
        color: #73794f;
        background: #fffdf8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 700;
        line-height: 1;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.25s ease, color 0.25s ease, border-color 0.25s ease;
    }

    .adl-room-listing-page.is-active,
    .adl-room-listing-page:hover {
        color: #fff;
        background: #73794f;
        border-color: #73794f;
    }

    @media (max-width: 1199px) {
        .adl-room-listing-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 991px) {
        .adl-room-listing-container {
            width: min(100% - 48px, 1680px);
        }

        .adl-room-listing-title {
            text-align: center;
            margin-bottom: 24px;
        }

        .adl-room-listing-title p {
            margin-left: auto;
            margin-right: auto;
        }

        .adl-room-listing-next {
            right: -12px;
        }
    }

    @media (max-width: 767px) {
        .adl-room-listing-container {
            width: min(100% - 32px, 1680px);
        }

        .adl-room-listing-next {
            top: auto;
            right: 0;
            bottom: -68px;
            transform: none;
        }

        .adl-room-listing-pagination {
            padding-right: 64px;
        }

        .adl-room-listing-content {
            padding: 22px;
        }

        .adl-room-listing-content h3 {
            font-size: 22px;
        }
    }
</style>
