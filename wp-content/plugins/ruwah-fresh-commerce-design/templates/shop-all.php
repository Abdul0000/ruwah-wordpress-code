<?php
defined('ABSPATH') || exit;

$products = function_exists('rwb_products') ? array_values(array_filter(rwb_products(), static fn($item) => $item instanceof WC_Product && $item->is_visible())) : [];
$priority = [
    'triple action serum' => 10,
    'rice whitening cream' => 20,
    'rice glow serum' => 30,
    'rice brightening face wash' => 40,
    'rice glow sun lotion' => 50,
];
usort($products, static function (WC_Product $a, WC_Product $b) use ($priority): int {
    $ak = strtolower(trim($a->get_name()));
    $bk = strtolower(trim($b->get_name()));
    return ($priority[$ak] ?? 999) <=> ($priority[$bk] ?? 999);
});
$products = array_slice($products, 0, 5);
$editorial_product = $products[0] ?? null;
$editorial_id = 0;
if ($editorial_product) {
    $editorial_ids = Ruwah_Fresh_Commerce_Design::gallery_ids($editorial_product);
    $editorial_id = (int) (end($editorial_ids) ?: $editorial_product->get_image_id());
}

$plugin_file = dirname(__DIR__) . '/ruwah-fresh-commerce-design.php';
wp_enqueue_style(
    'ruwah-reference-card-parity-shop',
    plugins_url('assets/card-parity.css', $plugin_file),
    [],
    '6.5.0'
);

get_header();
?>
<main id="main-content" class="rwb-dieux-shop" aria-labelledby="rwb-shop-all-title">
    <header class="rwb-dieux-shop-head">
        <h1 id="rwb-shop-all-title">SHOP ALL</h1>
        <nav class="rwb-dieux-shop-nav" aria-label="Shop categories">
            <a href="#skincare">Skincare</a>
            <a href="#serums">Serums</a>
            <a href="#brightening">Brightening</a>
            <a href="#cleanse">Cleanse</a>
            <a href="#sun-care">Sun Care</a>
        </nav>
    </header>

    <?php woocommerce_output_all_notices(); ?>

    <section id="skincare" class="rwb-dieux-catalog" aria-label="Ruwah skincare catalogue">
        <article class="rwb-dieux-category-tile">
            <?php if ($editorial_id) : ?>
                <?php echo wp_kses_post(wp_get_attachment_image($editorial_id, 'large', false, ['loading' => 'eager', 'decoding' => 'async'])); ?>
            <?php endif; ?>
            <span>Skincare</span>
        </article>

        <?php foreach ($products as $index => $product) :
            $name_key = strtolower($product->get_name());
            $anchor = '';
            if (str_contains($name_key, 'triple action')) $anchor = 'serums';
            elseif (str_contains($name_key, 'whitening cream')) $anchor = 'brightening';
            elseif (str_contains($name_key, 'face wash')) $anchor = 'cleanse';
            elseif (str_contains($name_key, 'sun lotion')) $anchor = 'sun-care';
            ?>
            <article class="rwb-dieux-product-card rwb-commerce-card"<?php if ($anchor) : ?> id="<?php echo esc_attr($anchor); ?>"<?php endif; ?>>
                <?php Ruwah_Fresh_Commerce_Design::render_card($product, (int) $index); ?>
            </article>
        <?php endforeach; ?>
    </section>
</main>
<?php get_footer();
