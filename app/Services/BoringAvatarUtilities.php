<?php

namespace App\Services;

class BoringAvatarUtilities
{
    public function hashCode(string $name): int
    {
        $hash = 0;
        for ($i = 0; $i < strlen($name); $i++) {
            $character = ord($name[$i]);
            $hash = (($hash << 5) - $hash) + $character;
            $hash = $hash & $hash;
        }

        return abs($hash);
    }

    public function getModulus(int $num, int $max): int
    {
        return $num % $max;
    }

    public function getDigit(int $number, int $ntn): int
    {
        return (int) floor(($number / pow(10, $ntn)) % 10);
    }

    public function getBoolean(int $number, int $ntn): bool
    {
        return ! (($this->getDigit($number, $ntn)) % 2);
    }

    public function getAngle(float $x, float $y): float
    {
        return atan2($y, $x) * 180 / M_PI;
    }

    public function getUnit(int $number, int $range, ?int $index = null): int
    {
        $value = $number % $range;

        if ($index !== null && (($this->getDigit($number, $index) % 2) === 0)) {
            return -$value;
        }

        return $value;
    }

    public function getRandomColor(int $number, array $colors, int $range): string
    {
        return $colors[$number % $range];
    }

    public function getContrast(string $hexcolor): string
    {
        if (substr($hexcolor, 0, 1) === '#') {
            $hexcolor = substr($hexcolor, 1);
        }

        $r = hexdec(substr($hexcolor, 0, 2));
        $g = hexdec(substr($hexcolor, 2, 2));
        $b = hexdec(substr($hexcolor, 4, 2));

        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

        return ($yiq >= 128) ? '#000000' : '#FFFFFF';
    }
}
