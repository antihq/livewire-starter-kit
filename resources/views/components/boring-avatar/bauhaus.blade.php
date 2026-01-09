<svg viewBox="0 0 {{ $SIZE }} {{ $SIZE }}" fill="none" role="img" xmlns="http://www.w3.org/2000/svg" {{ $attributes->merge(['width' => $size, 'height' => $size]) }}>
    @if ($title)
        <title>{{ $name }}</title>
    @endif

    <mask id="{{ $maskId }}" maskUnits="userSpaceOnUse" x="0" y="0" width="{{ $SIZE }}" height="{{ $SIZE }}">
        <rect width="{{ $SIZE }}" height="{{ $SIZE }}" rx="{{ $square ? 0 : $SIZE * 2 }}" fill="#FFFFFF" />
    </mask>

    <g mask="url(#{{ $maskId }})">
        <rect width="{{ $SIZE }}" height="{{ $SIZE }}" fill="{{ $elementsProperties[0]['color'] }}" />

        <rect
            x="{{ ($SIZE - 60) / 2 }}"
            y="{{ ($SIZE - 20) / 2 }}"
            width="{{ $SIZE }}"
            height="{{ $elementsProperties[1]['isSquare'] ? $SIZE : $SIZE / 8 }}"
            fill="{{ $elementsProperties[1]['color'] }}"
            transform="translate({{ $elementsProperties[1]['translateX'] }} {{ $elementsProperties[1]['translateY'] }}) rotate({{ $elementsProperties[1]['rotate'] }} {{ $SIZE / 2 }} {{ $SIZE / 2 }})" />

        <circle
            cx="{{ $SIZE / 2 }}"
            cy="{{ $SIZE / 2 }}"
            fill="{{ $elementsProperties[2]['color'] }}"
            r="{{ $SIZE / 5 }}"
            transform="translate({{ $elementsProperties[2]['translateX'] }} {{ $elementsProperties[2]['translateY'] }})" />

        <line
            x1="0"
            y1="{{ $SIZE / 2 }}"
            x2="{{ $SIZE }}"
            y2="{{ $SIZE / 2 }}"
            stroke-width="2"
            stroke="{{ $elementsProperties[3]['color'] }}"
            transform="translate({{ $elementsProperties[3]['translateX'] }} {{ $elementsProperties[3]['translateY'] }}) rotate({{ $elementsProperties[3]['rotate'] }} {{ $SIZE / 2 }} {{ $SIZE / 2 }})" />
    </g>
</svg>
