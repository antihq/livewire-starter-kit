@php
    use App\Services\BoringAvatarUtilities;

    $utilities = new BoringAvatarUtilities();

    $name ??= '';
    $colors ??= ['#E2E8F0', '#CBD5E1', '#94A3B8', '#64748B', '#475569', '#334155', '#1E293B', '#0F172A'];
    $title ??= false;
    $square ??= false;
    $size ??= 40;

    $SIZE = 36;

    $numFromName = $utilities->hash($name);
    $range = count($colors);

    $wrapperColor = $utilities->randomColor($numFromName, $colors, $range);

    $preTranslateX = $utilities->unit($numFromName, 10, 1);
    $wrapperTranslateX = $preTranslateX < 5 ? $preTranslateX + $SIZE / 9 : $preTranslateX;

    $preTranslateY = $utilities->unit($numFromName, 10, 2);
    $wrapperTranslateY = $preTranslateY < 5 ? $preTranslateY + $SIZE / 9 : $preTranslateY;

    $data = [
        'wrapperColor' => $wrapperColor,
        'faceColor' => $utilities->contrast($wrapperColor),
        'backgroundColor' => $utilities->randomColor($numFromName + 13, $colors, $range),
        'wrapperTranslateX' => $wrapperTranslateX,
        'wrapperTranslateY' => $wrapperTranslateY,
        'wrapperRotate' => $utilities->unit($numFromName, 360),
        'wrapperScale' => 1 + $utilities->unit($numFromName, $SIZE / 12) / 10,
        'isMouthOpen' => $utilities->boolean($numFromName, 2),
        'isCircle' => $utilities->boolean($numFromName, 1),
        'eyeSpread' => $utilities->unit($numFromName, 5),
        'mouthSpread' => $utilities->unit($numFromName, 3),
        'faceRotate' => $utilities->unit($numFromName, 10, 3),
        'faceTranslateX' => $wrapperTranslateX > $SIZE / 6 ? $wrapperTranslateX / 2 : $utilities->unit($numFromName, 8, 1),
        'faceTranslateY' => $wrapperTranslateY > $SIZE / 6 ? $wrapperTranslateY / 2 : $utilities->unit($numFromName, 7, 2),
    ];

    $maskId = 'beam-mask-' . preg_replace('/[^a-zA-Z0-9]/', '', $name) . '-' . $numFromName;
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
        <rect width="{{ $SIZE }}" height="{{ $SIZE }}" fill="{{ $data['backgroundColor'] }}" />

        <rect
            x="0"
            y="0"
            width="{{ $SIZE }}"
            height="{{ $SIZE }}"
            transform="translate({{ $data['wrapperTranslateX'] }} {{ $data['wrapperTranslateY'] }}) rotate({{ $data['wrapperRotate'] }} {{ $SIZE / 2 }} {{ $SIZE / 2 }}) scale({{ $data['wrapperScale'] }})"
            fill="{{ $data['wrapperColor'] }}"
            rx="{{ $data['isCircle'] ? $SIZE : $SIZE / 6 }}"
        />

        <g
            transform="translate({{ $data['faceTranslateX'] }} {{ $data['faceTranslateY'] }}) rotate({{ $data['faceRotate'] }} {{ $SIZE / 2 }} {{ $SIZE / 2 }})"
        >
            @if ($data['isMouthOpen'])
                <path
                    d="M15 {{ 19 + $data['mouthSpread'] }}c2 1 4 1 6 0"
                    stroke="{{ $data['faceColor'] }}"
                    fill="none"
                    stroke-linecap="round"
                />
            @else
                <path d="M13,{{ 19 + $data['mouthSpread'] }} a1,0.75 0 0,0 10,0" fill="{{ $data['faceColor'] }}" />
            @endif

            <rect
                x="{{ 14 - $data['eyeSpread'] }}"
                y="14"
                width="1.5"
                height="2"
                rx="1"
                stroke="none"
                fill="{{ $data['faceColor'] }}"
            />
            <rect
                x="{{ 20 + $data['eyeSpread'] }}"
                y="14"
                width="1.5"
                height="2"
                rx="1"
                stroke="none"
                fill="{{ $data['faceColor'] }}"
            />
        </g>
    </g>
</svg>
