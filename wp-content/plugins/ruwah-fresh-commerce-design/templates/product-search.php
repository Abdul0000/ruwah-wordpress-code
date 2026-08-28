<?php
defined('ABSPATH') || exit;
get_header();
?>
<main id="main-content" class="rhp-search-results" aria-labelledby="rhp-search-title">
    <section class="rhp-section">
        <header class="rhp-section-head">
            <div><p class="rhp-eyebrow">Product search</p><h1 id="rhp-search-title">Search results for “<?php echo esc_html(get_search_query()); ?>”</h1></div>
            <p>Current Ruwah products matching your search, with live price and availability.</p>
        </header>
        <?php if (have_posts()) : ?>
            <div class="rhp-product-grid">
                <?php $rank = 0; while (have_posts()) : the_post(); $candidate = function_exists('wc_get_product') ? wc_get_product(get_the_ID()) : null; if (! $candidate instanceof WC_Product || ! $candidate->is_visible()) continue; if (function_exists('rwb_render_master_product_card')) rwb_render_master_product_card($candidate, $rank++); endwhile; ?>
            </div>
        <?php else : ?>
            <p>No matching products found. <a href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/')); ?>">Shop all skincare</a>.</p>
        <?php endif; ?>
    </section>
</main>
<?php get_footer();
