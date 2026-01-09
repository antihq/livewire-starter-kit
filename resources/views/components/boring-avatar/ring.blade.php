@php
    use App\Services\BoringAvatarUtilities;

    $utilities = new BoringAvatarUtilities();

    $name ??= '';
    $colors ??= ['#E2E8F0', '#CBD5E1', '#94A3B8', '#64748B', '#475569', '#334155', '#1E293B', '#0F172A'];
    $title ??= false;
    $square ??= false;
    $size ??= 40;

    $COLORS = 5;
    $SIZE = 90;

    $numFromName = $utilities->hash($name);
    $range = count($colors);

    $colorsShuffle = [];
    for ($i = 0; $i < $COLORS; $i++) {
        $colorsShuffle[] = $utilities->randomColor($numFromName + $i, $colors, $range);
    }

    $colorsList = [
        $colorsShuffle[0],
        $colorsShuffle[1],
        $colorsShuffle[1],
        $colorsShuffle[2],
        $colorsShuffle[2],
        $colorsShuffle[3],
        $colorsShuffle[3],
        $colorsShuffle[0],
        $colorsShuffle[4],
    ];

    $maskId = 'ring-mask-' . preg_replace('/[^a-zA-Z0-9]/', '', $name) . '-' . $numFromName;
@endphp

<svg
    viewBox="0 0 {{ $SIZE }} {{ $SIZE }}"
    fill="none"
    role="img"
    xmlns="http://www.w3.org/2000/svg"
    {{ $attributes->merge(['width' => $size, 'height' => $size]) }}
>
    @if ($title)
        <title>{{ $name }}</title>
    @endif

    <mask id="{{ $maskId }}" maskUnits="userSpaceOnUse" x="0" y="0" width="{{ $SIZE }}" height="{{ $SIZE }}">
        <rect width="{{ $SIZE }}" height="{{ $SIZE }}" rx="{{ $square ? 0 : $SIZE * 2 }}" fill="#FFFFFF" />
    </mask>

    <g mask="url(#{{ $maskId }})">
        <path d="M0 0h90v45H0z" fill="{{ $colorsList[0] }}" />
        <path d="M0 45h90v45H0z" fill="{{ $colorsList[1] }}" />
        <path d="M83 45a38 38 0 00-76 0h76z" fill="{{ $colorsList[2] }}" />
        <path d="M83 45a38 38 0 01-76 0h76z" fill="{{ $colorsList[3] }}" />
        <path d="M77 45a32 32 0 10-64 0h64z" fill="{{ $colorsList[4] }}" />
        <path d="M77 45a32 32 0 11-64 0h64z" fill="{{ $colorsList[5] }}" />
        <path d="M71 45a26 26 0 00-52 0h52z" fill="{{ $colorsList[6] }}" />
        <path d="M71 45a26 26 0 01-52 0h52z" fill="{{ $colorsList[7] }}" />
        <circle cx="45" cy="45" r="23" fill="{{ $colorsList[8] }}" />
    </g>
</svg>
