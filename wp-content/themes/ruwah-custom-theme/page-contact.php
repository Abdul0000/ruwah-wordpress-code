<?php
/**
 * Dedicated Ruwah Beauty Contact page.
 */
defined('ABSPATH') || exit;

$contact_page_url = get_permalink();
$contact_state = isset($_GET['contact']) ? sanitize_key(wp_unslash($_GET['contact'])) : '';

if ('POST' === strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? '')) && isset($_POST['rwb_contact_submit'])) {
    $redirect = $contact_page_url ?: home_url('/contact/');

    if (! isset($_POST['rwb_contact_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['rwb_contact_nonce'])), 'rwb_contact_request')) {
        wp_safe_redirect(add_query_arg('contact', 'invalid', $redirect));
        exit;
    }

    $honeypot = isset($_POST['company_website']) ? trim((string) wp_unslash($_POST['company_website'])) : '';
    if ('' !== $honeypot) {
        wp_safe_redirect(add_query_arg('contact', 'success', $redirect));
        exit;
    }

    $name = isset($_POST['contact_name']) ? sanitize_text_field(wp_unslash($_POST['contact_name'])) : '';
    $email = isset($_POST['contact_email']) ? sanitize_email(wp_unslash($_POST['contact_email'])) : '';
    $phone = isset($_POST['contact_phone']) ? sanitize_text_field(wp_unslash($_POST['contact_phone'])) : '';
    $order = isset($_POST['contact_order']) ? sanitize_text_field(wp_unslash($_POST['contact_order'])) : '';
    $topic = isset($_POST['contact_topic']) ? sanitize_text_field(wp_unslash($_POST['contact_topic'])) : 'General enquiry';
    $message = isset($_POST['contact_message']) ? sanitize_textarea_field(wp_unslash($_POST['contact_message'])) : '';

    $name_length = function_exists('mb_strlen') ? mb_strlen($name) : strlen($name);
    $message_length = function_exists('mb_strlen') ? mb_strlen($message) : strlen($message);
    $allowed_topics = ['Order support', 'Product question', 'Return or refund', 'Delivery question', 'Account help', 'General enquiry'];

    $valid = $name_length >= 2 && $name_length <= 80
        && is_email($email)
        && $message_length >= 10 && $message_length <= 3000
        && in_array($topic, $allowed_topics, true);

    if ($phone && ! preg_match('/^\+?[0-9() .-]{7,24}$/', $phone)) {
        $valid = false;
    }

    if (! $valid) {
        wp_safe_redirect(add_query_arg('contact', 'invalid', $redirect));
        exit;
    }

    $recipient = sanitize_email((string) get_option('admin_email'));
    $subject = sprintf('Ruwah Beauty contact: %s', $topic);
    $body = "New contact request from ruwahbeauty.com\n\n";
    $body .= "Name: {$name}\nEmail: {$email}\n";
    if ($phone) $body .= "Phone: {$phone}\n";
    if ($order) $body .= "Order number: {$order}\n";
    $body .= "Topic: {$topic}\n\nMessage:\n{$message}\n";
    $headers = ['Reply-To: ' . $name . ' <' . $email . '>'];

    $sent = $recipient && wp_mail($recipient, $subject, $body, $headers);
    wp_safe_redirect(add_query_arg('contact', $sent ? 'success' : 'error', $redirect));
    exit;
}

get_header();
?>
<style id="rwb-contact-page-inline">
.rwb-contact-page{width:100%;overflow:hidden;background:#f7f3e9;color:#111}.rwb-contact-page *{box-sizing:border-box}
.rwb-contact-page .rwb-contact-shell{width:100%;max-width:none;margin:0;padding-left:1in;padding-right:1in}
.rwb-contact-hero{padding:62px 0 44px;border-bottom:1px solid rgba(17,17,17,.18);background:#f7f3e9}
.rwb-contact-kicker{margin:0 0 24px;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:12px;line-height:1;letter-spacing:.07em;text-transform:uppercase}
.rwb-contact-hero h1{max-width:900px;margin:0;color:#111;font-family:var(--serif,Georgia,serif);font-size:clamp(58px,6vw,94px);font-weight:400;line-height:.9;letter-spacing:-.045em}
.rwb-contact-content{padding:58px 0 96px;background:#f7f3e9}
.rwb-contact-inner{width:100%;max-width:980px;margin:0 auto;font-family:var(--sans,Arial,sans-serif);font-size:17px;line-height:1.62}
.rwb-contact-intro{padding-bottom:40px;border-bottom:1px solid rgba(17,17,17,.28)}
.rwb-contact-meta{margin:0 0 28px;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:12px;line-height:1.4;letter-spacing:.055em;text-transform:uppercase}
.rwb-contact-lead{max-width:900px;margin:0!important;font-family:var(--serif,Georgia,serif);font-size:clamp(31px,3.2vw,46px);font-weight:400;line-height:1.05;letter-spacing:-.025em}
.rwb-contact-summary{max-width:820px;margin:24px 0 0;color:#494641;font-size:16px}
.rwb-contact-options{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));margin:34px 0 0;border-top:1px solid rgba(17,17,17,.22);border-bottom:1px solid rgba(17,17,17,.22)}
.rwb-contact-option{padding:24px 22px 24px 0}.rwb-contact-option+.rwb-contact-option{padding-left:22px;border-left:1px solid rgba(17,17,17,.22)}
.rwb-contact-option span{display:block;margin-bottom:10px;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:10px;letter-spacing:.07em}.rwb-contact-option strong{display:block;font-family:var(--serif,Georgia,serif);font-size:25px;font-weight:400;line-height:1.05}.rwb-contact-option p{margin:9px 0 0;color:#55514c;font-size:13px;line-height:1.5}
.rwb-contact-section{display:grid;grid-template-columns:74px minmax(0,1fr);gap:24px;padding:42px 0;border-bottom:1px solid rgba(17,17,17,.22)}
.rwb-contact-number{padding-top:5px;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:11px;letter-spacing:.08em}
.rwb-contact-section h2{margin:0 0 18px;color:#111;font-family:var(--serif,Georgia,serif);font-size:clamp(32px,3vw,47px);font-weight:400;line-height:1;letter-spacing:-.03em}
.rwb-contact-section p{margin:0 0 15px;color:#34322f}.rwb-contact-section p:last-child{margin-bottom:0}
.rwb-contact-form-wrap{margin-top:4px}.rwb-contact-notice{margin:0 0 22px;padding:14px 16px;border:1px solid #111;font-size:14px}.rwb-contact-notice.success{background:#e6efe9}.rwb-contact-notice.error{background:#f5e6e5}
.rwb-contact-form{display:grid;grid-template-columns:1fr 1fr;gap:18px 16px}.rwb-contact-field{display:grid;gap:7px}.rwb-contact-field.full{grid-column:1/-1}.rwb-contact-field label{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:10px;letter-spacing:.055em;text-transform:uppercase}
.rwb-contact-field input,.rwb-contact-field select,.rwb-contact-field textarea{width:100%;min-height:48px;padding:10px 12px!important;border:1px solid rgba(17,17,17,.35)!important;border-radius:0!important;background:#fbf9f2!important;color:#111!important;box-shadow:none!important;outline:0}.rwb-contact-field textarea{min-height:160px;resize:vertical}.rwb-contact-field input:focus,.rwb-contact-field select:focus,.rwb-contact-field textarea:focus{border-color:#111!important}
.rwb-contact-submit{grid-column:1/-1;display:flex;align-items:center;justify-content:space-between;gap:20px;margin-top:4px}.rwb-contact-submit small{max-width:560px;color:#625e58;font-size:11px}.rwb-contact-submit button{min-width:180px;min-height:48px;padding:0 20px;border:1px solid #111;border-radius:0;background:#111;color:#fff;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:11px;letter-spacing:.05em;text-transform:uppercase}.rwb-contact-submit button:hover{background:transparent;color:#111}
.rwb-contact-hp{position:absolute!important;left:-9999px!important;width:1px!important;height:1px!important;overflow:hidden!important}
.rwb-contact-dark{margin-top:54px;padding:38px 40px;background:#111;color:#fff}.rwb-contact-dark h2{margin:0 0 14px;color:#fff;font-family:var(--serif,Georgia,serif);font-size:clamp(32px,3.4vw,46px);font-weight:400;line-height:1}.rwb-contact-dark p{max-width:740px;margin:0;color:#d5d2cb}.rwb-contact-dark a{color:#fff;border-bottom:1px solid currentColor}
@media(max-width:1100px){.rwb-contact-page .rwb-contact-shell{padding-left:48px;padding-right:48px}}
@media(max-width:720px){.rwb-contact-page .rwb-contact-shell{padding-left:24px;padding-right:24px}.rwb-contact-hero{padding:42px 0 34px}.rwb-contact-content{padding:40px 0 70px}.rwb-contact-inner{font-size:15px}.rwb-contact-options{grid-template-columns:1fr}.rwb-contact-option,.rwb-contact-option+.rwb-contact-option{padding:18px 0;border-left:0;border-top:1px solid rgba(17,17,17,.16)}.rwb-contact-option:first-child{border-top:0}.rwb-contact-section{grid-template-columns:1fr;gap:9px;padding:30px 0}.rwb-contact-number{padding-top:0}.rwb-contact-form{grid-template-columns:1fr}.rwb-contact-field.full,.rwb-contact-submit{grid-column:1}.rwb-contact-submit{align-items:stretch;flex-direction:column}.rwb-contact-submit button{width:100%}.rwb-contact-dark{padding:30px 24px}}
</style>
<section class="rwb-contact-page" aria-labelledby="rwb-contact-title">
    <header class="rwb-contact-hero"><div class="rwb-contact-shell"><p class="rwb-contact-kicker">RUWAH BEAUTY · CONTACT</p><h1 id="rwb-contact-title">Contact Us</h1></div></header>
    <div class="rwb-contact-content"><div class="rwb-contact-shell"><article class="rwb-contact-inner">
        <header class="rwb-contact-intro">
            <p class="rwb-contact-meta">Customer care · Pakistan · Online skincare</p>
            <p class="rwb-contact-lead">Tell us what you need help with and we’ll route your message with the right context.</p>
            <p class="rwb-contact-summary">For order-related questions, include your order number where available. For damaged or incorrect items, describe the issue clearly; our Refund Policy explains the information that may be useful when a claim is reviewed.</p>
            <div class="rwb-contact-options"><div class="rwb-contact-option"><span>01</span><strong>Order support</strong><p>Delivery, order status, changes and checkout questions.</p></div><div class="rwb-contact-option"><span>02</span><strong>Product questions</strong><p>Formula, size, routine and product-information enquiries.</p></div><div class="rwb-contact-option"><span>03</span><strong>Returns &amp; refunds</strong><p>Damaged, incorrect, return or refund requests.</p></div></div>
        </header>

        <section class="rwb-contact-section"><div class="rwb-contact-number">01</div><div><h2>Before you send</h2><p>Include enough detail for us to understand the request without asking you to repeat basic information. For an existing order, the order number and the email used at checkout are especially helpful.</p><p>Please never send card numbers, passwords, one-time verification codes or other sensitive authentication information through this form.</p></div></section>

        <section class="rwb-contact-section" id="contact-form"><div class="rwb-contact-number">02</div><div><h2>Send Ruwah a message</h2><div class="rwb-contact-form-wrap">
            <?php if ('success' === $contact_state) : ?><div class="rwb-contact-notice success" role="status">Thank you. Your message has been sent to Ruwah Beauty.</div><?php elseif ('invalid' === $contact_state) : ?><div class="rwb-contact-notice error" role="alert">Please check the required fields and try again.</div><?php elseif ('error' === $contact_state) : ?><div class="rwb-contact-notice error" role="alert">We could not send your message right now. Please try again.</div><?php endif; ?>
            <form class="rwb-contact-form" method="post" action="<?php echo esc_url($contact_page_url ?: home_url('/contact/')); ?>">
                <?php wp_nonce_field('rwb_contact_request', 'rwb_contact_nonce'); ?>
                <div class="rwb-contact-hp" aria-hidden="true"><label>Website<input type="text" name="company_website" tabindex="-1" autocomplete="off"></label></div>
                <div class="rwb-contact-field"><label for="rwb-contact-name">Name *</label><input id="rwb-contact-name" name="contact_name" type="text" required minlength="2" maxlength="80" autocomplete="name"></div>
                <div class="rwb-contact-field"><label for="rwb-contact-email">Email *</label><input id="rwb-contact-email" name="contact_email" type="email" required maxlength="160" autocomplete="email"></div>
                <div class="rwb-contact-field"><label for="rwb-contact-phone">Phone</label><input id="rwb-contact-phone" name="contact_phone" type="tel" maxlength="24" inputmode="tel" placeholder="+92 300 1234567" autocomplete="tel"></div>
                <div class="rwb-contact-field"><label for="rwb-contact-order">Order number</label><input id="rwb-contact-order" name="contact_order" type="text" maxlength="80" autocomplete="off"></div>
                <div class="rwb-contact-field full"><label for="rwb-contact-topic">What can we help with? *</label><select id="rwb-contact-topic" name="contact_topic" required><option value="General enquiry">General enquiry</option><option value="Order support">Order support</option><option value="Product question">Product question</option><option value="Return or refund">Return or refund</option><option value="Delivery question">Delivery question</option><option value="Account help">Account help</option></select></div>
                <div class="rwb-contact-field full"><label for="rwb-contact-message">Message *</label><textarea id="rwb-contact-message" name="contact_message" required minlength="10" maxlength="3000" placeholder="Tell us how we can help."></textarea></div>
                <div class="rwb-contact-submit"><small>By sending this form, you provide the information needed to respond to your enquiry. See our <a href="<?php echo esc_url(get_privacy_policy_url()); ?>">Privacy Policy</a> for information about how customer data is handled.</small><button type="submit" name="rwb_contact_submit" value="1">Send Message</button></div>
            </form>
        </div></div></section>

        <section class="rwb-contact-section"><div class="rwb-contact-number">03</div><div><h2>Order and return enquiries</h2><p>If your message concerns a damaged, defective or incorrect item, keep the product and packaging while the request is reviewed. Clear photos and your order number can help us understand what happened.</p><p>For eligibility, cancellation, exchange and refund information, please read our <a href="<?php echo esc_url(home_url('/refund-policy/')); ?>">Refund Policy</a>.</p></div></section>

        <section class="rwb-contact-dark"><div class="rwb-contact-number">04</div><h2>Ruwah Beauty customer care</h2><p>Ruwah Beauty is an online skincare store serving customers in Pakistan. Use the form above for order support, product questions, delivery enquiries, account assistance, returns and refund requests.</p></section>
    </article></div></div>
</section>
<?php get_footer(); ?>
