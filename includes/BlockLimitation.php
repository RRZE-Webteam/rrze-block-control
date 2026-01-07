<?php

namespace RRZE\BlockControl;
defined('ABSPATH' || exit);

use Exception;
use WP_Block_Type_Registry;

/**
 * Handles the Registration, Localization and Rendering of the Baseline Blocks
 */
class BlockLimitation
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'register_settings_page']);
        add_action( 'admin_enqueue_scripts', [$this, 'enqueue_settings_page_scripts'] );
        add_action('init', function () {
            if ( ! is_admin() || (defined('WP_DEBUG') && ! WP_DEBUG) ) {
                return;
            }
            $this->my_log_registered_blocks('init-999');
        }, 999);

//        add_action('init', function () {
//            if ( ! defined('WP_DEBUG') || ! WP_DEBUG ) {
//                return;
//            }
//
//            $blocks = $this->my_get_all_registered_block_names();
//
//            error_log(
//                'Registered blocks: ' . wp_json_encode(
//                    $blocks,
//                    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
//                )
//            );
//        });
    }

    public function my_log_registered_blocks(string $label): void {
        $types  = WP_Block_Type_Registry::get_instance()->get_all_registered();
        $names  = array_keys($types);

        error_log(sprintf(
            '[blocks][%s] count=%d %s',
            $label,
            count($names),
            wp_json_encode($names, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        ));
    }

    public function my_get_all_registered_block_names(): array {
        $registry = WP_Block_Type_Registry::get_instance();
        $types    = $registry->get_all_registered(); // array: name => WP_Block_Type

        return array_keys($types);
    }

    public function register_settings_page(): void
    {
        add_options_page(
            __('Unadorned Announcement Bar', 'unadorned-announcement-bar'),
            __('Unadorned Announcement Bar', 'unadorned-announcement-bar'),
            'manage_options',
            'unadorned-announcement-bar',
            [$this, 'render_settings_page']
        );
    }

    public function render_settings_page(): void
    {
        printf(
            '<div class="wrap" id="unadorned-announcement-bar-settings">%s</div>',
            esc_html__('Loading…', 'unadorned-announcement-bar')
        );
    }

    public function enqueue_settings_page_scripts( $admin_page ): void
    {
        if ( 'settings_page_unadorned-announcement-bar' !== $admin_page ) {
            return;
        }

        $asset_file = plugin_dir_path( __DIR__ ) . 'build/index.asset.php';

        if ( ! file_exists( $asset_file ) ) {
            return;
        }

        $asset = include $asset_file;

        $plugin_root = dirname(__DIR__);
        $base_url    = plugins_url('', $plugin_root . '/rrze-block-control.php');
        $script_url  = $base_url . '/build/index.js';

        wp_enqueue_script(
            'unadorned-announcement-bar-script',
            $script_url,
            $asset['dependencies'],
            $asset['version'],
            array('in_footer' => true)
        );

    }
}