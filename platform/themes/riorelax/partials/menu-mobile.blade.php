@php
    $isChild = (bool) ($is_child ?? false);
    $isColumn = (bool) ($is_column ?? false);
@endphp

@if (! $isChild && ! $isColumn)
    @php
        $nodes = $menu_nodes instanceof \Illuminate\Support\Collection ? $menu_nodes->values() : collect($menu_nodes)->values();
        $midpoint = (int) ceil($nodes->count() / 2);
        $columns = [
            $nodes->slice(0, $midpoint)->values(),
            $nodes->slice($midpoint)->values(),
        ];
    @endphp

    <div class="riorelax-mobile-menu-grid">
        @foreach ($columns as $columnNodes)
            <div class="riorelax-mobile-menu-grid__column">
                {!! Theme::partial('menu-mobile', [
                    'menu_nodes' => $columnNodes,
                    'is_column' => true,
                ]) !!}
            </div>
        @endforeach
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
