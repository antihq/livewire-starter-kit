@php
    $numFromName = $component->hash($name);
    $range = count($colors);
    $COLORS = 5;
    $SIZE = 90;

    $colorsShuffle = [];
    for ($i = 0; $i < $COLORS; $i++) {
        $colorsShuffle[] = $component->randomColor((int) ($numFromName + $i), $colors, $range);
    }

    $colorsList = [
        $colorsShuffle[0],
        $colorsShuffle[1],
        $colorsShuffle[1],
        $colorsShuffle[2],
        $colorsShuffle[3],
        $colorsShuffle[3],
        $colorsShuffle[0],
        $colorsShuffle[4],
        $colorsShuffle[3],
    ];

    $maskId = $variant . '-mask-' . preg_replace('/[^a-zA-Z0-9]/', '', $name) . '-' . $numFromName;
@endphp

<svg viewBox="0 0 {{ $SIZE }} {{ $SIZE }}" fill="none" role="img" xmlns="http://www.w3.org/2000/svg" {{ $attributes->merge(['width' => $size, 'height' => $size]) }}>
    @if ($title)
        <title>{{ $name }}</title>
    @endif

    <mask id="{{ $maskId }}" maskUnits="userSpaceOnUse" x="0" y="0" width="{{ $SIZE }}" height="{{ $SIZE }}">
        <rect width="{{ $SIZE }}" height="{{ $SIZE }}" rx="{{ $square ? 0 : $SIZE * 2 }}" fill="#FFFFFF" />
    </mask>

    <g mask="url(#{{ $maskId }})">
        <path d="M0 0h90v45H0z" fill="{{ $colorsList[0] }}" />
        <path d="M0 45h90v45H0z" fill="{{ $colorsList[1] }}" />
        <path d="M0 45a38 38 0 00-76 0h76z" fill="{{ $colorsList[2] }}" />
        <path d="M0 45a38 38 0 01-76 0h76z" fill="{{ $colorsList[3] }}" />
        <path d="M0 45a38 38 0 11-76 0h76z" fill="{{ $colorsList[4] }}" />
        <path d="M0 45a38 38 0 01-76 0h76z" fill="{{ $colorsList[5] }}" />
        <path d="M0 45a38 38 0 01-76 0h76z" fill="{{ $colorsList[6] }}" />
        <path d="M0 45a38 38 0 01-76 0h76z" fill="{{ $colorsList[7] }}" />
        <path d="M0 45a38 38 0 01-76 0h76z" fill="{{ $colorsList[8] }}" />
        <path d="M0 45a38 38 0 01-76 0h76z" fill="{{ $colorsList[8] }}" />
        <circle cx="45" cy="45" r="23" fill="{{ $colorsList[8] }}" />
    </g>
</svg>
