<?php

namespace App\Services;

class BoringAvatarUtilities
{
    public function hash(string $name): int
    {
        $hash = 0;
        $length = strlen($name);

        for ($i = 0; $i < $length; $i++) {
            $character = ord($name[$i]);
            $hash = ($hash << 5) - $hash + $character;
            $hash = $hash & $hash;
        }

        return abs($hash);
    }

    public function modulus(int $num, int $max): int
    {
        return $num % $max;
    }

    public function digit(int $number, int $ntn): int
    {
        return (int) floor($number / (10 ** $ntn)) % 10;
    }

    public function boolean(int $number, int $ntn): bool
    {
        return ($this->digit($number, $ntn) % 2) === 0;
    }

    public function angle(float $x, float $y): float
    {
        return atan2($y, $x) * 180 / M_PI;
    }

    public function unit(int $number, int $range, ?int $index = null): int
    {
        $value = $number % $range;

        if ($index !== null && ($this->digit($number, $index) % 2) === 0) {
            return -$value;
        }

        return $value;
    }

    public function randomColor(int $number, array $colors, int $range): string
    {
        return $colors[$number % $range];
    }

    public function contrast(string $hexColor): string
    {
        $hexColor = ltrim($hexColor, '#');

        $red = hexdec(substr($hexColor, 0, 2));
        $green = hexdec(substr($hexColor, 2, 2));
        $blue = hexdec(substr($hexColor, 4, 2));

        $yiq = (($red * 299) + ($green * 587) + ($blue * 114)) / 1000;

        return $yiq >= 128 ? '#000000' : '#FFFFFF';
    }
}
