@php
    $numFromName = $component->hash($name);
    $range = count($colors);
    $ELEMENTS = 64;
    $SIZE = 80;

    $pixelColors = [];
    for ($i = 0; $i < $ELEMENTS; $i++) {
        $pixelColors[] = $component->randomColor((int) ($numFromName % ($i + 1)), $colors, $range);
    }

    $positions = [
        [0, 0], [10, 0], [20, 0], [30, 0], [40, 0], [50, 0], [60, 0], [70, 0],
        [0, 10], [10, 10], [20, 10], [30, 10], [40, 10], [50, 10], [60, 10], [70, 10],
        [0, 20], [10, 20], [20, 20], [30, 20], [40, 20], [50, 20], [60, 20], [70, 20],
        [0, 30], [10, 30], [20, 30], [30, 30], [40, 30], [50, 30], [60, 30], [70, 30],
        [0, 40], [10, 40], [20, 40], [30, 40], [40, 40], [50, 40], [60, 40], [70, 40],
        [0, 50], [10, 50], [20, 50], [30, 50], [40, 50], [50, 50], [60, 50], [70, 50],
        [0, 60], [10, 60], [20, 60], [30, 60], [40, 60], [50, 60], [60, 60], [70, 60],
        [0, 70], [10, 70], [20, 70], [30, 70], [40, 70], [50, 70], [60, 70], [70, 70],
    ];

    $maskId = $variant . '-mask-' . preg_replace('/[^a-zA-Z0-9]/', '', $name) . '-' . $numFromName;
@endphp

<svg viewBox="0 0 {{ $SIZE }} {{ $SIZE }}" fill="none" role="img" xmlns="http://www.w3.org/2000/svg" {{ $attributes->merge(['width' => $size, 'height' => $size]) }}>
    @if ($title)
        <title>{{ $name }}</title>
    @endif

    <mask id="{{ $maskId }}" mask-type="alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="{{ $SIZE }}" height="{{ $SIZE }}">
        <rect width="{{ $SIZE }}" height="{{ $SIZE }}" rx="{{ $square ? 0 : $SIZE * 2 }}" fill="#FFFFFF" />
    </mask>

    <g mask="url(#{{ $maskId }})">
        @foreach ($positions as $index => [$x, $y])
            <rect x="{{ $x }}" y="{{ $y }}" width="10" height="10" fill="{{ $pixelColors[$index] }}" />
        @endforeach
    </g>
</svg>
