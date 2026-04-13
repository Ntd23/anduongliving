<ul {!! BaseHelper::clean($options) !!}>
    @foreach($menu_nodes->loadMissing('metadata') as $item)
        <li>
            <a @if ($item->target) target="{{ $item->target }}" @endif href="{{ $item->url }}">{{ $item->title }}</a>
        </li>
    @endforeach
</ul>
