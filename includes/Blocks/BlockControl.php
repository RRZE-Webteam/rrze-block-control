<?php

namespace RRZE\BlockControl\Blocks;

use RRZE\BlockControl\Settings\Settings;
use RRZE\BlockControl\Blocks\BlockRegistry;
use RRZE\BlockControl\Helper;

defined('ABSPATH') || exit;


/**
 * Restricts available Gutenberg blocks in the editor based on the current user's role.
 *
 * This class hooks into the WordPress filter `allowed_block_types_all` and returns
 * a whitelist of allowed block slugs for the detected role.
 *
 * Notes:
 * - This only affects which blocks can be inserted in the editor.
 * - It does NOT remove blocks already stored in post content.
 * - Admins are typically excluded to avoid locking yourself out.
 */
class BlockControl
{

    protected Settings $settings;
    protected BlockRegistry $registry;

    public function __construct(Settings $settings, BlockRegistry $registry)
    {
        $this->settings = $settings;
        $this->registry = $registry;

        add_filter(
            'allowed_block_types_all',
            [$this, 'filterBlocksByRole'],
            77,
            2
        );
    }


    /**
     * Filters allowed Gutenberg blocks for the current user.
     *
     * @param bool|array $allowedBlocks Block types allowed so far.
     * @param \WP_Block_Editor_Context $context Editor context.
     * @return bool|array
     */
    public function filterBlocksByRole($allowedBlocks, $context)
    {
        if (!function_exists('wp_get_current_user')) {
            return $allowedBlocks;
        }

        $role = $this->getCurrentUserRole();

        if ($role === '') {
            return [];
        }
        $allowedPerRole = $this->settings->getBlockSlugsForRole($role);

        if (!is_array($allowedPerRole)) {
            return [];
        }

        $allowedPerRole = array_values(array_filter($allowedPerRole, static function ($slug) {
            return is_string($slug) && $slug !== '';
        }));

        Helper::debug(__METHOD__ . ' called');
        Helper::debug('Current user: ' . wp_get_current_user()->user_login);
        Helper::debug('Detected role: ' . $role);
        Helper::debug('Allowed blocks count: ' . count($allowedPerRole));
        Helper::debug('Allowed block slugs: ' . implode(', ', $allowedPerRole));

        if ($allowedPerRole === []) {
            return [];
        }

        return $allowedPerRole;
    }


    /**
     * Returns the primary role of the current user.
     *
     * @return string Role slug or empty string if none detected.
     */
    protected function getCurrentUserRole(): string
    {
        $user = wp_get_current_user();

        if (!$user || empty($user->roles) || !is_array($user->roles)) {
            return 'subscriber';
        }

        return (string) $user->roles[0];
    }
}
