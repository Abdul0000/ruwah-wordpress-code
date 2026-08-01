<?php
defined('ABSPATH') || exit;

/**
 * Persist RUWA's design tokens into Astra's native Customizer option.
 * This is intentionally idempotent and runs once per design-system version.
 */
function ruwa_sync_astra_design_system(): void {
    $version = '31.1.0';
    if ((string) get_option('ruwa_astra_design_system_version', '') === $version) {
        return;
    }

    $settings = get_option('astra-settings', []);
    if (!is_array($settings)) {
        $settings = [];
    }

    $settings['global-color-palette'] = [
        'palette' => [
            '#1F3A30',
            '#16281F',
            '#3D342B',
            '#8C7F70',
            '#F7F0E6',
            '#FCF8F2',
            '#E8DECF',
            '#B8935B',
            '#A8462E',
        ],
        'flag' => false,
    ];

    $settings['theme-color'] = '#1F3A30';
    $settings['link-color'] = '#1F3A30';
    $settings['link-h-color'] = '#16281F';
    $settings['text-color'] = '#3D342B';
    $settings['heading-base-color'] = '#1F3A30';
    $settings['border-color'] = '#E8DECF';

    $settings['body-font-family'] = "'Inter', sans-serif";
    $settings['body-font-weight'] = '400';
    $settings['body-font-variant'] = '400';
    $settings['headings-font-family'] = "'Fraunces', serif";
    $settings['headings-font-weight'] = '600';
    $settings['headings-font-variant'] = '600';

    $settings['button-font-family'] = "'Inter', sans-serif";
    $settings['button-font-weight'] = '700';
    $settings['button-font-variant'] = '700';
    $settings['button-color'] = '#FFFFFF';
    $settings['button-h-color'] = '#FFFFFF';
    $settings['button-bg-color'] = '#1F3A30';
    $settings['button-bg-h-color'] = '#16281F';
    $settings['button-border-color'] = '#B8935B';
    $settings['button-border-h-color'] = '#B8935B';
    $settings['button-radius'] = 999;

    $settings['secondary-button-color'] = '#1F3A30';
    $settings['secondary-button-h-color'] = '#FFFFFF';
    $settings['secondary-button-bg-color'] = '#FCF8F2';
    $settings['secondary-button-bg-h-color'] = '#16281F';
    $settings['secondary-button-border-color'] = '#1F3A30';
    $settings['secondary-button-border-h-color'] = '#B8935B';

    $settings['site-layout-outside-bg-color'] = '#F7F0E6';
    $settings['content-bg-color'] = '#F7F0E6';

    update_option('astra-settings', $settings, true);
    update_option('ruwa_astra_design_system_version', $version, true);

    delete_transient('astra_dynamic_css');
    delete_transient('astra-addon-dynamic-css');
}
