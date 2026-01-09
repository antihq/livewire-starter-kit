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
        return match ($this->variant) {
            'bauhaus' => $this->renderBauhaus(),
            'beam' => $this->renderBeam(),
            'marble' => $this->renderMarble(),
            'pixel' => $this->renderPixel(),
            'ring' => $this->renderRing(),
            'sunset' => $this->renderSunset(),
        };
    }

    private function renderBauhaus(): View
    {
        $numFromName = $this->hash($this->name);
        $range = count($this->colors);
        $maskId = $this->variant.'-mask-'.preg_replace('/[^a-zA-Z0-9]/', '', $this->name).'-'.$numFromName;
        $SIZE = 80;

        $elementsProperties = [];
        for ($i = 0; $i < 4; $i++) {
            $elementsProperties[] = [
                'color' => $this->colors[$this->modulus((int) ($numFromName + $i), $range)],
                'translateX' => $this->unit((int) ($numFromName * ($i + 1)), (int) ($SIZE / 2 - ($i + 17)), 1),
                'translateY' => $this->unit((int) ($numFromName * ($i + 1)), (int) ($SIZE / 2 - ($i + 17)), 2),
                'rotate' => $this->unit((int) ($numFromName * ($i + 1)), 360),
                'isSquare' => $this->boolean($numFromName, 2),
            ];
        }

        return view('components.boring-avatar.bauhaus', [
            'SIZE' => $SIZE,
            'maskId' => $maskId,
            'elementsProperties' => $elementsProperties,
            'name' => $this->name,
            'title' => $this->title,
            'square' => $this->square,
            'size' => $this->size,
            'attributes' => $this->attributes,
        ]);
    }

    private function renderBeam(): View
    {
        $numFromName = $this->hash($this->name);
        $range = count($this->colors);
        $SIZE = 36;

        $wrapperColor = $this->colors[$this->modulus($numFromName, $range)];

        $preTranslateX = $this->unit($numFromName, 10, 1);
        $wrapperTranslateX = $preTranslateX < 5 ? $preTranslateX + $SIZE / 9 : $preTranslateX;

        $preTranslateY = $this->unit($numFromName, 10, 2);
        $wrapperTranslateY = $preTranslateY < 5 ? $preTranslateY + $SIZE / 9 : $preTranslateY;

        $data = [
            'wrapperColor' => $wrapperColor,
            'faceColor' => $this->getContrastColor($wrapperColor),
            'backgroundColor' => $this->colors[$this->modulus((int) ($numFromName + 13), $range)],
            'wrapperTranslateX' => $wrapperTranslateX,
            'wrapperTranslateY' => $wrapperTranslateY,
            'wrapperRotate' => $this->unit($numFromName, 360),
            'wrapperScale' => 1 + $this->unit($numFromName, $SIZE / 12) / 10,
            'isMouthOpen' => $this->boolean($numFromName, 2),
            'isCircle' => $this->boolean($numFromName, 1),
            'eyeSpread' => $this->unit($numFromName, 5),
            'mouthSpread' => $this->unit($numFromName, 3),
            'faceRotate' => $this->unit($numFromName, 10, 3),
            'faceTranslateX' => $wrapperTranslateX > $SIZE / 6 ? $wrapperTranslateX / 2 : $this->unit($numFromName, 8, 1),
            'faceTranslateY' => $wrapperTranslateY > $SIZE / 6 ? $wrapperTranslateY / 2 : $this->unit($numFromName, 7, 2),
        ];

        $mouthPath = $data['isMouthOpen']
            ? 'M15 '.(19 + $data['mouthSpread']).'c2 1 4 1 6 0'
            : 'M13,'.(19 + $data['mouthSpread']).' a1,0.75 0 0,0 10,0';

        $maskId = $this->variant.'-mask-'.preg_replace('/[^a-zA-Z0-9]/', '', $this->name).'-'.$numFromName;

        return view('components.boring-avatar.beam', [
            'SIZE' => $SIZE,
            'data' => $data,
            'mouthPath' => $mouthPath,
            'maskId' => $maskId,
            'name' => $this->name,
            'title' => $this->title,
            'square' => $this->square,
            'size' => $this->size,
            'attributes' => $this->attributes,
        ]);
    }

    private function renderMarble(): View
    {
        $numFromName = $this->hash($this->name);
        $range = count($this->colors);
        $ELEMENTS = 3;
        $SIZE = 80;

        $elementsProperties = [];
        for ($i = 0; $i < $ELEMENTS; $i++) {
            $elementsProperties[] = [
                'color' => $this->colors[$this->modulus((int) ($numFromName + $i), $range)],
                'translateX' => $this->unit((int) ($numFromName * ($i + 1)), (int) ($SIZE / 10), 1),
                'translateY' => $this->unit((int) ($numFromName * ($i + 1)), (int) ($SIZE / 10), 2),
                'scale' => 1.2 + $this->unit((int) ($numFromName * ($i + 1)), (int) ($SIZE / 20)) / 10,
                'rotate' => $this->unit((int) ($numFromName * ($i + 1)), 360, 1),
            ];
        }

        $filterId = 'marble-filter-'.preg_replace('/[^a-zA-Z0-9]/', '', $this->name).'-'.$numFromName;
        $maskId = $this->variant.'-mask-'.preg_replace('/[^a-zA-Z0-9]/', '', $this->name).'-'.$numFromName;

        return view('components.boring-avatar.marble', [
            'SIZE' => $SIZE,
            'elementsProperties' => $elementsProperties,
            'filterId' => $filterId,
            'maskId' => $maskId,
            'name' => $this->name,
            'title' => $this->title,
            'square' => $this->square,
            'size' => $this->size,
            'attributes' => $this->attributes,
        ]);
    }

    private function renderPixel(): View
    {
        $numFromName = $this->hash($this->name);
        $range = count($this->colors);
        $ELEMENTS = 64;
        $SIZE = 80;

        $pixelColors = [];
        for ($i = 0; $i < $ELEMENTS; $i++) {
            $pixelColors[] = $this->colors[$this->modulus((int) ($numFromName % ($i + 1)), $range)];
        }

        $positions = [
            [0, 0], [10, 0], [20, 0], [30, 0], [40, 0], [50, 0], [60, 0], [70, 0],
            [0, 10], [10, 10], [20, 10], [30, 10], [40, 10], [50, 10], [60, 10], [70, 10],
            [0, 20], [10, 20], [20, 20], [30, 20], [40, 20], [50, 20], [60, 20], [70, 20],
            [0, 30], [10, 30], [20, 30], [30, 30], [40, 30], [50, 30], [60, 30], [70, 30],
            [0, 40], [10, 40], [20, 40], [30, 40], [40, 40], [50, 40], [60, 40], [70, 40],
            [0, 50], [10, 50], [20, 50], [30, 50], [40, 50], [50, 50], [60, 50], [70, 50],
            [0, 60], [10, 60], [20, 60], [30, 60], [40, 60], [50, 60], [60, 60], [70, 60],
            [0, 70], [10, 70], [20, 70], [30, 70], [40, 70], [50, 70], [60, 70], [70, 70],
        ];

        $maskId = $this->variant.'-mask-'.preg_replace('/[^a-zA-Z0-9]/', '', $this->name).'-'.$numFromName;

        return view('components.boring-avatar.pixel', [
            'SIZE' => $SIZE,
            'pixelColors' => $pixelColors,
            'positions' => $positions,
            'maskId' => $maskId,
            'name' => $this->name,
            'title' => $this->title,
            'square' => $this->square,
            'size' => $this->size,
            'attributes' => $this->attributes,
        ]);
    }

    private function renderRing(): View
    {
        $numFromName = $this->hash($this->name);
        $range = count($this->colors);
        $COLORS = 5;
        $SIZE = 90;

        $colorsShuffle = [];
        for ($i = 0; $i < $COLORS; $i++) {
            $colorsShuffle[] = $this->colors[$this->modulus((int) ($numFromName + $i), $range)];
        }

        $colorsList = [
            $colorsShuffle[0],
            $colorsShuffle[1],
            $colorsShuffle[1],
            $colorsShuffle[2],
            $colorsShuffle[3],
            $colorsShuffle[3],
            $colorsShuffle[0],
            $colorsShuffle[4],
            $colorsShuffle[3],
        ];

        $maskId = $this->variant.'-mask-'.preg_replace('/[^a-zA-Z0-9]/', '', $this->name).'-'.$numFromName;

        return view('components.boring-avatar.ring', [
            'SIZE' => $SIZE,
            'colorsList' => $colorsList,
            'maskId' => $maskId,
            'name' => $this->name,
            'title' => $this->title,
            'square' => $this->square,
            'size' => $this->size,
            'attributes' => $this->attributes,
        ]);
    }

    private function renderSunset(): View
    {
        $numFromName = $this->hash($this->name);
        $range = count($this->colors);
        $ELEMENTS = 4;
        $SIZE = 80;

        $colorsList = [];
        for ($i = 0; $i < $ELEMENTS; $i++) {
            $colorsList[] = $this->colors[$this->modulus((int) ($numFromName + $i), $range)];
        }

        $nameWithoutSpace = preg_replace('/\s/', '', $this->name);
        $gradient0Id = 'gradient-paint0-linear-'.$nameWithoutSpace;
        $gradient1Id = 'gradient-paint1-linear-'.$nameWithoutSpace;
        $maskId = $this->variant.'-mask-'.preg_replace('/[^a-zA-Z0-9]/', '', $this->name).'-'.$numFromName;

        return view('components.boring-avatar.sunset', [
            'SIZE' => $SIZE,
            'colorsList' => $colorsList,
            'nameWithoutSpace' => $nameWithoutSpace,
            'gradient0Id' => $gradient0Id,
            'gradient1Id' => $gradient1Id,
            'maskId' => $maskId,
            'name' => $this->name,
            'title' => $this->title,
            'square' => $this->square,
            'size' => $this->size,
            'attributes' => $this->attributes,
        ]);
    }

    public function hash(string $str): int
    {
        return (int) preg_replace('/[^0-9]/', '', md5($str));
    }

    public function contrast(string $color): string
    {
        return $this->getContrastColor($color);
    }

    public function randomColor(int $numFromName, array $colors, int $range): string
    {
        return $colors[$this->modulus($numFromName, $range)];
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

    public function modulus(int $numFromName, int $range): int
    {
        return abs($numFromName % $range);
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
