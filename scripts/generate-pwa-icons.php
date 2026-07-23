<?php

$iconDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'icons';

if (!is_dir($iconDir)) {
    mkdir($iconDir, 0777, true);
}

if (!function_exists('imagecreatetruecolor')) {
    fwrite(STDERR, "The PHP GD extension is required to generate PWA icons.\n");
    exit(1);
}

function roundedRect($image, int $x, int $y, int $width, int $height, int $radius, int $color): void
{
    imagefilledrectangle($image, $x + $radius, $y, $x + $width - $radius, $y + $height, $color);
    imagefilledrectangle($image, $x, $y + $radius, $x + $width, $y + $height - $radius, $color);
    imagefilledellipse($image, $x + $radius, $y + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($image, $x + $width - $radius, $y + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($image, $x + $radius, $y + $height - $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($image, $x + $width - $radius, $y + $height - $radius, $radius * 2, $radius * 2, $color);
}

function drawCloud($image, int $size, int $cx, int $cy, float $scale, int $fill, int $line): void
{
    $w = (int) ($size * 0.42 * $scale);
    $h = (int) ($size * 0.24 * $scale);

    imagefilledellipse($image, $cx - (int) ($w * 0.22), $cy, (int) ($w * 0.52), (int) ($h * 0.95), $fill);
    imagefilledellipse($image, $cx + (int) ($w * 0.08), $cy - (int) ($h * 0.18), (int) ($w * 0.55), (int) ($h * 1.12), $fill);
    imagefilledellipse($image, $cx + (int) ($w * 0.36), $cy + (int) ($h * 0.05), (int) ($w * 0.42), (int) ($h * 0.78), $fill);
    imagefilledrectangle($image, $cx - (int) ($w * 0.48), $cy, $cx + (int) ($w * 0.54), $cy + (int) ($h * 0.42), $fill);

    imagesetthickness($image, max(2, (int) ($size * 0.018)));
    imagearc($image, $cx - (int) ($w * 0.22), $cy, (int) ($w * 0.52), (int) ($h * 0.95), 180, 355, $line);
    imagearc($image, $cx + (int) ($w * 0.08), $cy - (int) ($h * 0.18), (int) ($w * 0.55), (int) ($h * 1.12), 190, 350, $line);
    imagearc($image, $cx + (int) ($w * 0.36), $cy + (int) ($h * 0.05), (int) ($w * 0.42), (int) ($h * 0.78), 200, 20, $line);
    imageline($image, $cx - (int) ($w * 0.48), $cy + (int) ($h * 0.42), $cx + (int) ($w * 0.54), $cy + (int) ($h * 0.42), $line);
    imagesetthickness($image, 1);
}

function drawCodeMark($image, int $size, int $color): void
{
    imagesetthickness($image, max(3, (int) ($size * 0.026)));
    $left = [
        (int) ($size * 0.27), (int) ($size * 0.59),
        (int) ($size * 0.20), (int) ($size * 0.50),
        (int) ($size * 0.27), (int) ($size * 0.41),
    ];
    $right = [
        (int) ($size * 0.73), (int) ($size * 0.41),
        (int) ($size * 0.80), (int) ($size * 0.50),
        (int) ($size * 0.73), (int) ($size * 0.59),
    ];
    imageopenpolygon($image, $left, 3, $color);
    imageopenpolygon($image, $right, 3, $color);
    imageline($image, (int) ($size * 0.57), (int) ($size * 0.38), (int) ($size * 0.43), (int) ($size * 0.62), $color);
    imagesetthickness($image, 1);
}

function makeIcon(string $path, int $size, bool $maskable = false): void
{
    $image = imagecreatetruecolor($size, $size);
    imageantialias($image, true);

    for ($y = 0; $y < $size; $y++) {
        $ratio = $y / max(1, $size - 1);
        $color = imagecolorallocate(
            $image,
            (int) (15 + (127 - 15) * $ratio),
            (int) (23 + (29 - 23) * $ratio),
            (int) (42 + (29 - 42) * $ratio)
        );
        imageline($image, 0, $y, $size, $y, $color);
    }

    $grid = imagecolorallocatealpha($image, 255, 247, 237, 112);
    for ($line = 0; $line < $size; $line += max(16, (int) ($size * 0.075))) {
        imageline($image, $line, 0, $line, $size, $grid);
        imageline($image, 0, $line, $size, $line, $grid);
    }

    $white = imagecolorallocate($image, 255, 255, 255);
    $cream = imagecolorallocate($image, 255, 247, 237);
    $orange = imagecolorallocate($image, 249, 115, 22);
    $red = imagecolorallocate($image, 220, 38, 38);
    $amber = imagecolorallocate($image, 251, 191, 36);
    $shadow = imagecolorallocatealpha($image, 2, 6, 23, 74);
    $safe = $maskable ? 0.24 : 0.16;

    imagefilledellipse($image, (int) ($size * 0.5), (int) ($size * 0.5), (int) ($size * (1 - $safe)), (int) ($size * (1 - $safe)), $shadow);
    imagefilledellipse($image, (int) ($size * 0.5), (int) ($size * 0.48), (int) ($size * (0.76 - $safe / 3)), (int) ($size * (0.76 - $safe / 3)), $orange);
    imagefilledellipse($image, (int) ($size * 0.5), (int) ($size * 0.48), (int) ($size * (0.68 - $safe / 3)), (int) ($size * (0.68 - $safe / 3)), $red);

    drawCloud($image, $size, (int) ($size * 0.50), (int) ($size * 0.37), $maskable ? 0.86 : 0.96, $cream, $white);
    drawCodeMark($image, $size, $white);

    $scrollX = (int) ($size * 0.34);
    $scrollY = (int) ($size * 0.68);
    $scrollW = (int) ($size * 0.32);
    $scrollH = (int) ($size * 0.10);
    roundedRect($image, $scrollX, $scrollY, $scrollW, $scrollH, max(3, (int) ($size * 0.022)), $cream);
    imagefilledellipse($image, $scrollX, $scrollY + (int) ($scrollH * 0.5), (int) ($scrollH * 0.86), (int) ($scrollH * 1.18), $amber);
    imagefilledellipse($image, $scrollX + $scrollW, $scrollY + (int) ($scrollH * 0.5), (int) ($scrollH * 0.86), (int) ($scrollH * 1.18), $amber);
    imagesetthickness($image, max(1, (int) ($size * 0.008)));
    imageline($image, $scrollX + (int) ($scrollW * 0.24), $scrollY + (int) ($scrollH * 0.32), $scrollX + (int) ($scrollW * 0.76), $scrollY + (int) ($scrollH * 0.32), $orange);
    imageline($image, $scrollX + (int) ($scrollW * 0.24), $scrollY + (int) ($scrollH * 0.66), $scrollX + (int) ($scrollW * 0.68), $scrollY + (int) ($scrollH * 0.66), $orange);

    imagesetthickness($image, max(2, (int) ($size * 0.012)));
    imagearc($image, (int) ($size * 0.50), (int) ($size * 0.50), (int) ($size * 0.88), (int) ($size * 0.88), 210, 325, imagecolorallocatealpha($image, 255, 247, 237, 62));
    imagearc($image, (int) ($size * 0.50), (int) ($size * 0.50), (int) ($size * 0.80), (int) ($size * 0.80), 30, 150, imagecolorallocatealpha($image, 251, 191, 36, 70));
    imagesetthickness($image, 1);

    imagepng($image, $path);
    imagedestroy($image);
}

makeIcon($iconDir . DIRECTORY_SEPARATOR . 'icon-192.png', 192);
makeIcon($iconDir . DIRECTORY_SEPARATOR . 'apple-touch-icon.png', 180);
makeIcon($iconDir . DIRECTORY_SEPARATOR . 'icon-512.png', 512);
makeIcon($iconDir . DIRECTORY_SEPARATOR . 'maskable-512.png', 512, true);

echo "PWA icons generated.\n";
