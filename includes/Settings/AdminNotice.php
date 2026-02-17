<?php

namespace RRZE\BlockControl\Settings;

use RRZE\BlockControl\Blocks\BlockRegistry;

defined('ABSPATH') || exit;

/**
 * Displays an admin notice when new Gutenberg blocks
 * have been registered since the last snapshot.
 */
class AdminNotice
{
    /**
     * Constructor.
     *
     * Hooks the notice renderer and dismiss handler
     * into the WordPress admin lifecycle.
     */
    public function __construct()
    {
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

        $registry = new BlockRegistry();

        $newBlockSlugs = $registry->getNewBlockSlugs();
        $newBlockDetails = $registry->getBlockDetailsForSlugs($newBlockSlugs);

        // Wenn keine neuen Blöcke → keine Notice
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
                    <?php esc_html_e('New blocks detected.', 'rrze-block-control'); ?>
                </strong>
            </p>

            <p>
                <?php esc_html_e(
                        'The following new blocks have been registered. Please review your block restrictions if necessary.',
                        'rrze-block-control'
                ); ?>
            </p>

            <ul class="rrze-block-control-new-blocks">
                <?php foreach ($newBlockDetails as $block) : ?>
                    <li>
                        <?php
                        $categoryLabel = ucwords(str_replace('-', ' ', $block['category']));
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

        $registry = new BlockRegistry();

        // Snapshot aktualisieren
        $registry->markNewBlocksAsSeen();

        wp_safe_redirect(remove_query_arg(['rrze_block_control_dismiss', '_wpnonce']));
        exit;
    }
}
