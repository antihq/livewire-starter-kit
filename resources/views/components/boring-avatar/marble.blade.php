<svg viewBox="0 0 {{ $SIZE }} {{ $SIZE }}" fill="none" role="img" xmlns="http://www.w3.org/2000/svg" {{ $attributes->merge(['width' => $size, 'height' => $size]) }}>
    @if ($title)
        <title>{{ $name }}</title>
    @endif

    <mask id="{{ $maskId }}" maskUnits="userSpaceOnUse" x="0" y="0" width="{{ $SIZE }}" height="{{ $SIZE }}">
        <rect width="{{ $SIZE }}" height="{{ $SIZE }}" rx="{{ $square ? 0 : $SIZE * 2 }}" fill="#FFFFFF" />
    </mask>

    <g mask="url(#{{ $maskId }})">
        <rect width="{{ $SIZE }}" height="{{ $SIZE }}" fill="{{ $elementsProperties[0]['color'] }}" />

        <path
            filter="url(#{{ $filterId }})"
            d="M32.414 59.35L50.376 70.5H72.5v-71H33.28L26.5 13.38l19.057 27.08L32.414 59.35z"
            fill="{{ $elementsProperties[1]['color'] }}"
            transform="translate({{ $elementsProperties[1]['translateX'] }} {{ $elementsProperties[1]['translateY'] }}) rotate({{ $elementsProperties[1]['rotate'] }} {{ $SIZE / 2 }})"
            rx="{{ $square ? 0 : $SIZE * 2 }}" />

        <path
            filter="url(#{{ $filterId }})"
            style="mix-blend-mode: overlay"
            d="M22.216 24L0 46.75l14.108 38.129L78.86l-3.081 59.27.08L32.414 59.35 27.395 0 12.972 20.186-23.35 27.395L22.215 24z"
            fill="{{ $elementsProperties[2]['color'] }}"
            transform="translate({{ $elementsProperties[2]['translateX'] }} {{ $elementsProperties[2]['translateY'] }}) rotate({{ $elementsProperties[2]['rotate'] }} {{ $SIZE / 2 }})"
            rx="{{ $square ? 0 : $SIZE * 2 }}" />
    </g>

    <defs>
        <filter id="{{ $filterId }}" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
            <feFlood flood-opacity="0" result="BackgroundImageFix" />
            <feBlend in="SourceGraphic" in2="BackgroundImageFix" result="shape" />
            <feGaussianBlur stdDeviation="7" result="effect1_foregroundBlur" />
        </filter>
    </defs>
</svg>
