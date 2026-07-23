<?php

$iconDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'icons';

if (!is_dir($iconDir)) {
    mkdir($iconDir, 0777, true);
}

if (!function_exists('imagecreatetruecolor')) {
    fwrite(STDERR, "The PHP GD extension is required to generate PWA icons.\n");
    exit(1);
}

function makeIcon(string $path, int $size, bool $maskable = false): void
{
    $image = imagecreatetruecolor($size, $size);

    for ($y = 0; $y < $size; $y++) {
        $ratio = $y / max(1, $size - 1);
        $color = imagecolorallocate(
            $image,
            (int) (249 + (220 - 249) * $ratio),
            (int) (115 + (38 - 115) * $ratio),
            (int) (22 + (38 - 22) * $ratio)
        );
        imageline($image, 0, $y, $size, $y, $color);
    }

    $white = imagecolorallocate($image, 255, 255, 255);
    $orange = imagecolorallocate($image, 249, 115, 22);
    $padding = $maskable ? (int) ($size * 0.18) : (int) ($size * 0.12);

    imagefilledellipse($image, (int) ($size * 0.5), (int) ($size * 0.48), $size - ($padding * 2), $size - ($padding * 2), $white);
    imagefilledellipse($image, (int) ($size * 0.5), (int) ($size * 0.48), $size - ($padding * 2) - 12, $size - ($padding * 2) - 12, $orange);

    $font = 5;
    $text = 'AD';
    $textWidth = imagefontwidth($font) * strlen($text);
    $textHeight = imagefontheight($font);

    imagestring($image, $font, (int) (($size - $textWidth) / 2), (int) (($size - $textHeight) / 2), $text, $white);
    imagepng($image, $path);
    imagedestroy($image);
}

makeIcon($iconDir . DIRECTORY_SEPARATOR . 'icon-192.png', 192);
makeIcon($iconDir . DIRECTORY_SEPARATOR . 'icon-512.png', 512);
makeIcon($iconDir . DIRECTORY_SEPARATOR . 'maskable-512.png', 512, true);

echo "PWA icons generated.\n";
