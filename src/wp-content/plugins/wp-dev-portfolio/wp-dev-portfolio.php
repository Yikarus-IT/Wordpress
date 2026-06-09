<?php
/**
 * Plugin Name: WP Dev Portfolio
 * Description: Adds a small developer profile admin page and a frontend shortcode.
 * Version: 1.0.0
 * Author: Local WordPress Lab
 * Text Domain: wp-dev-portfolio
 */

if (! defined('ABSPATH')) {
    exit;
}

const WDP_OPTION_NAME = 'wdp_profile';

add_action('admin_menu', 'wdp_register_admin_page');
add_action('admin_init', 'wdp_register_settings');
add_action('wp_enqueue_scripts', 'wdp_register_assets');
add_shortcode('dev_profile', 'wdp_render_profile_shortcode');

function wdp_register_admin_page(): void
{
    add_menu_page(
        'Developer Profile',
        'Dev Profile',
        'manage_options',
        'wp-dev-portfolio',
        'wdp_render_admin_page',
        'dashicons-id',
        26
    );
}

function wdp_register_settings(): void
{
    register_setting(
        'wdp_profile_settings',
        WDP_OPTION_NAME,
        [
            'type' => 'array',
            'sanitize_callback' => 'wdp_sanitize_profile',
            'default' => wdp_default_profile(),
        ]
    );

    add_settings_section(
        'wdp_profile_section',
        'Profile Details',
        function (): void {
            echo '<p>These values can be displayed on the public site with the <code>[dev_profile]</code> shortcode.</p>';
        },
        'wp-dev-portfolio'
    );

    $fields = [
        'name' => 'Name',
        'role' => 'Role',
        'summary' => 'Summary',
        'skills' => 'Skills',
        'cta_url' => 'Call-to-action URL',
    ];

    foreach ($fields as $key => $label) {
        add_settings_field(
            'wdp_' . $key,
            $label,
            'wdp_render_field',
            'wp-dev-portfolio',
            'wdp_profile_section',
            [
                'key' => $key,
                'label' => $label,
            ]
        );
    }
}

function wdp_render_admin_page(): void
{
    if (! current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1>Developer Profile</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('wdp_profile_settings');
            do_settings_sections('wp-dev-portfolio');
            submit_button('Save Profile');
            ?>
        </form>
    </div>
    <?php
}

function wdp_render_field(array $args): void
{
    $profile = wdp_get_profile();
    $key = $args['key'];
    $value = $profile[$key] ?? '';
    $name = WDP_OPTION_NAME . '[' . esc_attr($key) . ']';

    if ($key === 'summary') {
        printf(
            '<textarea name="%1$s" rows="5" class="large-text">%2$s</textarea>',
            esc_attr($name),
            esc_textarea($value)
        );
        return;
    }

    printf(
        '<input type="text" name="%1$s" value="%2$s" class="regular-text" />',
        esc_attr($name),
        esc_attr($value)
    );
}

function wdp_render_profile_shortcode(): string
{
    $profile = wdp_get_profile();

    wp_enqueue_style('wdp-profile');

    ob_start();
    ?>
    <section class="wdp-profile-card">
        <p class="wdp-profile-card__eyebrow">Developer Profile</p>
        <h2><?php echo esc_html($profile['name']); ?></h2>
        <p class="wdp-profile-card__role"><?php echo esc_html($profile['role']); ?></p>
        <p><?php echo esc_html($profile['summary']); ?></p>
        <p class="wdp-profile-card__skills"><?php echo esc_html($profile['skills']); ?></p>
        <?php if (! empty($profile['cta_url'])) : ?>
            <a href="<?php echo esc_url($profile['cta_url']); ?>">View work</a>
        <?php endif; ?>
    </section>
    <?php

    return ob_get_clean();
}

function wdp_register_assets(): void
{
    wp_register_style(
        'wdp-profile',
        plugin_dir_url(__FILE__) . 'assets/profile.css',
        [],
        '1.0.0'
    );
}

function wdp_get_profile(): array
{
    $profile = get_option(WDP_OPTION_NAME, []);

    return wp_parse_args(is_array($profile) ? $profile : [], wdp_default_profile());
}

function wdp_default_profile(): array
{
    return [
        'name' => 'Your Name',
        'role' => 'WordPress Developer',
        'summary' => 'I build custom WordPress features with plugins, themes, hooks, and clean admin workflows.',
        'skills' => 'PHP, WordPress hooks, custom plugins, themes, REST API',
        'cta_url' => home_url('/'),
    ];
}

function wdp_sanitize_profile(mixed $input): array
{
    $input = is_array($input) ? $input : [];

    return [
        'name' => sanitize_text_field($input['name'] ?? ''),
        'role' => sanitize_text_field($input['role'] ?? ''),
        'summary' => sanitize_textarea_field($input['summary'] ?? ''),
        'skills' => sanitize_text_field($input['skills'] ?? ''),
        'cta_url' => esc_url_raw($input['cta_url'] ?? ''),
    ];
}
