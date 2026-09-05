<?php
defined('ABSPATH') || exit;

/**
 * Keep the five approved Ruwah products on the new-image-only presentation.
 *
 * The current featured image is deliberately preserved. Only legacy
 * WooCommerce gallery IDs are removed. This is self-healing: if an older
 * image plugin re-attaches gallery images later, the next uncached page
 * request clears them again without touching any other product field.
 */
function ruwah_enforce_new_only_product_galleries(): void {
    if (! function_exists('wc_get_product')) {
        return;
    }

    foreach ([54, 60, 62, 64, 68] as $product_id) {
        $product = wc_get_product($product_id);
        if (! $product instanceof WC_Product) {
            continue;
        }

        if (empty($product->get_gallery_image_ids())) {
            continue;
        }

        $product->set_gallery_image_ids([]);
        $product->save();

        if (function_exists('wc_delete_product_transients')) {
            wc_delete_product_transients($product_id);
        }
    }
}

/**
 * Compatibility entry point retained because footer.php already invokes this
 * helper. The former binary image cleanup is intentionally removed so the
 * newly approved image files remain byte-for-byte untouched.
 */
function ruwah_product_62_remove_baked_floor(): void {
    ruwah_enforce_new_only_product_galleries();
}
