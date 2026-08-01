<?php
/**
 * Plugin Name: Ruwah Fresh Commerce Design
 * Description: Reversible premium storefront design using the existing Ruwah Beauty WooCommerce catalog.
 * Version: 1.0.0
 * Author: Ruwah Beauty
 * Text Domain: ruwah-fresh-commerce-design
 * Requires at least: 6.5
 * Requires PHP: 7.4
 * WC requires at least: 8.0
 * WC tested up to: 10.9
 */

defined( 'ABSPATH' ) || exit;

final class Ruwah_Fresh_Commerce_Design {
    private const VERSION = '1.0.0';

    public static function init(): void {
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'assets' ), 99 );
        add_filter( 'body_class', array( __CLASS__, 'body_class' ) );
        add_filter( 'the_content', array( __CLASS__, 'front_page' ), 999 );
        add_filter( 'woocommerce_output_related_products_args', array( __CLASS__, 'related' ) );
    }

    public static function activate(): void {
        update_option( 'rfd_version', self::VERSION, false );
        flush_rewrite_rules();
    }

    public static function deactivate(): void {
        flush_rewrite_rules();
    }

    public static function body_class( array $classes ): array {
        if ( ! is_admin() ) {
            $classes[] = 'rfd-design';
        }
        if ( is_front_page() ) {
            $classes[] = 'rfd-front';
        }
        return $classes;
    }

    public static function assets(): void {
        if ( is_admin() ) {
            return;
        }
        wp_register_style( 'rfd-design', false, array(), self::VERSION );
        wp_enqueue_style( 'rfd-design' );
        wp_add_inline_style( 'rfd-design', self::css() );
        wp_register_script( 'rfd-design', '', array(), self::VERSION, true );
        wp_enqueue_script( 'rfd-design' );
        wp_add_inline_script( 'rfd-design', self::js() );
    }

    public static function related( array $args ): array {
        $args['posts_per_page'] = 4;
        $args['columns'] = 4;
        return $args;
    }

    public static function front_page( string $content ): string {
        if ( is_admin() || ! is_front_page() || ! in_the_loop() || ! is_main_query() ) {
            return $content;
        }
        if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'wc_get_products' ) ) {
            return $content;
        }
        return self::home_markup();
    }

    private static function home_markup(): string {
        $products = wc_get_products( array(
            'status' => 'publish',
            'limit' => 12,
            'orderby' => 'date',
            'order' => 'DESC',
            'return' => 'objects',
        ) );
        $popular = wc_get_products( array(
            'status' => 'publish',
            'limit' => 5,
            'orderby' => 'popularity',
            'order' => 'DESC',
            'return' => 'objects',
        ) );
        $categories = get_terms( array(
            'taxonomy' => 'product_cat',
            'hide_empty' => true,
            'number' => 6,
            'orderby' => 'count',
            'order' => 'DESC',
        ) );
        if ( is_wp_error( $categories ) ) {
            $categories = array();
        }

        $shop = wc_get_page_permalink( 'shop' );
        $cart = wc_get_cart_url();
        $account = wc_get_page_permalink( 'myaccount' );
        $featured = ! empty( $products ) ? $products[0] : null;
        $featured_image = $featured ? wp_get_attachment_image_url( $featured->get_image_id(), 'woocommerce_single' ) : '';

        ob_start();
        ?>
        <main class="rfd-home" aria-label="<?php esc_attr_e( 'Ruwah Beauty storefront', 'ruwah-fresh-commerce-design' ); ?>">
            <section class="rfd-hero rfd-reveal">
                <div class="rfd-word" aria-hidden="true">RUWAH</div>
                <div class="rfd-hero-copy">
                    <span class="rfd-kicker"><?php esc_html_e( 'THOUGHTFUL EVERYDAY SKINCARE', 'ruwah-fresh-commerce-design' ); ?></span>
                    <h1><?php esc_html_e( 'A calmer way to shop your beauty routine.', 'ruwah-fresh-commerce-design' ); ?></h1>
                    <p><?php esc_html_e( 'A completely new storefront experience built around the same Ruwah Beauty products, prices, images, stock and checkout you already use.', 'ruwah-fresh-commerce-design' ); ?></p>
                    <div class="rfd-actions">
                        <?php echo self::pill( $shop, __( 'Shop the collection', 'ruwah-fresh-commerce-design' ), 'dark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <a class="rfd-text-link" href="<?php echo esc_url( $account ); ?>"><?php esc_html_e( 'Your account', 'ruwah-fresh-commerce-design' ); ?> →</a>
                    </div>
                </div>
                <div class="rfd-visual">
                    <span class="rfd-ring rfd-ring-a"></span><span class="rfd-ring rfd-ring-b"></span>
                    <?php if ( $featured_image ) : ?>
                        <img src="<?php echo esc_url( $featured_image ); ?>" alt="<?php echo esc_attr( $featured->get_name() ); ?>" loading="eager" fetchpriority="high" decoding="async">
                    <?php else : ?>
                        <span class="rfd-placeholder" aria-hidden="true">RB</span>
                    <?php endif; ?>
                    <span class="rfd-badge"><?php esc_html_e( 'Beauty, delivered', 'ruwah-fresh-commerce-design' ); ?></span>
                </div>
                <?php if ( $featured ) : ?>
                    <a class="rfd-feature-card" href="<?php echo esc_url( $featured->get_permalink() ); ?>">
                        <small><?php esc_html_e( 'Featured ritual', 'ruwah-fresh-commerce-design' ); ?></small>
                        <strong><?php echo esc_html( $featured->get_name() ); ?></strong>
                        <span><?php echo wp_kses_post( $featured->get_price_html() ); ?></span>
                    </a>
                <?php endif; ?>
            </section>

            <?php if ( ! empty( $categories ) ) : ?>
                <section class="rfd-shell rfd-reveal">
                    <?php echo self::heading( __( 'Shop by routine', 'ruwah-fresh-commerce-design' ), $shop ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <div class="rfd-categories">
                        <?php foreach ( $categories as $index => $category ) :
                            $link = get_term_link( $category );
                            if ( is_wp_error( $link ) ) { continue; }
                            $image_id = (int) get_term_meta( $category->term_id, 'thumbnail_id', true );
                            $image = $image_id ? wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' ) : '';
                            ?>
                            <a class="rfd-category rfd-cat-<?php echo esc_attr( (string) ( $index % 6 ) ); ?>" href="<?php echo esc_url( $link ); ?>">
                                <span class="rfd-category-image">
                                    <?php if ( $image ) : ?><img src="<?php echo esc_url( $image ); ?>" alt="" loading="lazy" decoding="async"><?php else : ?><b aria-hidden="true"><?php echo esc_html( strtoupper( substr( $category->name, 0, 1 ) ) ); ?></b><?php endif; ?>
                                </span>
                                <strong><?php echo esc_html( $category->name ); ?></strong>
                                <small><?php echo esc_html( sprintf( _n( '%s product', '%s products', (int) $category->count, 'ruwah-fresh-commerce-design' ), number_format_i18n( (int) $category->count ) ) ); ?></small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <section class="rfd-shell rfd-reveal">
                <?php echo self::heading( __( 'Fresh for your routine', 'ruwah-fresh-commerce-design' ), $shop ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <?php echo self::grid( $products ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </section>

            <section class="rfd-banner rfd-reveal">
                <div>
                    <span class="rfd-kicker"><?php esc_html_e( 'SIMPLE. EFFECTIVE. YOURS.', 'ruwah-fresh-commerce-design' ); ?></span>
                    <h2><?php esc_html_e( 'Build a routine that feels easy to return to.', 'ruwah-fresh-commerce-design' ); ?></h2>
                    <p><?php esc_html_e( 'Explore cleansers, serums, moisturisers and targeted care from your existing Ruwah Beauty collection.', 'ruwah-fresh-commerce-design' ); ?></p>
                    <?php echo self::pill( $shop, __( 'Explore all products', 'ruwah-fresh-commerce-design' ), 'black' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
                <div class="rfd-product-stack" aria-hidden="true">
                    <?php foreach ( array_slice( $products, 1, 3 ) as $index => $product ) :
                        $image = wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_thumbnail' );
                        if ( $image ) : ?><img class="rfd-stack-<?php echo esc_attr( (string) $index ); ?>" src="<?php echo esc_url( $image ); ?>" alt="" loading="lazy" decoding="async"><?php endif;
                    endforeach; ?>
                </div>
            </section>

            <section class="rfd-promos rfd-reveal">
                <article class="rfd-promo rfd-mint"><span>01</span><h3><?php esc_html_e( 'Thoughtful essentials', 'ruwah-fresh-commerce-design' ); ?></h3><p><?php esc_html_e( 'A focused collection for repeatable daily skincare.', 'ruwah-fresh-commerce-design' ); ?></p></article>
                <article class="rfd-promo rfd-coral"><span>02</span><h3><?php esc_html_e( 'Secure checkout', 'ruwah-fresh-commerce-design' ); ?></h3><p><?php esc_html_e( 'The same trusted cart, payment and order flow remains in place.', 'ruwah-fresh-commerce-design' ); ?></p></article>
                <article class="rfd-promo rfd-yellow"><span>03</span><h3><?php esc_html_e( 'Delivered with care', 'ruwah-fresh-commerce-design' ); ?></h3><p><?php esc_html_e( 'A responsive shopping experience that feels calm on every screen.', 'ruwah-fresh-commerce-design' ); ?></p></article>
            </section>

            <?php if ( ! empty( $popular ) ) : ?>
                <section class="rfd-shell rfd-reveal">
                    <?php echo self::heading( __( 'Most-loved products', 'ruwah-fresh-commerce-design' ), $shop ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php echo self::grid( $popular, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </section>
            <?php endif; ?>

            <section class="rfd-cta rfd-reveal">
                <div><span class="rfd-kicker"><?php esc_html_e( 'YOUR ROUTINE, READY', 'ruwah-fresh-commerce-design' ); ?></span><h2><?php esc_html_e( 'Fill your beauty bag with products you already love.', 'ruwah-fresh-commerce-design' ); ?></h2><p><?php esc_html_e( 'The design is new. Your products and store operations remain unchanged.', 'ruwah-fresh-commerce-design' ); ?></p><div class="rfd-actions"><?php echo self::pill( $shop, __( 'Start shopping', 'ruwah-fresh-commerce-design' ), 'dark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a class="rfd-text-link" href="<?php echo esc_url( $cart ); ?>"><?php esc_html_e( 'View bag', 'ruwah-fresh-commerce-design' ); ?> →</a></div></div>
                <div class="rfd-mark" aria-hidden="true">R</div>
            </section>
        </main>
        <?php
        return (string) ob_get_clean();
    }

    private static function heading( string $title, string $url ): string {
        return '<div class="rfd-heading"><h2>' . esc_html( $title ) . '</h2>' . self::pill( $url, __( 'Show all', 'ruwah-fresh-commerce-design' ), 'black small' ) . '</div>';
    }

    private static function pill( string $url, string $label, string $type ): string {
        return '<a class="rfd-pill rfd-' . esc_attr( str_replace( ' ', ' rfd-', $type ) ) . '" href="' . esc_url( $url ) . '"><span>' . esc_html( $label ) . '</span><b aria-hidden="true">↗</b></a>';
    }

    private static function grid( array $products, bool $five = false ): string {
        if ( empty( $products ) ) {
            return '<div class="rfd-empty">' . esc_html__( 'Products will appear here when available.', 'ruwah-fresh-commerce-design' ) . '</div>';
        }
        ob_start();
        ?><div class="rfd-grid<?php echo $five ? ' rfd-five' : ''; ?>">
        <?php foreach ( $products as $product ) :
            if ( ! is_a( $product, 'WC_Product' ) ) { continue; }
            $image = wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_thumbnail' );
            $terms = get_the_terms( $product->get_id(), 'product_cat' );
            $category = is_array( $terms ) && ! empty( $terms ) ? $terms[0]->name : __( 'Ruwah Beauty', 'ruwah-fresh-commerce-design' );
            $classes = array( 'rfd-add', 'button', 'product_type_' . $product->get_type() );
            if ( $product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ) { $classes[] = 'add_to_cart_button'; $classes[] = 'ajax_add_to_cart'; }
            ?>
            <article class="rfd-product">
                <a class="rfd-image" href="<?php echo esc_url( $product->get_permalink() ); ?>">
                    <?php if ( $product->is_on_sale() ) : ?><span class="rfd-sale"><?php esc_html_e( 'Sale', 'ruwah-fresh-commerce-design' ); ?></span><?php endif; ?>
                    <?php if ( $image ) : ?><img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" loading="lazy" decoding="async"><?php else : ?><span class="rfd-placeholder" aria-hidden="true">RB</span><?php endif; ?>
                </a>
                <div class="rfd-product-body"><small><?php echo esc_html( $category ); ?></small><h3><a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3><div class="rfd-product-foot"><span class="rfd-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span><a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-quantity="1" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>" aria-label="<?php echo esc_attr( $product->add_to_cart_description() ); ?>" rel="nofollow"><span aria-hidden="true">+</span><span class="screen-reader-text"><?php echo esc_html( $product->add_to_cart_text() ); ?></span></a></div></div>
            </article>
        <?php endforeach; ?></div><?php
        return (string) ob_get_clean();
    }

    private static function css(): string {
        return <<<'CSS'
:root{--rfd-bg:#f2f3f1;--rfd-white:#fff;--rfd-ink:#101312;--rfd-muted:#6d746f;--rfd-line:#e8ebe7;--rfd-green:#1a6a59;--rfd-deep:#0b5144;--rfd-top:#08745e;--rfd-lime:#9ddb6e;--rfd-mint:#d7eee8;--rfd-soft:#eaf7f3;--rfd-blue:#ddf3ff;--rfd-coral:#ef6d7a;--rfd-yellow:#ffd94a;--rfd-shadow:0 18px 50px rgba(20,50,42,.08)}
body.rfd-design{background:var(--rfd-bg);color:var(--rfd-ink);font-family:Inter,Manrope,"Segoe UI",Arial,sans-serif}body.rfd-design *{box-sizing:border-box}body.rfd-design a{transition:.3s ease}body.rfd-front .entry-header,body.rfd-front .page-header{display:none!important}body.rfd-front .site-content,body.rfd-front .content-area,body.rfd-front .site-main,body.rfd-front article,body.rfd-front .entry-content,body.rfd-front .ast-container{width:100%!important;max-width:none!important;padding:0!important;margin:0!important}
.rfd-home{width:calc(100% - 40px);max-width:1440px;margin:22px auto 48px;display:grid;gap:22px}.rfd-hero,.rfd-shell,.rfd-banner,.rfd-cta{border-radius:30px;overflow:hidden;position:relative}.rfd-hero{min-height:690px;padding:clamp(34px,5vw,76px);background:linear-gradient(180deg,var(--rfd-top),var(--rfd-mint));display:grid;grid-template-columns:1fr 1.1fr;align-items:end;isolation:isolate}.rfd-word{position:absolute;z-index:-1;top:8%;left:50%;transform:translateX(-50%);font-family:Georgia,serif;font-size:clamp(88px,15vw,210px);line-height:.8;letter-spacing:-.08em;color:var(--rfd-lime);white-space:nowrap}.rfd-hero-copy{max-width:560px;z-index:3}.rfd-kicker{display:inline-block;font-size:12px;font-weight:900;letter-spacing:.17em;color:var(--rfd-deep);margin-bottom:18px}.rfd-hero h1,.rfd-banner h2,.rfd-cta h2{font-family:Georgia,serif;font-weight:500;letter-spacing:-.045em;line-height:.96;margin:0}.rfd-hero h1{font-size:clamp(48px,6.5vw,92px)}.rfd-hero p,.rfd-banner p,.rfd-cta p{font-size:16px;line-height:1.7;color:#31443e;margin:24px 0;max-width:580px}.rfd-actions{display:flex;align-items:center;gap:20px;flex-wrap:wrap}.rfd-pill{display:inline-flex;align-items:center;justify-content:space-between;gap:16px;min-height:52px;padding:6px 7px 6px 22px;border-radius:999px;text-decoration:none!important;font-size:14px;font-weight:900;color:#fff!important}.rfd-pill b{display:grid;place-items:center;width:40px;height:40px;border-radius:50%;background:#fff;color:#111}.rfd-dark{background:var(--rfd-deep)}.rfd-black{background:#0b0d0c}.rfd-small{min-height:44px;font-size:13px}.rfd-small b{width:34px;height:34px}.rfd-pill:hover{transform:translateY(-2px);box-shadow:0 12px 26px rgba(11,81,68,.18)}.rfd-text-link{font-size:14px;font-weight:900;color:var(--rfd-ink)!important;text-decoration:none!important}.rfd-visual{min-height:530px;position:relative;display:grid;place-items:end center}.rfd-visual>img{z-index:2;max-height:520px;max-width:92%;object-fit:contain;filter:drop-shadow(0 30px 34px rgba(11,81,68,.2));animation:rfdFloat 5s ease-in-out infinite}.rfd-ring{position:absolute;border-radius:50%;border:1px solid rgba(255,255,255,.55)}.rfd-ring-a{width:420px;height:420px;bottom:18px}.rfd-ring-b{width:310px;height:310px;bottom:74px;border-color:rgba(157,219,110,.9)}.rfd-badge{position:absolute;right:1%;top:16%;z-index:4;background:var(--rfd-lime);color:var(--rfd-deep);padding:13px 20px;border-radius:999px;font-size:13px;font-weight:900;transform:rotate(7deg);box-shadow:var(--rfd-shadow)}.rfd-feature-card{position:absolute;right:34px;bottom:30px;z-index:5;width:220px;padding:18px;border-radius:20px;background:rgba(221,243,255,.94);text-decoration:none!important;color:var(--rfd-ink)!important;box-shadow:var(--rfd-shadow);display:grid;gap:5px}.rfd-feature-card small{font-size:11px;font-weight:900;letter-spacing:.12em;text-transform:uppercase;color:var(--rfd-deep)}
.rfd-shell{background:#fff;padding:clamp(26px,4vw,54px);box-shadow:var(--rfd-shadow)}.rfd-heading{display:flex;align-items:center;justify-content:space-between;gap:18px;margin-bottom:30px}.rfd-heading h2{font-family:Georgia,serif;font-size:clamp(30px,3vw,46px);font-weight:500;letter-spacing:-.04em;margin:0}.rfd-categories{display:grid;grid-template-columns:repeat(6,1fr);gap:14px}.rfd-category{min-height:220px;border-radius:22px;padding:20px 14px;text-align:center;text-decoration:none!important;color:var(--rfd-ink)!important;display:flex;flex-direction:column;align-items:center;justify-content:flex-end;gap:5px}.rfd-cat-0{background:linear-gradient(155deg,#eaf7f3,#ccebe1)}.rfd-cat-1{background:linear-gradient(155deg,#fce8ec,#f8cfd5)}.rfd-cat-2{background:linear-gradient(155deg,#fff5d5,#ffe89b)}.rfd-cat-3{background:linear-gradient(155deg,#e9f4ff,#cde8ff)}.rfd-cat-4{background:linear-gradient(155deg,#f0eaff,#dbd1fa)}.rfd-cat-5{background:linear-gradient(155deg,#eef5e6,#d9eabf)}.rfd-category:hover{transform:translateY(-4px);box-shadow:0 16px 30px rgba(20,50,42,.09)}.rfd-category-image{width:126px;height:126px;display:grid;place-items:center;margin:auto auto 12px}.rfd-category-image img{max-width:100%;max-height:100%;object-fit:contain}.rfd-category-image b{font:66px Georgia,serif;color:rgba(11,81,68,.45)}.rfd-category small{font-size:12px;color:var(--rfd-muted)}
.rfd-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px}.rfd-five{grid-template-columns:repeat(5,1fr)}.rfd-product{border:1px solid var(--rfd-line);border-radius:20px;background:#fff;overflow:hidden;box-shadow:0 8px 22px rgba(20,50,42,.045);transition:.3s ease}.rfd-product:hover{transform:translateY(-4px);box-shadow:0 18px 32px rgba(20,50,42,.1)}.rfd-image{display:grid;place-items:center;position:relative;aspect-ratio:1;background:linear-gradient(180deg,#fbfcfb,#f1f5f2);overflow:hidden}.rfd-image img{width:84%;height:84%;object-fit:contain;transition:.35s ease}.rfd-product:hover .rfd-image img{transform:scale(1.035)}.rfd-sale{position:absolute;left:14px;top:14px;z-index:2;padding:7px 11px;border-radius:999px;background:var(--rfd-coral);color:#fff;font-size:11px;font-weight:900;text-transform:uppercase}.rfd-product-body{padding:18px}.rfd-product-body>small{font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.1em;color:var(--rfd-green)}.rfd-product h3{font-size:16px;line-height:1.35;margin:8px 0 18px;min-height:43px}.rfd-product h3 a{text-decoration:none!important;color:var(--rfd-ink)!important}.rfd-product-foot{display:flex;align-items:center;justify-content:space-between;gap:12px}.rfd-price{font-weight:900;font-size:15px}.rfd-price del{color:#929a95;font-weight:500;font-size:12px}.rfd-price ins{text-decoration:none}.rfd-add{width:42px;height:42px;min-height:42px!important;padding:0!important;border-radius:50%!important;background:var(--rfd-deep)!important;color:#fff!important;display:grid!important;place-items:center;text-decoration:none!important;font-size:22px!important}.rfd-placeholder{width:74%;height:74%;border-radius:50%;display:grid;place-items:center;background:var(--rfd-soft);color:var(--rfd-green);font:54px Georgia,serif}
.rfd-banner{min-height:430px;background:linear-gradient(125deg,var(--rfd-blue),#f6fcff);display:grid;grid-template-columns:1.1fr .9fr;align-items:center;padding:clamp(36px,5vw,72px);box-shadow:var(--rfd-shadow)}.rfd-banner h2,.rfd-cta h2{font-size:clamp(42px,5vw,74px)}.rfd-product-stack{height:320px;position:relative}.rfd-product-stack img{position:absolute;width:230px;height:230px;object-fit:contain;filter:drop-shadow(0 22px 24px rgba(20,50,42,.13));border-radius:50%;background:rgba(255,255,255,.58);padding:18px}.rfd-stack-0{left:5%;top:10%;transform:rotate(-9deg)}.rfd-stack-1{right:3%;top:2%;transform:rotate(9deg)}.rfd-stack-2{left:34%;bottom:-12%;transform:rotate(3deg)}.rfd-promos{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}.rfd-promo{min-height:310px;border-radius:30px;padding:32px;display:flex;flex-direction:column;justify-content:flex-end;position:relative;overflow:hidden}.rfd-mint{background:linear-gradient(145deg,#168472,#c9eadf)}.rfd-coral{background:linear-gradient(145deg,#ef6678,#f7c1c8)}.rfd-yellow{background:linear-gradient(145deg,#ffd83d,#fff0a5)}.rfd-promo>span{position:absolute;left:28px;top:25px;font:60px Georgia,serif;color:rgba(16,19,18,.22)}.rfd-promo h3{font:500 34px/1 Georgia,serif;letter-spacing:-.035em;margin:0 0 14px}.rfd-promo p{font-size:14px;line-height:1.6;margin:0;color:#253a34}.rfd-cta{background:linear-gradient(125deg,#cdeeff,#f6fcff);padding:clamp(40px,6vw,80px);display:grid;grid-template-columns:1.2fr .8fr;align-items:center;min-height:430px;box-shadow:var(--rfd-shadow)}.rfd-mark{justify-self:center;width:260px;height:260px;border-radius:50%;background:var(--rfd-deep);color:var(--rfd-lime);display:grid;place-items:center;font:170px Georgia,serif;box-shadow:0 30px 50px rgba(11,81,68,.2);transform:rotate(-8deg)}.rfd-empty{padding:60px;text-align:center;color:var(--rfd-muted);background:var(--rfd-soft);border-radius:20px}
body.rfd-design.woocommerce .site-main,body.rfd-design.woocommerce-page .site-main{background:#fff;border-radius:30px;padding:clamp(24px,4vw,54px);margin-top:24px;margin-bottom:40px;box-shadow:var(--rfd-shadow)}body.rfd-design.woocommerce ul.products,body.rfd-design.woocommerce-page ul.products{display:grid!important;grid-template-columns:repeat(4,1fr);gap:20px;margin:0!important}body.rfd-design.woocommerce ul.products:before,body.rfd-design.woocommerce ul.products:after{display:none!important}body.rfd-design.woocommerce ul.products li.product,body.rfd-design.woocommerce-page ul.products li.product{width:auto!important;margin:0!important;border:1px solid var(--rfd-line);border-radius:20px;padding:14px;background:#fff;box-shadow:0 8px 22px rgba(20,50,42,.045)}body.rfd-design.woocommerce ul.products li.product img{border-radius:15px;background:#f5f7f5;margin:0 0 16px!important}body.rfd-design.woocommerce ul.products li.product .price{color:var(--rfd-ink)!important;font-weight:900!important}body.rfd-design.woocommerce a.button,body.rfd-design.woocommerce button.button,body.rfd-design.woocommerce input.button,body.rfd-design .wc-block-components-button{border-radius:999px!important;background:var(--rfd-deep)!important;color:#fff!important;border:0!important;font-weight:900!important}body.rfd-design.woocommerce div.product{display:grid;grid-template-columns:1fr 1fr;gap:clamp(30px,5vw,70px)}body.rfd-design.woocommerce div.product div.images,body.rfd-design.woocommerce div.product div.summary{width:auto!important;float:none!important}body.rfd-design.woocommerce div.product div.images img{background:#f5f7f5;border-radius:24px}body.rfd-design.woocommerce div.product .product_title{font:500 clamp(42px,5vw,68px)/1 Georgia,serif;letter-spacing:-.04em}body.rfd-design table.shop_table{border:1px solid var(--rfd-line)!important;border-radius:20px!important;overflow:hidden;background:#fff}body.rfd-design .cart_totals,body.rfd-design #order_review{background:var(--rfd-soft);border-radius:22px;padding:24px}body.rfd-design input.input-text,body.rfd-design textarea,body.rfd-design select,body.rfd-design .select2-selection{border:1px solid var(--rfd-line)!important;border-radius:14px!important;background:#fff!important;min-height:48px;padding:10px 14px!important}
.rfd-reveal{opacity:0;transform:translateY(16px);transition:opacity .55s ease,transform .55s ease}.rfd-visible{opacity:1;transform:none}@keyframes rfdFloat{0%,100%{transform:translateY(0)}50%{transform:translateY(-7px)}}
@media(max-width:1100px){.rfd-categories{grid-template-columns:repeat(3,1fr)}.rfd-grid,.rfd-five{grid-template-columns:repeat(3,1fr)}body.rfd-design.woocommerce ul.products,body.rfd-design.woocommerce-page ul.products{grid-template-columns:repeat(3,1fr)}}
@media(max-width:820px){.rfd-home{width:calc(100% - 24px);margin-top:12px}.rfd-hero{grid-template-columns:1fr;min-height:820px;padding:34px 24px}.rfd-word{font-size:clamp(72px,21vw,150px)}.rfd-hero-copy{padding-top:80px}.rfd-visual{min-height:360px}.rfd-visual>img{max-height:350px}.rfd-feature-card{left:24px;right:auto;bottom:22px;width:calc(100% - 48px)}.rfd-categories{display:flex;overflow-x:auto;scroll-snap-type:x mandatory}.rfd-category{flex:0 0 220px;scroll-snap-align:start}.rfd-grid,.rfd-five{grid-template-columns:repeat(2,1fr)}.rfd-banner,.rfd-cta{grid-template-columns:1fr}.rfd-promos{grid-template-columns:1fr}.rfd-mark{margin-top:40px;width:210px;height:210px;font-size:138px}body.rfd-design.woocommerce ul.products,body.rfd-design.woocommerce-page ul.products{grid-template-columns:repeat(2,1fr)}body.rfd-design.woocommerce div.product{grid-template-columns:1fr}}
@media(max-width:520px){.rfd-home{gap:14px}.rfd-shell{padding:24px 16px}.rfd-heading .rfd-pill{width:44px;padding:5px}.rfd-heading .rfd-pill>span{display:none}.rfd-grid,.rfd-five{gap:10px}.rfd-product-body{padding:13px}.rfd-product h3{font-size:14px;min-height:57px}.rfd-price{font-size:13px}.rfd-banner,.rfd-cta{padding:32px 20px}.rfd-promo{min-height:260px;padding:25px}body.rfd-design.woocommerce ul.products,body.rfd-design.woocommerce-page ul.products{gap:10px}}
@media(prefers-reduced-motion:reduce){.rfd-reveal{opacity:1;transform:none;transition:none}.rfd-visual>img{animation:none}body.rfd-design *{transition-duration:.01ms!important;animation-duration:.01ms!important}}
CSS;
    }

    private static function js(): string {
        return <<<'JS'
(function(){'use strict';var n=document.querySelectorAll('.rfd-reveal');if(!n.length)return;if(!('IntersectionObserver'in window)){n.forEach(function(e){e.classList.add('rfd-visible')});return}var o=new IntersectionObserver(function(e){e.forEach(function(e){if(e.isIntersecting){e.target.classList.add('rfd-visible');o.unobserve(e.target)}})},{threshold:.08,rootMargin:'0px 0px -30px 0px'});n.forEach(function(e){o.observe(e)})})();
JS;
    }
}

register_activation_hook( __FILE__, array( 'Ruwah_Fresh_Commerce_Design', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Ruwah_Fresh_Commerce_Design', 'deactivate' ) );
Ruwah_Fresh_Commerce_Design::init();
