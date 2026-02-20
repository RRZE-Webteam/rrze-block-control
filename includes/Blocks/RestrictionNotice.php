<?php

namespace RRZE\BlockControl\Blocks;

use RRZE\BlockControl\Settings\Settings;

defined('ABSPATH') || exit;


/**
 * Adds a Gutenberg editor notice
 * if blocks are restricted for the current user role.
 */
class RestrictionNotice
{
    protected Settings $settings;
    /**
     * Constructor.
     *
     * Hooks into block editor asset loading.
     */
    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
        add_action('enqueue_block_editor_assets', [$this, 'enqueueEditorNotice']);
    }


    /**
     * Enqueues the editor notice script
     * only if restrictions apply.
     *
     * @return void
     */
    public function enqueueEditorNotice(): void
    {
        if (current_user_can('manage_options')) {
            return;
        }

        if (!$this->hasRestrictedBlocks()) {
            return;
        }

        wp_enqueue_script(
            'rrze-bc-editor-notice',
            plugins_url(
                'assets/js/editor-notice.js',
                dirname(__DIR__, 2) . '/rrze-block-control.php'
            ),
            ['wp-data'],
            '1.0',
            true
        );

        wp_localize_script(
            'rrze-bc-editor-notice',
            'rrzeBlockControlNotice',
            [
                'message' => __('Some blocks are not available due to your user role.', 'rrze-block-control'),
            ]
        );
    }

    /**
     * Checks whether the current user role
     * has block restrictions configured.
     *
     * @return bool
     */
    protected function hasRestrictedBlocks(): bool
    {
        $user = wp_get_current_user();
        if (!$user || empty($user->roles)) {
            return false;
        }

        foreach ($user->roles as $role) {
            $restricted = $this->settings->getBlockSlugsForRole($role);
            if (!empty($restricted)) {
                return true;
            }
        }

        return false;
    }

}