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

    $numFromName = $utilities->hashCode($name);
    $range = count($colors);

    $elementsProperties = [];
    for ($i = 0; $i < $ELEMENTS; $i++) {
        $elementsProperties[] = [
            'color' => $utilities->getRandomColor($numFromName + $i, $colors, $range),
            'translateX' => $utilities->getUnit($numFromName * ($i + 1), $SIZE / 2 - ($i + 17), 1),
            'translateY' => $utilities->getUnit($numFromName * ($i + 1), $SIZE / 2 - ($i + 17), 2),
            'rotate' => $utilities->getUnit($numFromName * ($i + 1), 360),
            'isSquare' => $utilities->getBoolean($numFromName, 2),
        ];
    }

    $maskId = 'bauhaus-mask-' . preg_replace('/[^a-zA-Z0-9]/', '', $name) . '-' . $numFromName;
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
        <rect width="{{ $SIZE }}" height="{{ $SIZE }}" fill="{{ $elementsProperties[0]['color'] }}" />

        <rect
            x="{{ ($SIZE - 60) / 2 }}"
            y="{{ ($SIZE - 20) / 2 }}"
            width="{{ $SIZE }}"
            height="{{ $elementsProperties[1]['isSquare'] ? $SIZE : $SIZE / 8 }}"
            fill="{{ $elementsProperties[1]['color'] }}"
            transform="translate({{ $elementsProperties[1]['translateX'] }} {{ $elementsProperties[1]['translateY'] }}) rotate({{ $elementsProperties[1]['rotate'] }} {{ $SIZE / 2 }} {{ $SIZE / 2 }})"
        />

        <circle
            cx="{{ $SIZE / 2 }}"
            cy="{{ $SIZE / 2 }}"
            fill="{{ $elementsProperties[2]['color'] }}"
            r="{{ $SIZE / 5 }}"
            transform="translate({{ $elementsProperties[2]['translateX'] }} {{ $elementsProperties[2]['translateY'] }})"
        />

        <line
            x1="0"
            y1="{{ $SIZE / 2 }}"
            x2="{{ $SIZE }}"
            y2="{{ $SIZE / 2 }}"
            stroke-width="2"
            stroke="{{ $elementsProperties[3]['color'] }}"
            transform="translate({{ $elementsProperties[3]['translateX'] }} {{ $elementsProperties[3]['translateY'] }}) rotate({{ $elementsProperties[3]['rotate'] }} {{ $SIZE / 2 }} {{ $SIZE / 2 }})"
        />
    </g>
</svg>
