@php
    $variant ??= 'beam';
    $name ??= '';
    $colors ??= null;
    $title ??= false;
    $square ??= false;
    $size ??= 40;

    $supportedVariants = ['bauhaus', 'beam', 'marble', 'pixel', 'ring', 'sunset'];

    if (! in_array($variant, $supportedVariants, true)) {
        $variant = 'beam';
    }
@endphp

@include(
    'components.boring-avatar.' . $variant,
    [
        'name' => $name,
        'colors' => $colors,
        'title' => $title,
        'square' => $square,
        'size' => $size,
        'attributes' => $attributes,
    ]
)
