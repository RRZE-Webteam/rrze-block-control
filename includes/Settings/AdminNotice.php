<?php

namespace RRZE\BlockControl\Settings;

use RRZE\BlockControl\Blocks\BlockRegistry;
use RRZE\BlockControl\Helper;

defined('ABSPATH') || exit;

/**
 * Displays an admin notice when new Gutenberg blocks
 * have been registered since the last snapshot.
 */
class AdminNotice
{
    protected BlockRegistry $registry;

    /**
     * Constructor.
     *
     * Hooks the notice renderer and dismiss handler
     * into the WordPress admin lifecycle.
     */
    public function __construct(BlockRegistry $registry)
    {
        $this->registry = $registry;
        add_action('admin_notices', [$this, 'renderAdminNotice']);
        add_action('admin_init', [$this, 'handleDismiss']);
    }

    /**
     * Renders the admin notice if new blocks are detected.
     *
     * @return void
     */
    public function renderAdminNotice(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $screen = get_current_screen();

        if (!$screen) {
            return;
        }

        $allowedScreens = [
                'dashboard',
                'settings_page_rrze-block-control',
        ];

        if (!in_array($screen->id, $allowedScreens, true)) {
            return;
        }

        $registry = $this->registry;

        $newBlockSlugs = $registry->getNewBlockSlugs();
        $newBlockDetails = $registry->getBlockDetailsForSlugs($newBlockSlugs);

        if (empty($newBlockDetails)) {
            return;
        }

        $dismissUrl = wp_nonce_url(
                add_query_arg('rrze_block_control_dismiss', 1),
                'rrze_block_control_dismiss'
        );

        $settingsUrl = admin_url('options-general.php?page=rrze-block-control');
        ?>

        <div class="notice notice-warning bc-admin-notice">
            <p>
                <strong>
                    <?php esc_html_e('RRZE Block Control: New blocks available!', 'rrze-block-control'); ?>
                </strong>
            </p>

            <p>
                <?php esc_html_e(
                        'New blocks are available on this website. Check the block settings for user roles.',
                        'rrze-block-control');
                ?>
            </p>

            <ul class="rrze-block-control-new-blocks">
                <?php foreach ($newBlockDetails as $block) : ?>
                    <li>
                        <?php
                        $categoryLabel = Helper::getCategoryLabel($block['category']);
                        printf(
                                '%s / %s',
                                esc_html($categoryLabel),
                                esc_html($block['title'])
                        );
                        ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <p>
                <a href="<?php echo esc_url($settingsUrl); ?>" class="button button-primary">
                    <?php esc_html_e('Review settings', 'rrze-block-control'); ?>
                </a>

                <a href="<?php echo esc_url($dismissUrl); ?>" class="button">
                    <?php esc_html_e('Confirm', 'rrze-block-control'); ?>
                </a>
            </p>
        </div>

        <?php
    }

    /**
     * Handles manual dismissal of the notice.
     *
     * When dismissed, the current block list is stored
     * as the new snapshot.
     *
     * @return void
     */
    public function handleDismiss(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (!isset($_GET['rrze_block_control_dismiss'])) {
            return;
        }

        if (!isset($_GET['_wpnonce']) ||
                !wp_verify_nonce(wp_unslash($_GET['_wpnonce']), 'rrze_block_control_dismiss')
        ) {
            return;
        }

        $registry = $this->registry;

        // Snapshot Update
        $registry->markNewBlocksAsSeen();

        wp_safe_redirect(remove_query_arg(['rrze_block_control_dismiss', '_wpnonce']));
        exit;
    }
}
