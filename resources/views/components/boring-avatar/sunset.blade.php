<svg viewBox="0 0 {{ $SIZE }} {{ $SIZE }}" fill="none" role="img" xmlns="http://www.w3.org/2000/svg" {{ $attributes->merge(['width' => $size, 'height' => $size]) }}>
    @if ($title)
        <title>{{ $name }}</title>
    @endif

    <mask id="{{ $maskId }}" maskUnits="userSpaceOnUse" x="0" y="0" width="{{ $SIZE }}" height="{{ $SIZE }}">
        <rect width="{{ $SIZE }}" height="{{ $SIZE }}" rx="{{ $square ? 0 : $SIZE * 2 }}" fill="#FFFFFF" />
    </mask>

    <g mask="url(#{{ $maskId }})">
        <rect width="{{ $SIZE }}" height="{{ $SIZE }}" fill="{{ $colorsList[0] }}" />
        <path fill="url(#{{ $gradient0Id }})" d="M0 0h80v40H0z" />
        <path fill="url(#{{ $gradient1Id }})" d="M0 40h80v40H0z" fill="{{ $colorsList[1] }}" />
    </g>

    <defs>
        <linearGradient id="{{ $gradient0Id }}" x1="{{ $SIZE / 2 }}" y1="{{ $SIZE / 2 }}" x2="{{ $SIZE / 2 }}" gradientUnits="userSpaceOnUse">
            <stop stop-color="{{ $colorsList[0] }}" />
            <stop offset="1" stop-color="{{ $colorsList[1] }}" />
        </linearGradient>
        <linearGradient id="{{ $gradient1Id }}" x1="{{ $SIZE / 2 }}" y1="{{ $SIZE / 2 }}" gradientUnits="userSpaceOnUse">
            <stop stop-color="{{ $colorsList[0] }}" />
            <stop offset="1" stop-color="{{ $colorsList[1] }}" />
        </linearGradient>
    </defs>
</svg>
