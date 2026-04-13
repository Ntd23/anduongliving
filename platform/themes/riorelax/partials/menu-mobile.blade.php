@php
    $isChild = (bool) ($is_child ?? false);
    $isColumn = (bool) ($is_column ?? false);
@endphp

@if (! $isChild && ! $isColumn)
    @php
        $nodes = $menu_nodes instanceof \Illuminate\Support\Collection ? $menu_nodes->values() : collect($menu_nodes)->values();
        $midpoint = (int) ceil($nodes->count() / 2);
        $leftColumnNodes = $nodes->slice(0, $midpoint)->values();
        $rightColumnNodes = $nodes->slice($midpoint)->values();
        $rowCount = max($leftColumnNodes->count(), $rightColumnNodes->count());
    @endphp

    <div class="riorelax-mobile-menu-rows">
        @for ($index = 0; $index < $rowCount; $index++)
            <div class="riorelax-mobile-menu-rows__row">
                <div class="riorelax-mobile-menu-rows__cell">
                    @if ($leftNode = $leftColumnNodes->get($index))
                        {!! Theme::partial('menu-mobile', [
                            'menu_nodes' => collect([$leftNode]),
                            'is_column' => true,
                        ]) !!}
                    @endif
                </div>

                <div class="riorelax-mobile-menu-rows__cell">
                    @if ($rightNode = $rightColumnNodes->get($index))
                        {!! Theme::partial('menu-mobile', [
                            'menu_nodes' => collect([$rightNode]),
                            'is_column' => true,
                        ]) !!}
                    @endif
                </div>
            </div>
        @endfor
    </div>
@else
    <ul class="riorelax-mobile-menu-list{{ $isChild ? ' riorelax-mobile-menu-list--child' : '' }}">
        @foreach ($menu_nodes as $row)
            <li class="riorelax-mobile-menu-list__item{{ $row->active ? ' is-active' : '' }}{{ $row->has_child ? ' has-sub' : '' }}">
                <a
                    href="{{ $row->url }}"
                    class="riorelax-mobile-menu-link{{ $isChild ? ' riorelax-mobile-menu-link--child' : '' }}{{ $row->active ? ' is-active' : '' }}"
                    @if ($row->target) target="{{ $row->target }}" @endif
                >
                    <span class="riorelax-mobile-menu-link__arrow" aria-hidden="true"></span>
                    <span class="riorelax-mobile-menu-link__text">{{ $row->title }}</span>
                </a>

                @if ($row->has_child)
                    {!! Theme::partial('menu-mobile', [
                        'menu_nodes' => $row->child,
                        'is_child' => true,
                    ]) !!}
                @endif
            </li>
        @endforeach
    </ul>
@endif
