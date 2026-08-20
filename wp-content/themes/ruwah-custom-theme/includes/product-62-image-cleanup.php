<?php
defined('ABSPATH') || exit;

/**
 * One-time cleanup for the current Hydrating Moisturizer featured PNG.
 * Keeps attachment/product mapping unchanged and removes only the baked floor
 * below/outside the jar's lower silhouette.
 */
function ruwah_product_62_remove_baked_floor(): void {
    $marker = 'ruwah_product_62_floor_clean_v2';
    if ('done' === get_option($marker)) {
        return;
    }

    if (! function_exists('wc_get_product') || ! function_exists('imagecreatefrompng') || ! function_exists('imagepng')) {
        return;
    }

    $product = wc_get_product(62);
    if (! $product || 329 !== (int) $product->get_image_id()) {
        return;
    }

    $attachment_id = 329;
    if ('image/png' !== get_post_mime_type($attachment_id)) {
        return;
    }

    $file = get_attached_file($attachment_id);
    if (! $file || ! is_readable($file) || ! is_writable(dirname($file))) {
        return;
    }

    $image = @imagecreatefrompng($file);
    if (! $image) {
        return;
    }

    $width = imagesx($image);
    $height = imagesy($image);
    if ($width < 500 || $height < 500) {
        imagedestroy($image);
        return;
    }

    imagealphablending($image, false);
    imagesavealpha($image, true);

    $start_y = max(0, (int) floor($height * 0.66));
    $min_core = max(16, (int) floor($width * 0.014));
    $padding = max(4, (int) round($width * 0.004));
    $bounds = [];
    $last_valid_y = -1;

    // Find the actual jar-base silhouette. Neutral white/grey floor pixels have
    // almost no channel spread; the jar's olive/metal lower rim retains it.
    for ($y = $start_y; $y < $height; $y++) {
        $left = $width;
        $right = -1;
        $count = 0;
        for ($x = 0; $x < $width; $x++) {
            $rgba = imagecolorsforindex($image, imagecolorat($image, $x, $y));
            if ((int) $rgba['alpha'] >= 120) {
                continue;
            }
            $r = (int) $rgba['red'];
            $g = (int) $rgba['green'];
            $b = (int) $rgba['blue'];
            $max = max($r, $g, $b);
            $min = min($r, $g, $b);
            if (($max - $min) >= 5 && $min < 248) {
                $left = min($left, $x);
                $right = max($right, $x);
                $count++;
            }
        }
        if ($count >= $min_core && $right >= $left) {
            $bounds[$y] = [max(0, $left - $padding), min($width - 1, $right + $padding)];
            $last_valid_y = $y;
        }
    }

    if ($last_valid_y < $start_y || empty($bounds)) {
        imagedestroy($image);
        return;
    }

    // Sanity guard: the detected lower silhouette must end in the lower quarter.
    if ($last_valid_y < (int) floor($height * 0.70) || $last_valid_y > (int) floor($height * 0.82)) {
        imagedestroy($image);
        return;
    }

    $transparent = imagecolorallocatealpha($image, 255, 255, 255, 127);
    $previous = null;
    for ($y = $start_y; $y < $height; $y++) {
        if ($y > $last_valid_y) {
            imagefilledrectangle($image, 0, $y, $width - 1, $y, $transparent);
            continue;
        }

        if (isset($bounds[$y])) {
            $previous = $bounds[$y];
        }
        if (! $previous) {
            continue;
        }

        [$left, $right] = $previous;
        if ($left > 0) {
            imagefilledrectangle($image, 0, $y, $left - 1, $y, $transparent);
        }
        if ($right < $width - 1) {
            imagefilledrectangle($image, $right + 1, $y, $width - 1, $y, $transparent);
        }
    }

    $backup = $file . '.rwb-before-floor-clean-v2';
    if (! is_file($backup) && ! @copy($file, $backup)) {
        imagedestroy($image);
        return;
    }

    $tmp = $file . '.rwb-cleaning.tmp.png';
    $saved = imagepng($image, $tmp, 6);
    imagedestroy($image);
    if (! $saved || ! is_file($tmp) || filesize($tmp) < 10000) {
        @unlink($tmp);
        return;
    }

    if (! @rename($tmp, $file)) {
        @unlink($tmp);
        return;
    }

    require_once ABSPATH . 'wp-admin/includes/image.php';
    $old_meta = wp_get_attachment_metadata($attachment_id);
    if (is_array($old_meta) && ! empty($old_meta['sizes'])) {
        $dir = dirname($file);
        foreach ($old_meta['sizes'] as $size) {
            if (! empty($size['file'])) {
                $derived = trailingslashit($dir) . basename((string) $size['file']);
                if (is_file($derived)) {
                    @unlink($derived);
                }
            }
        }
    }

    $metadata = wp_generate_attachment_metadata($attachment_id, $file);
    if (! is_array($metadata) || empty($metadata['width']) || empty($metadata['height'])) {
        @copy($backup, $file);
        $restored = wp_generate_attachment_metadata($attachment_id, $file);
        if (is_array($restored)) {
            wp_update_attachment_metadata($attachment_id, $restored);
        }
        return;
    }

    wp_update_attachment_metadata($attachment_id, $metadata);
    clean_post_cache($attachment_id);
    if (function_exists('wc_delete_product_transients')) {
        wc_delete_product_transients(62);
    }
    update_option($marker, 'done', false);
}
