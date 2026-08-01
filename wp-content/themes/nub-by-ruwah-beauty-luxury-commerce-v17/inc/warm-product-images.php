<?php
/**
 * Generate cached warm-background derivatives for product images whose
 * edge-connected backdrop is cool purple/lavender. Original media is untouched.
 */
defined('ABSPATH') || exit;

function ruwa_product_attachment_ids(): array {
    static $ids = null;
    if (is_array($ids)) return $ids;
    $ids = [];
    if (!function_exists('wc_get_products')) return $ids;
    $products = wc_get_products(['status' => 'publish', 'limit' => -1, 'return' => 'objects']);
    foreach ($products as $product) {
        if (!$product instanceof WC_Product) continue;
        $featured = (int) $product->get_image_id();
        if ($featured > 0) $ids[$featured] = true;
        foreach ($product->get_gallery_image_ids() as $gallery_id) {
            $gallery_id = (int) $gallery_id;
            if ($gallery_id > 0) $ids[$gallery_id] = true;
        }
    }
    return $ids;
}
function ruwa_is_product_attachment(int $attachment_id): bool { $ids = ruwa_product_attachment_ids(); return isset($ids[$attachment_id]); }
function ruwa_rgb_channels(int $rgb): array { return [($rgb >> 16) & 255, ($rgb >> 8) & 255, $rgb & 255]; }
function ruwa_is_cool_backdrop_pixel(int $rgb): bool {
    [$r, $g, $b] = ruwa_rgb_channels($rgb);
    $max = max($r, $g, $b); $min = min($r, $g, $b); $delta = $max - $min;
    if ($max < 58 || $delta < 5) return false;
    if ($max === $r) $hue = 60 * fmod((($g - $b) / $delta), 6);
    elseif ($max === $g) $hue = 60 * ((($b - $r) / $delta) + 2);
    else $hue = 60 * ((($r - $g) / $delta) + 4);
    if ($hue < 0) $hue += 360;
    $saturation = $delta / $max;
    $lavender = $b >= ($g + 3) && $r >= ($g - 8) && $saturation >= 0.045;
    $cool_blue = $b >= ($r + 5) && $b >= ($g + 3) && $saturation >= 0.055;
    return (($hue >= 205 && $hue <= 335 && $saturation >= 0.045) || $lavender || $cool_blue);
}
function ruwa_has_cool_image_edges($image, int $width, int $height): bool {
    $step = max(3, (int) floor(min($width, $height) / 90)); $cool = 0; $total = 0;
    for ($x = 0; $x < $width; $x += $step) foreach ([0, $height - 1] as $y) { $total++; if (ruwa_is_cool_backdrop_pixel(imagecolorat($image, $x, $y))) $cool++; }
    for ($y = 0; $y < $height; $y += $step) foreach ([0, $width - 1] as $x) { $total++; if (ruwa_is_cool_backdrop_pixel(imagecolorat($image, $x, $y))) $cool++; }
    return $total > 0 && ($cool / $total) >= 0.28;
}
function ruwa_warm_product_image(int $attachment_id) {
    static $cache = [];
    if (array_key_exists($attachment_id, $cache)) return $cache[$attachment_id];
    $cache[$attachment_id] = false;
    if (!ruwa_is_product_attachment($attachment_id) || !function_exists('imagecreatefromstring')) return false;
    $source = get_attached_file($attachment_id);
    if (!$source || !is_readable($source)) return false;
    $uploads = wp_get_upload_dir();
    if (!empty($uploads['error'])) return false;
    $fingerprint = substr(hash('sha256', (string) @filemtime($source) . ':' . (string) @filesize($source) . ':ruwa-warm-v1'), 0, 14);
    $directory = trailingslashit($uploads['basedir']) . 'ruwa-warm-products';
    $url_base = trailingslashit($uploads['baseurl']) . 'ruwa-warm-products';
    $target = trailingslashit($directory) . $attachment_id . '-' . $fingerprint . '.jpg';
    $marker = $target . '.not-cool';
    if (is_readable($target)) {
        $size = @getimagesize($target);
        if ($size) return $cache[$attachment_id] = ['url' => trailingslashit($url_base) . basename($target), 'width' => (int) $size[0], 'height' => (int) $size[1]];
    }
    if (is_readable($marker) || !wp_mkdir_p($directory)) return false;
    $blob = @file_get_contents($source); $original = $blob ? @imagecreatefromstring($blob) : false;
    if (!$original) return false;
    $source_width = imagesx($original); $source_height = imagesy($original); $max_dimension = 820;
    $scale = min(1, $max_dimension / max($source_width, $source_height));
    $width = max(1, (int) round($source_width * $scale)); $height = max(1, (int) round($source_height * $scale));
    $image = imagecreatetruecolor($width, $height); imagealphablending($image, true); imagesavealpha($image, false);
    $cream = imagecolorallocate($image, 247, 240, 230); imagefill($image, 0, 0, $cream);
    imagecopyresampled($image, $original, 0, 0, 0, 0, $width, $height, $source_width, $source_height); imagedestroy($original);
    if (!ruwa_has_cool_image_edges($image, $width, $height)) { @file_put_contents($marker, 'not-cool'); imagedestroy($image); return false; }
    $mask = imagecreatetruecolor($width, $height); $black = imagecolorallocate($mask, 0, 0, 0); $white = imagecolorallocate($mask, 255, 255, 255); imagefill($mask, 0, 0, $black);
    $queue = new SplQueue();
    for ($x = 0; $x < $width; $x++) { $queue->enqueue($x); $queue->enqueue((($height - 1) * $width) + $x); }
    for ($y = 1; $y < $height - 1; $y++) { $queue->enqueue($y * $width); $queue->enqueue(($y * $width) + ($width - 1)); }
    $background_pixels = 0;
    while (!$queue->isEmpty()) {
        $index = (int) $queue->dequeue(); $x = $index % $width; $y = intdiv($index, $width);
        if (imagecolorat($mask, $x, $y) === $white || !ruwa_is_cool_backdrop_pixel(imagecolorat($image, $x, $y))) continue;
        imagesetpixel($mask, $x, $y, $white); $background_pixels++;
        if ($x > 0 && imagecolorat($mask, $x - 1, $y) !== $white) $queue->enqueue($index - 1);
        if ($x + 1 < $width && imagecolorat($mask, $x + 1, $y) !== $white) $queue->enqueue($index + 1);
        if ($y > 0 && imagecolorat($mask, $x, $y - 1) !== $white) $queue->enqueue($index - $width);
        if ($y + 1 < $height && imagecolorat($mask, $x, $y + 1) !== $white) $queue->enqueue($index + $width);
    }
    if ($background_pixels < (int) (($width * $height) * 0.12)) {
        @file_put_contents($marker, 'insufficient-cool-backdrop'); imagedestroy($mask); imagedestroy($image); return false;
    }
    for ($y = 0; $y < $height; $y++) for ($x = 0; $x < $width; $x++) {
        if (imagecolorat($mask, $x, $y) !== $white) continue;
        [$r, $g, $b] = ruwa_rgb_channels(imagecolorat($image, $x, $y));
        $luminance = (0.2126 * $r) + (0.7152 * $g) + (0.0722 * $b);
        $shade = max(-16, min(10, (int) round(($luminance - 188) * 0.12)));
        $warm = (max(0, min(255, 247 + $shade)) << 16) | (max(0, min(255, 240 + $shade)) << 8) | max(0, min(255, 230 + $shade));
        imagesetpixel($image, $x, $y, $warm);
    }
    imagedestroy($mask); $saved = @imagejpeg($image, $target, 91); imagedestroy($image);
    if (!$saved || !is_readable($target)) return false;
    return $cache[$attachment_id] = ['url' => trailingslashit($url_base) . basename($target), 'width' => $width, 'height' => $height];
}
add_filter('wp_get_attachment_image_src', function ($image, $attachment_id, $size, $icon) {
    if (!$image || $icon) return $image;
    $warm = ruwa_warm_product_image((int) $attachment_id);
    return $warm ? [$warm['url'], $warm['width'], $warm['height'], false] : $image;
}, 99, 4);
add_filter('wp_calculate_image_srcset', function ($sources, $size_array, $image_src, $image_meta, $attachment_id) {
    return ruwa_warm_product_image((int) $attachment_id) ? false : $sources;
}, 99, 5);
