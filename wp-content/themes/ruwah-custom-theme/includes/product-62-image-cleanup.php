<?php
defined('ABSPATH') || exit;

/**
 * Keep the five approved Ruwah products on the intended presentation:
 * current/new featured image as the main image, plus the verified original
 * gallery images. The featured image itself is never duplicated in gallery.
 *
 * No image binary is edited and no other product field is changed.
 */
function ruwah_enforce_new_only_product_galleries(): void {
    if (! function_exists('wc_get_product')) {
        return;
    }

    $gallery_map = [
        54 => [263, 265, 266],
        60 => [275, 277, 278],
        62 => [267, 268, 269],
        64 => [271, 273, 274],
        68 => [279, 281, 282],
    ];

    foreach ($gallery_map as $product_id => $gallery_ids) {
        $product = wc_get_product($product_id);
        if (! $product instanceof WC_Product) {
            continue;
        }

        $featured_id = (int) $product->get_image_id();
        $desired_gallery = array_values(array_filter(
            array_map('intval', $gallery_ids),
            static fn(int $attachment_id): bool => $attachment_id > 0 && $attachment_id !== $featured_id
        ));

        $current_gallery = array_map('intval', $product->get_gallery_image_ids());
        if ($current_gallery === $desired_gallery) {
            continue;
        }

        $product->set_gallery_image_ids($desired_gallery);
        $product->save();

        if (function_exists('wc_delete_product_transients')) {
            wc_delete_product_transients($product_id);
        }
    }
}

/** Compatibility entry point retained for the existing footer call. */
function ruwah_product_62_remove_baked_floor(): void {
    ruwah_enforce_new_only_product_galleries();
}
