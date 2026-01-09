@php
    $numFromName = $component->hash($name);
    $range = count($colors);
    $SIZE = 36;

    $wrapperColor = $component->randomColor((int) $numFromName, $colors, $range);

    $preTranslateX = $component->unit($numFromName, 10, 1);
    $wrapperTranslateX = $preTranslateX < 5 ? $preTranslateX + $SIZE / 9 : $preTranslateX;

    $preTranslateY = $component->unit($numFromName, 10, 2);
    $wrapperTranslateY = $preTranslateY < 5 ? $preTranslateY + $SIZE / 9 : $preTranslateY;

    $data = [
        'wrapperColor' => $wrapperColor,
        'faceColor' => $component->contrast($wrapperColor),
        'backgroundColor' => $component->randomColor((int) ($numFromName + 13), $colors, $range),
        'wrapperTranslateX' => $wrapperTranslateX,
        'wrapperTranslateY' => $wrapperTranslateY,
        'wrapperRotate' => $component->unit($numFromName, 360),
        'wrapperScale' => 1 + $component->unit($numFromName, $SIZE / 12) / 10,
        'isMouthOpen' => $component->boolean($numFromName, 2),
        'isCircle' => $component->boolean($numFromName, 1),
        'eyeSpread' => $component->unit($numFromName, 5),
        'mouthSpread' => $component->unit($numFromName, 3),
        'faceRotate' => $component->unit($numFromName, 10, 3),
        'faceTranslateX' => $wrapperTranslateX > $SIZE / 6 ? $wrapperTranslateX / 2 : $component->unit($numFromName, 8, 1),
        'faceTranslateY' => $wrapperTranslateY > $SIZE / 6 ? $wrapperTranslateY / 2 : $component->unit($numFromName, 7, 2),
    ];

    $mouthPath = $data['isMouthOpen']
        ? 'M15 ' . (19 + $data['mouthSpread']) . 'c2 1 4 1 6 0'
        : 'M13,' . (19 + $data['mouthSpread']) . ' a1,0.75 0 0,0 10,0';

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
        <rect width="{{ $SIZE }}" height="{{ $SIZE }}" fill="{{ $data['backgroundColor'] }}" />

        <rect
            x="0"
            y="0"
            width="{{ $SIZE }}"
            height="{{ $SIZE }}"
            transform="translate({{ $data['wrapperTranslateX'] }} {{ $data['wrapperTranslateY'] }}) rotate({{ $data['wrapperRotate'] }} {{ $SIZE / 2 }})"
            fill="{{ $data['wrapperColor'] }}"
            rx="{{ $data['isCircle'] ? $SIZE : $SIZE / 6 }}" />

        <g transform="translate({{ $data['faceTranslateX'] }} {{ $data['faceTranslateY'] }}) rotate({{ $data['faceRotate'] }} {{ $SIZE / 2 }})">
            @if ($data['isMouthOpen'])
                <path
                    d="{{ $mouthPath }}"
                    stroke="{{ $data['faceColor'] }}"
                    fill="none"
                    stroke-linecap="round" />
            @else
                <path d="{{ $mouthPath }}" fill="{{ $data['faceColor'] }}" />
            @endif

            <rect
                x="{{ 14 - $data['eyeSpread'] }}"
                y="14"
                width="1.5"
                height="2"
                rx="1"
                stroke="none"
                fill="{{ $data['faceColor'] }}" />

            <rect
                x="{{ 20 + $data['eyeSpread'] }}"
                y="14"
                width="1.5"
                height="2"
                rx="1"
                stroke="none"
                fill="{{ $data['faceColor'] }}" />
        </g>
    </g>
</svg>
