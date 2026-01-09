<?php

it('beam avatar renders for valid name', function () {
    $html = view('components.boring-avatar.beam', [
        'name' => 'John Doe',
        'size' => 40,
        'attributes' => new \Illuminate\View\ComponentAttributeBag,
    ])->render();

    expect($html)->toContain('<svg')
        ->and($html)->toContain('viewBox="0 0 36 36"')
        ->and($html)->toContain('role="img"');
});

it('bauhaus avatar renders for valid name', function () {
    $html = view('components.boring-avatar.bauhaus', [
        'name' => 'Jane Smith',
        'size' => 40,
        'attributes' => new \Illuminate\View\ComponentAttributeBag,
    ])->render();

    expect($html)->toContain('<svg')
        ->and($html)->toContain('viewBox="0 0 80 80"')
        ->and($html)->toContain('role="img"');
});

it('marble avatar renders for valid name', function () {
    $html = view('components.boring-avatar.marble', [
        'name' => 'Alice',
        'size' => 40,
        'attributes' => new \Illuminate\View\ComponentAttributeBag,
    ])->render();

    expect($html)->toContain('<svg')
        ->and($html)->toContain('viewBox="0 0 80 80"')
        ->and($html)->toContain('role="img"');
});

it('pixel avatar renders for valid name', function () {
    $html = view('components.boring-avatar.pixel', [
        'name' => 'Bob',
        'size' => 40,
        'attributes' => new \Illuminate\View\ComponentAttributeBag,
    ])->render();

    expect($html)->toContain('<svg')
        ->and($html)->toContain('viewBox="0 0 80 80"')
        ->and($html)->toContain('role="img"');
});

it('ring avatar renders for valid name', function () {
    $html = view('components.boring-avatar.ring', [
        'name' => 'Charlie',
        'size' => 40,
        'attributes' => new \Illuminate\View\ComponentAttributeBag,
    ])->render();

    expect($html)->toContain('<svg')
        ->and($html)->toContain('viewBox="0 0 90 90"')
        ->and($html)->toContain('role="img"');
});

it('sunset avatar renders for valid name', function () {
    $html = view('components.boring-avatar.sunset', [
        'name' => 'Diana',
        'size' => 40,
        'attributes' => new \Illuminate\View\ComponentAttributeBag,
    ])->render();

    expect($html)->toContain('<svg')
        ->and($html)->toContain('viewBox="0 0 80 80"')
        ->and($html)->toContain('role="img"');
});

it('wrapper component defaults to beam variant', function () {
    $html = view('components.boring-avatar', [
        'name' => 'John Doe',
        'attributes' => new \Illuminate\View\ComponentAttributeBag,
    ])->render();

    expect($html)->toContain('<svg')
        ->and($html)->toContain('beam-mask-');
});

it('wrapper component renders different variants', function () {
    $variants = ['bauhaus', 'beam', 'marble', 'pixel', 'ring', 'sunset'];

    foreach ($variants as $variant) {
        $html = view('components.boring-avatar', [
            'variant' => $variant,
            'name' => 'Test User',
            'attributes' => new \Illuminate\View\ComponentAttributeBag,
        ])->render();

        expect($html)->toContain("{$variant}-mask-");
    }
});

it('square avatar renders with square corners', function () {
    $html = view('components.boring-avatar', [
        'name' => 'Test User',
        'square' => true,
        'attributes' => new \Illuminate\View\ComponentAttributeBag,
    ])->render();

    expect($html)->toContain('rx="0"');
});

it('circular avatar renders with rounded corners', function () {
    $html = view('components.boring-avatar', [
        'name' => 'Test User',
        'square' => false,
        'attributes' => new \Illuminate\View\ComponentAttributeBag,
    ])->render();

    expect($html)->not->toContain('rx="0"');
});

it('custom colors are applied', function () {
    $customColors = ['#ff0000', '#00ff00', '#0000ff', '#ffff00', '#ff00ff'];
    $html = view('components.boring-avatar.beam', [
        'name' => 'Test User',
        'colors' => $customColors,
        'size' => 40,
        'attributes' => new \Illuminate\View\ComponentAttributeBag,
    ])->render();

    $foundColors = 0;
    foreach ($customColors as $color) {
        if (str_contains($html, $color)) {
            $foundColors++;
        }
    }

    expect($foundColors)->toBeGreaterThan(0);
});

it('different names generate different avatars', function () {
    $html1 = view('components.boring-avatar', [
        'name' => 'Alice',
        'attributes' => new \Illuminate\View\ComponentAttributeBag,
    ])->render();

    $html2 = view('components.boring-avatar', [
        'name' => 'Bob',
        'attributes' => new \Illuminate\View\ComponentAttributeBag,
    ])->render();

    expect($html1)->not->toBe($html2);
});

it('same names generate same avatars', function () {
    $name = 'Test User';

    $html1 = view('components.boring-avatar', [
        'name' => $name,
        'attributes' => new \Illuminate\View\ComponentAttributeBag,
    ])->render();

    $html2 = view('components.boring-avatar', [
        'name' => $name,
        'attributes' => new \Illuminate\View\ComponentAttributeBag,
    ])->render();

    expect($html1)->toBe($html2);
});

it('title is included when title prop is true', function () {
    $html = view('components.boring-avatar', [
        'name' => 'Test User',
        'title' => true,
        'attributes' => new \Illuminate\View\ComponentAttributeBag,
    ])->render();

    expect($html)->toContain('<title>Test User</title>');
});

it('title is not included when title prop is false', function () {
    $html = view('components.boring-avatar', [
        'name' => 'Test User',
        'title' => false,
        'attributes' => new \Illuminate\View\ComponentAttributeBag,
    ])->render();

    expect($html)->not->toContain('<title>');
});

it('size attribute is applied to svg', function () {
    $html = view('components.boring-avatar', [
        'name' => 'Test User',
        'size' => '64px',
        'attributes' => new \Illuminate\View\ComponentAttributeBag,
    ])->render();

    expect($html)->toContain('width="64px"')
        ->and($html)->toContain('height="64px"');
});

it('unsupported variant defaults to beam', function () {
    $html = view('components.boring-avatar', [
        'name' => 'Test User',
        'variant' => 'invalid-variant',
        'attributes' => new \Illuminate\View\ComponentAttributeBag,
    ])->render();

    expect($html)->toContain('beam-mask-');
});

it('additional attributes are forwarded to svg', function () {
    $attributes = new \Illuminate\View\ComponentAttributeBag(['class' => 'custom-class', 'id' => 'avatar-1']);

    $html = view('components.boring-avatar', [
        'name' => 'Test User',
        'attributes' => $attributes,
    ])->render();

    expect($html)->toContain('class="custom-class"')
        ->and($html)->toContain('id="avatar-1"');
});
