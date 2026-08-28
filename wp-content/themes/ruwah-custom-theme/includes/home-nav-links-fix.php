<?php
defined('ABSPATH') || exit;

/**
 * Correct only the standalone homepage navigation targets supplied by the
 * commerce plugin. Reuses existing content; creates no pages or forms.
 */
add_action('template_redirect', static function (): void {
    if (! is_front_page()) {
        return;
    }

    ob_start(static function (string $html): string {
        $learn_old   = esc_url(home_url('/beauty-guide/'));
        $learn_new   = esc_url(home_url('/#ingredient-guide'));
        $contact_old = esc_url(home_url('/contact-us/'));
        $contact_new = esc_url(home_url('/contact/'));

        return str_replace(
            [
                'href="' . $learn_old . '"',
                'href="' . $contact_old . '"',
            ],
            [
                'href="' . $learn_new . '"',
                'href="' . $contact_new . '"',
            ],
            $html
        );
    });
}, 2);
