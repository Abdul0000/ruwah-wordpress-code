<?php
/**
 * Dedicated Ruwah Beauty Privacy Policy template.
 */
defined('ABSPATH') || exit;

$privacy_css = get_template_directory() . '/assets/privacy-policy.css';
wp_enqueue_style(
    'rwb-privacy-policy',
    get_template_directory_uri() . '/assets/privacy-policy.css',
    [],
    is_readable($privacy_css) ? (string) filemtime($privacy_css) : (defined('RUWAH_THEME_VERSION') ? RUWAH_THEME_VERSION : null)
);

get_header();
?>
<main class="rwb-policy-page" id="main-content">
    <section class="rwb-policy-hero">
        <div class="rwb-policy-shell rwb-policy-shell--wide">
            <p class="rwb-policy-kicker">RUWAH BEAUTY · PRIVACY</p>
            <h1>Privacy Policy</h1>
        </div>
    </section>

    <section class="rwb-policy-content">
        <div class="rwb-policy-shell rwb-policy-shell--wide">
            <article class="rwb-privacy-policy">
                <header class="rwb-policy-intro">
                    <p class="rwb-policy-meta">Effective 20 August 2026 <span aria-hidden="true">·</span> Last updated 20 August 2026</p>
                    <p class="rwb-policy-lead">Ruwah Beauty respects your privacy. This policy explains what information may be collected when you browse our store, create an account, contact us or place an order, why it is used, when it may be shared, and the choices available to you.</p>
                    <p>By using this website, you acknowledge the practices described below. This policy should be read together with the notices shown during checkout and our Refund Policy.</p>
                </header>

                <nav class="rwb-policy-index" aria-label="Privacy policy sections">
                    <a href="#policy-collect">Information we collect</a>
                    <a href="#policy-use">How we use it</a>
                    <a href="#policy-cookies">Cookies</a>
                    <a href="#policy-sharing">Sharing</a>
                    <a href="#policy-rights">Your choices</a>
                    <a href="#policy-contact">Contact</a>
                </nav>

                <section class="rwb-policy-section" id="policy-about"><span class="rwb-policy-number">01</span><div><h2>About this policy</h2><p>This policy applies to personal information handled through ruwahbeauty.com and the online-store functions operated for Ruwah Beauty. It does not control the privacy practices of independent websites or services that we do not operate.</p></div></section>

                <section class="rwb-policy-section" id="policy-collect"><span class="rwb-policy-number">02</span><div><h2>Information we collect</h2><p>The information we collect depends on how you use the store. It may include:</p><ul><li><strong>Identity and contact information</strong> such as your name, email address and phone number.</li><li><strong>Order and delivery information</strong> such as billing and shipping addresses, products ordered, quantities and order status.</li><li><strong>Account information</strong> when you create or use a customer account.</li><li><strong>Transaction information</strong> such as order value, currency, payment status and information returned by the payment method used at checkout.</li><li><strong>Communications</strong> including messages, support requests, reviews or other information you choose to send us.</li><li><strong>Technical and usage information</strong> such as IP address, browser/device information, session activity, security logs and cookie identifiers generated when you use the website.</li></ul><p>Please provide accurate information at checkout so we can process and deliver your order correctly.</p></div></section>

                <section class="rwb-policy-section" id="policy-use"><span class="rwb-policy-number">03</span><div><h2>How we use your information</h2><p>We use personal information when reasonably necessary to operate the store and serve customers, including to:</p><ul><li>process, confirm, fulfil and support orders;</li><li>deliver purchases and communicate delivery or order updates;</li><li>operate customer accounts and respond to enquiries;</li><li>process payments, refunds and transaction status updates;</li><li>protect the website, customers and our business against fraud, abuse and security incidents;</li><li>maintain records required for accounting, dispute handling and legal obligations;</li><li>improve website usability, products and customer service; and</li><li>send marketing communications where requested or otherwise permitted, with an option to unsubscribe.</li></ul></div></section>

                <section class="rwb-policy-section" id="policy-cookies"><span class="rwb-policy-number">04</span><div><h2>Cookies and similar technologies</h2><p>Our website uses cookies and similar browser technologies to support core functions such as keeping items in your cart, maintaining a checkout session, remembering preferences, account login and protecting the site from misuse.</p><p>Analytics or marketing technologies may also be used where configured or permitted. You can control cookies through your browser settings, but blocking essential cookies may prevent parts of the store, cart, account or checkout from working correctly.</p></div></section>

                <section class="rwb-policy-section" id="policy-sharing"><span class="rwb-policy-number">05</span><div><h2>When information may be shared</h2><p>We do not publish your personal information for advertising by unrelated third parties. We may share information only where reasonably necessary with service providers that help us operate the store, such as:</p><ul><li>website hosting, ecommerce and technical service providers;</li><li>payment and transaction-processing providers;</li><li>couriers, delivery and logistics providers;</li><li>email, customer-support, security or fraud-prevention services where used;</li><li>professional advisers where required; and</li><li>government, regulatory or law-enforcement authorities when disclosure is required by applicable law or valid legal process.</li></ul></div></section>

                <section class="rwb-policy-section" id="policy-payments"><span class="rwb-policy-number">06</span><div><h2>Checkout and payments</h2><p>When you place an order, payment-related information may be transmitted to the payment method or payment service used at checkout so the transaction can be authorised, completed, refunded or verified. Information processed by an independent payment provider is also governed by that provider’s privacy and security terms.</p><p>We may retain transaction references, payment status and order records needed to administer your purchase, handle disputes and meet accounting or legal requirements.</p></div></section>

                <section class="rwb-policy-section" id="policy-retention"><span class="rwb-policy-number">07</span><div><h2>How long we keep information</h2><p>We keep personal information only for as long as reasonably necessary for the purpose for which it was collected, including order fulfilment, customer support, account administration, fraud prevention, dispute handling, accounting and applicable legal requirements.</p><p>Retention periods can differ by type of information. When information is no longer reasonably required, we may delete, anonymise or securely archive it as appropriate.</p></div></section>

                <section class="rwb-policy-section" id="policy-rights"><span class="rwb-policy-number">08</span><div><h2>Your choices and privacy rights</h2><p>Subject to applicable law and legal exceptions, you may ask us to help you access, correct or delete personal information associated with you. You may also ask questions about how your information is used or withdraw from marketing communications.</p><p>Some records cannot be deleted immediately where we need to retain them for completed orders, accounting, fraud prevention, dispute resolution, security or legal obligations. We may need to verify your identity before acting on a privacy request.</p></div></section>

                <section class="rwb-policy-section" id="policy-security"><span class="rwb-policy-number">09</span><div><h2>Security</h2><p>We use reasonable administrative and technical measures intended to protect information handled through the website. No internet transmission, website or storage system can be guaranteed to be completely secure, so customers should also protect account credentials and avoid sharing passwords or verification codes.</p></div></section>

                <section class="rwb-policy-section" id="policy-children"><span class="rwb-policy-number">10</span><div><h2>Children’s privacy</h2><p>Our online store is intended for customers able to make purchases under applicable law. We do not knowingly seek to collect personal information from children in circumstances where parental or guardian consent would be legally required.</p></div></section>

                <section class="rwb-policy-section" id="policy-international"><span class="rwb-policy-number">11</span><div><h2>Service providers and international processing</h2><p>Some technology or service providers used to operate an online store may process information from locations outside your country. Where this occurs, information may be subject to the laws of the location in which it is processed. We select and use providers according to operational needs and take reasonable steps to handle customer information appropriately.</p></div></section>

                <section class="rwb-policy-section" id="policy-updates"><span class="rwb-policy-number">12</span><div><h2>Changes to this policy</h2><p>We may update this policy when our services, technology or legal obligations change. The current version will be posted on this page with a revised “Last updated” date. Material changes may also be communicated through the website when appropriate.</p></div></section>

                <section class="rwb-policy-section rwb-policy-contact" id="policy-contact"><span class="rwb-policy-number">13</span><div><h2>Contact us about privacy</h2><p>If you have a privacy question or would like to make a request regarding your personal information, please use our <a href="<?php echo esc_url(home_url('/contact/')); ?>">Contact Us</a> page and include enough information for us to identify and respond to your request.</p><p><strong>Ruwah Beauty</strong><br>Pakistan · Online skincare</p></div></section>
            </article>
        </div>
    </section>
</main>
<?php get_footer(); ?>
