<?php

namespace RRZE\BlockControl\Settings;

use RRZE\BlockControl\Blocks\BlockRegistry;

defined('ABSPATH') || exit;

/**
 * Displays an admin notice when new Gutenberg blocks have been registered
 * since the last review by an administrator.
 */
class AdminNotice
{
    /**
     * AdminNotice constructor.
     *
     * Receives the shared Settings instance and hooks the notice renderer
     * into WordPress so new blocks can be announced to admins.
     */
    public function __construct()
    {
        add_action('admin_notices', [$this, 'renderAdminNotice']);
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

        if (empty($newBlockSlugs)) {
            return;
        }

        $settingsUrl = admin_url('options-general.php?page=rrze-block-control');

        ?>
        <div class="notice notice-warning">
            <p>
                <strong>
                    <?php echo esc_html__('New blocks detected.', 'rrze-block-control'); ?>
                </strong><br>
                <?php
                echo esc_html__(
                        'New Gutenberg blocks have been registered since your last review. Please check the block permissions for each user role.',
                        'rrze-block-control'
                );
                ?>
            </p>
            <p>
                <a href="<?php echo esc_url($settingsUrl); ?>" class="button button-primary">
                    <?php echo esc_html__('Review settings', 'rrze-block-control'); ?>
                </a>
            </p>
        </div>
        <?php
    }
}