<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BoringAvatar extends Component
{
    public string $variant;

    public string $name;

    public ?array $colors;

    public bool $title;

    public bool $square;

    public int|string $size;

    public array $supportedVariants = ['bauhaus', 'beam', 'marble', 'pixel', 'ring', 'sunset'];

    public function __construct(
        string $variant = 'beam',
        string $name = '',
        ?array $colors = null,
        bool $title = false,
        bool $square = false,
        int|string $size = 40,
    ) {
        $this->variant = in_array($variant, $this->supportedVariants, true) ? $variant : 'beam';
        $this->name = $name;
        $this->colors = $colors ?? ['#E2E8F0', '#CBD5E1', '#94A3B8', '#64748B', '#475569', '#334155', '#1E293B', '#0F172A'];
        $this->title = $title;
        $this->square = $square;
        $this->size = $size;
        $this->attributes = new \Illuminate\View\ComponentAttributeBag;
    }

    public function render(): View
    {
        return view('components.boring-avatar.'.$this->variant, [
            'variant' => $this->variant,
            'name' => $this->name,
            'colors' => $this->colors,
            'title' => $this->title,
            'square' => $this->square,
            'size' => $this->size,
            'component' => $this,
            'attributes' => $this->attributes,
        ]);
    }

    public function hash(string $str): int
    {
        return (int) preg_replace('/[^0-9]/', '', md5($str));
    }

    public function unit(int $numFromName, int $range, int $index = 0): int
    {
        $digit = (int) ($numFromName / pow(10, $index)) % 10;
        $value = $numFromName % $range;

        if ($digit % 2 === 0) {
            return -$value;
        }

        return $value;
    }

    public function digit(int $numFromName, int $index): int
    {
        return (int) ($numFromName / pow(10, $index)) % 10;
    }

    public function boolean(int $numFromName, int $index): bool
    {
        return ($numFromName >> $index) % 2 === 0;
    }

    public function randomColor(int $numFromName, array $colors, int $range): string
    {
        return $colors[$this->modulus($numFromName, $range)];
    }

    public function contrast(string $color): string
    {
        return $this->getContrastColor($color);
    }

    public function modulus(int $numFromName, int $range): int
    {
        return $numFromName % $range;
    }

    private function getContrastColor(string $hexColor): string
    {
        $hexColor = ltrim($hexColor, '#');
        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));

        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

        return $yiq >= 128 ? '#000000' : '#FFFFFF';
    }
}
