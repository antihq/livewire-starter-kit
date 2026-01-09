<?php

use App\Services\BoringAvatarUtilities;

it('generates deterministic hash for same name', function () {
    $utilities = new BoringAvatarUtilities;

    $hash1 = $utilities->hash('John Doe');
    $hash2 = $utilities->hash('John Doe');
    $hash3 = $utilities->hash('Jane Smith');

    expect($hash1)->toBe($hash2)
        ->and($hash1)->not->toBe($hash3);
});

it('generates integer hash for long names', function () {
    $utilities = new BoringAvatarUtilities;

    $hash = $utilities->hash('Oliver Servin');

    expect($hash)->toBeInt();
});

it('generates different hashes for different names', function () {
    $utilities = new BoringAvatarUtilities;

    $hash1 = $utilities->hash('Alice');
    $hash2 = $utilities->hash('Bob');
    $hash3 = $utilities->hash('Charlie');

    expect($hash1)->not->toBe($hash2)
        ->and($hash2)->not->toBe($hash3)
        ->and($hash1)->not->toBe($hash3);
});

it('getModulus returns correct modulo', function () {
    $utilities = new BoringAvatarUtilities;

    expect($utilities->modulus(10, 3))->toBe(1)
        ->and($utilities->modulus(15, 5))->toBe(0)
        ->and($utilities->modulus(7, 4))->toBe(3);
});

it('getDigit extracts correct digit', function () {
    $utilities = new BoringAvatarUtilities;

    expect($utilities->digit(1234, 0))->toBe(4)
        ->and($utilities->digit(1234, 1))->toBe(3)
        ->and($utilities->digit(1234, 2))->toBe(2)
        ->and($utilities->digit(1234, 3))->toBe(1);
});

it('getBoolean returns correct boolean based on digit parity', function () {
    $utilities = new BoringAvatarUtilities;

    expect($utilities->boolean(1234, 0))->toBeTrue()
        ->and($utilities->boolean(1235, 0))->toBeFalse()
        ->and($utilities->boolean(1236, 0))->toBeTrue();
});

it('getAngle calculates correct angle in degrees', function () {
    $utilities = new BoringAvatarUtilities;

    $angle1 = $utilities->angle(1, 0);
    $angle2 = $utilities->angle(0, 1);

    expect(abs($angle1 - 0))->toBeLessThan(0.001)
        ->and(abs($angle2 - 90))->toBeLessThan(0.001);
});

it('getUnit returns correct unit value', function () {
    $utilities = new BoringAvatarUtilities;

    $unit1 = $utilities->unit(10, 5);
    $unit2 = $utilities->unit(15, 10);

    expect($unit1)->toBe(0)
        ->and($unit2)->toBe(5);
});

it('getUnit negates value when index digit is even', function () {
    $utilities = new BoringAvatarUtilities;

    $num = 1234;
    $range = 10;
    $value1 = $utilities->unit($num, $range, 0);
    $value2 = $utilities->unit($num, $range, 1);

    expect($value1)->toBe(-$value2);
});

it('getRandomColor selects color from array', function () {
    $utilities = new BoringAvatarUtilities;

    $colors = ['#ff0000', '#00ff00', '#0000ff', '#ffff00'];
    $color = $utilities->randomColor(0, $colors, count($colors));

    expect(in_array($color, $colors))->toBeTrue();
});

it('getRandomColor is deterministic for same input', function () {
    $utilities = new BoringAvatarUtilities;

    $colors = ['#ff0000', '#00ff00', '#0000ff'];
    $color1 = $utilities->randomColor(10, $colors, count($colors));
    $color2 = $utilities->randomColor(10, $colors, count($colors));

    expect($color1)->toBe($color2);
});

it('getContrast returns white for dark colors', function () {
    $utilities = new BoringAvatarUtilities;

    expect($utilities->contrast('#000000'))->toBe('#FFFFFF')
        ->and($utilities->contrast('#1e293b'))->toBe('#FFFFFF');
});

it('getContrast returns black for light colors', function () {
    $utilities = new BoringAvatarUtilities;

    expect($utilities->contrast('#ffffff'))->toBe('#000000')
        ->and($utilities->contrast('#e2e8f0'))->toBe('#000000');
});

it('getContrast handles hex color with and without #', function () {
    $utilities = new BoringAvatarUtilities;

    $color1 = $utilities->contrast('#ffffff');
    $color2 = $utilities->contrast('ffffff');

    expect($color1)->toBe($color2);
});
