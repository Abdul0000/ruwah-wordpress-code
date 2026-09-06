<?php
/**
 * Dedicated Ruwah Beauty Refund Policy template.
 */
defined('ABSPATH') || exit;
get_header();
$policy_date = '6 September 2026';
$contact_url = function_exists('ruwah_page_url') ? ruwah_page_url('contact') : home_url('/contact/');
?>
<style id="rwb-refund-policy-inline">
.rwb-refund-page{width:100%;overflow:hidden;background:#f7f3e9;color:#111}
.rwb-refund-page *{box-sizing:border-box}
.rwb-refund-page .rwb-refund-shell{width:100%;max-width:none;margin:0;padding-left:1in;padding-right:1in}
.rwb-refund-hero{padding:62px 0 44px;border-bottom:1px solid rgba(17,17,17,.18);background:#f7f3e9}
.rwb-refund-kicker{margin:0 0 24px;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:12px;line-height:1;letter-spacing:.07em;text-transform:uppercase}
.rwb-refund-hero h1{max-width:900px;margin:0;color:#111;font-family:var(--serif,Georgia,serif);font-size:clamp(58px,6vw,94px);font-weight:400;line-height:.9;letter-spacing:-.045em}
.rwb-refund-content{padding:58px 0 96px;background:#f7f3e9}
.rwb-refund-policy{width:100%;max-width:980px;margin:0 auto;color:#111;font-family:var(--sans,Arial,sans-serif);font-size:17px;line-height:1.62}
.rwb-refund-intro{padding-bottom:40px;border-bottom:1px solid rgba(17,17,17,.28)}
.rwb-refund-meta{display:flex;flex-wrap:wrap;gap:10px 28px;margin:0 0 30px;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:12px;letter-spacing:.055em;text-transform:uppercase}
.rwb-refund-lead{max-width:900px;margin:0;font-family:var(--serif,Georgia,serif);font-size:clamp(31px,3.2vw,46px);font-weight:400;line-height:1.05;letter-spacing:-.025em}
.rwb-refund-summary{max-width:820px;margin:26px 0 0;color:#4d4a45;font-size:16px}
.rwb-refund-index{display:flex;flex-wrap:wrap;gap:10px 24px;margin-top:30px}
.rwb-refund-index a{padding-bottom:2px;border-bottom:1px solid rgba(17,17,17,.55);color:#111;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:11px;letter-spacing:.045em;text-transform:uppercase}
.rwb-refund-section{display:grid;grid-template-columns:74px minmax(0,1fr);gap:24px;padding:38px 0;border-bottom:1px solid rgba(17,17,17,.22);scroll-margin-top:110px}
.rwb-refund-number{padding-top:6px;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:12px;letter-spacing:.08em}
.rwb-refund-section h2{margin:0 0 16px;color:#111;font-family:var(--serif,Georgia,serif);font-size:clamp(30px,3vw,42px);font-weight:400;line-height:1;letter-spacing:-.025em}
.rwb-refund-section p{margin:0 0 14px;color:#34322f}
.rwb-refund-section p:last-child{margin-bottom:0}
.rwb-refund-section ul{margin:14px 0 0;padding:0;list-style:none}
.rwb-refund-section li{position:relative;margin:9px 0;padding-left:20px;color:#34322f}
.rwb-refund-section li:before{content:'—';position:absolute;left:0;top:0}
.rwb-refund-note{margin-top:18px;padding:16px 18px;border-left:2px solid #111;background:rgba(255,255,255,.34);font-size:14px}
.rwb-refund-contact{margin-top:54px;padding:38px 40px;background:#111;color:#fff}
.rwb-refund-contact .rwb-refund-number{color:#c8c4bc}
.rwb-refund-contact h2{margin:0 0 14px;color:#fff;font-family:var(--serif,Georgia,serif);font-size:clamp(32px,3.4vw,46px);font-weight:400;line-height:1}
.rwb-refund-contact p{max-width:720px;margin:0;color:#d5d2cb}
.rwb-refund-contact a{display:inline-flex;align-items:center;min-height:44px;margin-top:24px;padding:0 18px;border:1px solid #fff;color:#fff;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:11px;letter-spacing:.05em;text-transform:uppercase}
.rwb-refund-contact a:hover{background:#fff;color:#111}
@media(max-width:1100px){.rwb-refund-page .rwb-refund-shell{padding-left:48px;padding-right:48px}}
@media(max-width:720px){.rwb-refund-page .rwb-refund-shell{padding-left:24px;padding-right:24px}.rwb-refund-hero{padding:42px 0 34px}.rwb-refund-content{padding:40px 0 70px}.rwb-refund-policy{font-size:15px}.rwb-refund-section{grid-template-columns:1fr;gap:9px;padding:30px 0}.rwb-refund-number{padding-top:0}.rwb-refund-contact{padding:30px 24px}}
</style>
<section class="rwb-refund-page" aria-labelledby="rwb-refund-title">
    <header class="rwb-refund-hero">
        <div class="rwb-refund-shell">
            <p class="rwb-refund-kicker">RUWAH BEAUTY · RETURNS &amp; REFUNDS</p>
            <h1 id="rwb-refund-title">Refund Policy</h1>
        </div>
    </header>
    <div class="rwb-refund-content">
        <div class="rwb-refund-shell">
            <article class="rwb-refund-policy">
                <div class="rwb-refund-intro">
                    <div class="rwb-refund-meta"><span>Effective <?php echo esc_html($policy_date); ?></span><span>Last updated <?php echo esc_html($policy_date); ?></span></div>
                    <p class="rwb-refund-lead">We want every Ruwah Beauty order to arrive correctly and in suitable condition.</p>
                    <p class="rwb-refund-summary">This policy explains how we handle return, exchange and refund requests for online orders. Because skincare is a personal-use product category, eligibility can depend on product condition, hygiene considerations and the reason for the request.</p>
                    <nav class="rwb-refund-index" aria-label="Refund policy sections"><a href="#refund-eligibility">Eligibility</a><a href="#refund-damaged">Damaged orders</a><a href="#refund-returns">Returns</a><a href="#refund-refunds">Refunds</a><a href="#refund-cancellations">Cancellations</a><a href="#refund-contact">Contact</a></nav>
                </div>

                <section class="rwb-refund-section" id="refund-eligibility"><div class="rwb-refund-number">01</div><div><h2>Return eligibility</h2><p>If you would like to request a return, contact us before sending anything back. We will review the order, reason for return and product condition and will provide return instructions when the request is eligible.</p><ul><li>Products should normally be unused, unopened and in their original saleable packaging for a change-of-mind return to be considered.</li><li>Proof of purchase or sufficient order details may be required so we can locate and verify the transaction.</li><li>Items returned without prior approval may not be identifiable or processable.</li></ul></div></section>

                <section class="rwb-refund-section" id="refund-damaged"><div class="rwb-refund-number">02</div><div><h2>Damaged, defective or incorrect items</h2><p>If an item arrives damaged, appears defective, or is different from what you ordered, please contact us promptly after delivery. Include your order details and clear photos of the product, packaging and shipping label where relevant.</p><p>After review, we may offer an appropriate resolution such as a replacement, exchange, store remedy or refund, depending on the circumstances and product availability.</p></div></section>

                <section class="rwb-refund-section" id="refund-returns"><div class="rwb-refund-number">03</div><div><h2>Skincare hygiene exclusions</h2><p>For hygiene and safety reasons, opened or used skincare products are generally not eligible for a change-of-mind return. This does not prevent you from contacting us about an item that is damaged, defective, incorrect or otherwise has a genuine product issue.</p><div class="rwb-refund-note">Please do not discard a product or its packaging while a damage or defect request is being reviewed, unless we tell you it is safe to do so.</div></div></section>

                <section class="rwb-refund-section"><div class="rwb-refund-number">04</div><div><h2>How to start a return</h2><p>Contact Ruwah Beauty with your order number, the item involved and the reason for your request. If a return is approved, we will provide the applicable return address or collection instructions and explain any packaging requirements.</p><p>Return shipping responsibility can vary according to the reason for return. We will confirm the applicable arrangement before you send the item.</p></div></section>

                <section class="rwb-refund-section" id="refund-refunds"><div class="rwb-refund-number">05</div><div><h2>Refunds</h2><p>Approved refunds are processed after the relevant return or claim has been reviewed. Where supported, the refund will normally be directed to the original payment method or another method agreed with you.</p><p>For Cash on Delivery orders, there is no card payment to reverse. If a Cash on Delivery refund is approved, Ruwah support will confirm the refund method and any recipient details needed before processing.</p><p>Once we have issued a refund, the time required for the amount to appear can depend on the payment provider or receiving service and is outside our direct control.</p></div></section>

                <section class="rwb-refund-section"><div class="rwb-refund-number">06</div><div><h2>Delivery and return charges</h2><p>Original delivery charges and return-shipping costs are considered according to the reason for the return. If the issue resulted from an incorrect, damaged or defective item, we will advise you of the appropriate resolution. For discretionary or change-of-mind requests, delivery-related charges may not be refundable.</p></div></section>

                <section class="rwb-refund-section" id="refund-cancellations"><div class="rwb-refund-number">07</div><div><h2>Order cancellations</h2><p>If you need to cancel an order, contact us as soon as possible. We will try to accommodate the request if fulfilment has not progressed too far. Once an order has been packed, handed to a courier or dispatched, cancellation may no longer be possible and the return process may apply instead.</p></div></section>

                <section class="rwb-refund-section"><div class="rwb-refund-number">08</div><div><h2>Exchanges</h2><p>Where an exchange is appropriate, it is subject to product availability. If the requested replacement is unavailable, we may discuss another suitable resolution with you. Please do not place a duplicate order solely to create an exchange unless you prefer to make a separate purchase.</p></div></section>

                <section class="rwb-refund-section"><div class="rwb-refund-number">09</div><div><h2>Sale, promotional and bundled items</h2><p>Promotional pricing does not remove your ability to contact us about a damaged, defective or incorrect item. Eligibility for discretionary returns of sale, promotional or bundled products may depend on the specific offer and whether all relevant items are returned in suitable condition.</p></div></section>

                <section class="rwb-refund-section"><div class="rwb-refund-number">10</div><div><h2>Fair-use and legal rights</h2><p>We may decline requests that appear fraudulent, abusive, materially incomplete or inconsistent with the returned item. Nothing in this policy is intended to remove any rights or remedies that cannot lawfully be excluded under applicable consumer law.</p></div></section>

                <section class="rwb-refund-contact" id="refund-contact"><div class="rwb-refund-number">11</div><h2>Need help with an order?</h2><p>Send us your order number, the product name and a short description of the issue. For damaged, incorrect or defective items, adding clear photos will usually help us review the request more efficiently.</p><a href="<?php echo esc_url($contact_url); ?>">Contact Ruwah Beauty</a></section>
            </article>
        </div>
    </div>
</section>
<?php get_footer(); ?>