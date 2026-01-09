@php
    use App\Services\BoringAvatarUtilities;

    $utilities = new BoringAvatarUtilities();

    $name ??= '';
    $colors ??= ['#E2E8F0', '#CBD5E1', '#94A3B8', '#64748B', '#475569', '#334155', '#1E293B', '#0F172A'];
    $title ??= false;
    $square ??= false;
    $size ??= 40;

    $ELEMENTS = 4;
    $SIZE = 80;

    $numFromName = $utilities->hash($name);
    $range = count($colors);

    $colorsList = [];
    for ($i = 0; $i < $ELEMENTS; $i++) {
        $colorsList[] = $utilities->randomColor($numFromName + $i, $colors, $range);
    }

    $nameWithoutSpace = preg_replace('/\s/', '', $name);

    $maskId = 'sunset-mask-' . $nameWithoutSpace . '-' . $numFromName;
    $gradient0Id = 'gradient-paint0-linear-' . $nameWithoutSpace;
    $gradient1Id = 'gradient-paint1-linear-' . $nameWithoutSpace;
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
        <path fill="url(#{{ $gradient0Id }})" d="M0 0h80v40H0z" />
        <path fill="url(#{{ $gradient1Id }})" d="M0 40h80v40H0z" />
    </g>

    <defs>
        <linearGradient
            id="{{ $gradient0Id }}"
            x1="{{ $SIZE / 2 }}"
            y1="0"
            x2="{{ $SIZE / 2 }}"
            y2="{{ $SIZE / 2 }}"
            gradientUnits="userSpaceOnUse"
        >
            <stop stop-color="{{ $colorsList[0] }}" />
            <stop offset="1" stop-color="{{ $colorsList[1] }}" />
        </linearGradient>
        <linearGradient
            id="{{ $gradient1Id }}"
            x1="{{ $SIZE / 2 }}"
            y1="{{ $SIZE / 2 }}"
            x2="{{ $SIZE / 2 }}"
            y2="{{ $SIZE }}"
            gradientUnits="userSpaceOnUse"
        >
            <stop stop-color="{{ $colorsList[2] }}" />
            <stop offset="1" stop-color="{{ $colorsList[3] }}" />
        </linearGradient>
    </defs>
</svg>
