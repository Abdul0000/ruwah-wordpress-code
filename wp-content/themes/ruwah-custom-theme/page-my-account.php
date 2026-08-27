<?php
defined('ABSPATH') || exit;

get_header();
?>
<style id="rwb-my-account-inline">
body.woocommerce-account{background:#f7f3e9;color:#111}
body.woocommerce-account main#main-content{background:#f7f3e9}
.rwb-account-page{width:100%;padding:54px 0 82px;background:#f7f3e9}
.rwb-account-shell{width:calc(100% - 64px);max-width:1180px;margin:0 auto}
.rwb-account-kicker{margin:0 0 10px;color:#876cad;font-size:9px;font-weight:700;letter-spacing:.14em;text-transform:uppercase}
.rwb-account-title{margin:0 0 34px;font-family:var(--serif,'DM Serif Display',Georgia,serif);font-size:clamp(44px,5vw,66px);font-weight:400;line-height:.96;letter-spacing:-.03em}
.rwb-account-content{width:100%}
.rwb-account-content>.woocommerce{width:100%}
.rwb-account-content .woocommerce-notices-wrapper{max-width:760px;margin:0 auto 18px}
.rwb-account-content #customer_login{display:grid;grid-template-columns:minmax(0,620px);justify-content:center;margin:0!important}
.rwb-account-content #customer_login>.u-column1,.rwb-account-content #customer_login>.u-column2{float:none!important;width:100%!important;max-width:none!important;margin:0!important}
.rwb-account-content #customer_login>.u-column2{margin-top:22px!important}
.rwb-account-content #customer_login h2{margin:0 0 14px!important;font-family:var(--serif,'DM Serif Display',Georgia,serif)!important;font-size:30px!important;font-weight:400!important;line-height:1.05!important}
.rwb-account-content form.woocommerce-form-login,.rwb-account-content form.woocommerce-form-register{margin:0!important;padding:28px 30px!important;border:1px solid #d8cedb!important;border-radius:0!important;background:#fffdfa!important;box-shadow:none!important}
.rwb-account-content form .form-row{margin:0 0 16px!important;padding:0!important}
.rwb-account-content form .form-row label{display:block;margin:0 0 7px;color:#302a30;font-size:12px;font-weight:600;line-height:1.35}
.rwb-account-content form .form-row input.input-text{width:100%!important;min-height:52px!important;padding:0 14px!important;border:1px solid #cfc5d2!important;border-radius:0!important;background:#fff!important;color:#111!important;box-shadow:none!important;font-size:13px!important}
.rwb-account-content form .form-row input.input-text:focus{outline:0!important;border-color:#876cad!important;box-shadow:0 0 0 2px rgba(135,108,173,.10)!important}
.rwb-account-content .password-input{display:block;width:100%}
.rwb-account-content .show-password-input{top:50%!important;right:13px!important;transform:translateY(-50%)!important}
.rwb-account-content form .form-row:has(.woocommerce-form-login__submit){display:flex!important;align-items:center!important;gap:14px!important;flex-wrap:wrap!important;margin-top:5px!important}
.rwb-account-content .woocommerce-form-login__submit,.rwb-account-content .woocommerce-form-register__submit{min-width:122px!important;min-height:46px!important;margin:0!important;padding:0 22px!important;border:1px solid #111!important;border-radius:0!important;background:#111!important;color:#fff!important;font-size:11px!important;font-weight:700!important;letter-spacing:.04em!important;text-transform:uppercase!important}
.rwb-account-content .woocommerce-form-login__rememberme{display:inline-flex!important;align-items:center!important;gap:8px!important;margin:0!important;font-size:12px!important;font-weight:400!important}
.rwb-account-content .woocommerce-form-login__rememberme input{width:16px;height:16px;margin:0!important;accent-color:#876cad}
.rwb-account-content .woocommerce-LostPassword{margin:16px 0 0!important;font-size:12px!important}
.rwb-account-content .woocommerce-LostPassword a{color:#5f4a70;text-decoration:underline;text-underline-offset:3px}
.rwb-account-content .woocommerce-MyAccount-navigation{float:none!important;width:100%!important;margin:0 0 22px!important;padding:0!important;border:1px solid #d8cedb!important;background:#fffdfa!important}
.rwb-account-content .woocommerce-MyAccount-navigation ul{display:flex;flex-wrap:wrap;margin:0!important;padding:0!important;list-style:none!important}
.rwb-account-content .woocommerce-MyAccount-navigation li{margin:0!important;border-right:1px solid #e1d9e3!important;border-bottom:1px solid #e1d9e3!important}
.rwb-account-content .woocommerce-MyAccount-navigation a{display:block;padding:13px 16px;font-size:11px;font-weight:600}
.rwb-account-content .woocommerce-MyAccount-navigation .is-active a{background:#876cad;color:#fff}
.rwb-account-content .woocommerce-MyAccount-content{float:none!important;width:100%!important;margin:0!important;padding:28px 30px!important;border:1px solid #d8cedb!important;background:#fffdfa!important;min-height:220px}
.rwb-account-content .woocommerce-MyAccount-content p:first-child{margin-top:0}
@media(max-width:900px){.rwb-account-page{padding:42px 0 64px}.rwb-account-shell{width:calc(100% - 40px)}.rwb-account-title{margin-bottom:28px}.rwb-account-content #customer_login{grid-template-columns:minmax(0,680px)}}
@media(max-width:560px){.rwb-account-page{padding:30px 0 46px}.rwb-account-shell{width:calc(100% - 24px)}.rwb-account-title{font-size:40px;margin-bottom:22px}.rwb-account-content #customer_login h2{font-size:25px!important}.rwb-account-content form.woocommerce-form-login,.rwb-account-content form.woocommerce-form-register{padding:20px 16px!important}.rwb-account-content form .form-row:has(.woocommerce-form-login__submit){align-items:flex-start!important;flex-direction:column!important;gap:12px!important}.rwb-account-content .woocommerce-form-login__submit,.rwb-account-content .woocommerce-form-register__submit{width:100%!important}.rwb-account-content .woocommerce-MyAccount-navigation ul{display:block}.rwb-account-content .woocommerce-MyAccount-navigation li{border-right:0!important}.rwb-account-content .woocommerce-MyAccount-content{padding:20px 16px!important}}
</style>
<section class="rwb-account-page" aria-labelledby="rwb-account-title">
    <div class="rwb-account-shell">
        <p class="rwb-account-kicker">RUWAH BEAUTY</p>
        <h1 id="rwb-account-title" class="rwb-account-title"><?php the_title(); ?></h1>
        <div class="rwb-account-content">
            <?php while (have_posts()) : the_post(); the_content(); endwhile; ?>
        </div>
    </div>
</section>
<?php get_footer();
