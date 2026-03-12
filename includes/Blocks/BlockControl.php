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
     * $allowedBlocks = allowed_block_types_all
     * $allBlocks = WP_Block_Type_Registry
     * $allowed = allBlocks - restricted
     *
     * @param bool|array $allowedBlocks all registered blocks so far.
     * @param \WP_Block_Editor_Context $context Editor context.
     * @return bool|array
     */
    public function filterBlocksByRole($allowedBlocks, $context): bool | array
    {
        if (!$this->shouldFilterCurrentUser()) {
            return $allowedBlocks;
        }

        $role = $this->getCurrentUserRole();

        if ($role === '') {
            return $allowedBlocks;
        }

        $restricted = $this->settings->getBlockSlugsForRole($role);

        if (empty($restricted)) {
            return true;
        }

        $allBlocks = $this->registry->getAllBlockSlugs();

        $allowedBlocks = array_diff($allBlocks, $restricted);

        return array_values($allowedBlocks);
    }


    /**
     * Determines whether the current user should be subject to block filtering.
     *
     * Checks if WordPress is available, evaluates the stored option and the
     * `rrze_block_control_filter_admins` filter. By default administrators remain
     * exempt so there is always an escape hatch back into the editor.
     *
     * @return bool True if the block restrictions should apply to the current user.
     */
    protected function shouldFilterCurrentUser(): bool {
        if (!function_exists('current_user_can')) {
            return false;
        }

        // Option im Backend (Checkbox) oder Filter, standardmäßig false.
        $filterAdmins = (bool) get_option('rrze_block_control_filter_admins', false);
        $filterAdmins = apply_filters('rrze_block_control_filter_admins', $filterAdmins);

        if (!$filterAdmins && current_user_can('manage_options')) {
            return false;
        }

        return true;
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

        return (string)$user->roles[0];
    }
}
