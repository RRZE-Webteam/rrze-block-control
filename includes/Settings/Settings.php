<?php

namespace RRZE\BlockControl\Settings;

defined('ABSPATH') || exit;


/**
* Encapsulates all persistence-related tasks:
 * loading and caching the blacklist option, exposing per-role getters, saving updated block restrictions.
 *
 * NOTE:
 * - In "blacklist mode" we store the blocks that are NOT allowed.
 * - The actual "allowed blocks" list is computed elsewhere (BlockControl) by doing:
 *   allowed = all_registered_blocks - restricted_blocks
    */
class Settings
{
    /**
     * In-memory cache for the option payload.
     *
     * @var array|null
     */
    protected ?array $blacklist = null;


    /**
     * Loads the blacklist structure from the database.
     *
     * Flow:
     * 1) Return cached data if already loaded.
     * 2) Load option from database.
     * 3) If structure invalid or missing, create default.
     * 4) Cache and return.
     *
     * @return array
     */
    public function getBlacklist(): array
    {
        if ($this->blacklist !== null) {
            return $this->blacklist;
        }

        $blacklistConfig = get_option('rrze_block_control_blacklist');

        $hasValidBlacklist = is_array($blacklistConfig) && isset($blacklistConfig['blacklist']);

        if (!$hasValidBlacklist) {
            $blacklistConfig = [
                'pluginVersion' => '1.0.0',
                'userGenerated' => false,
                'blacklist' => [],
            ];

            update_option('rrze_block_control_blacklist', $blacklistConfig); //

        }

        $this->blacklist = $blacklistConfig;

        return $this->blacklist;

    }


    /**
     * Returns the restricted (blocked) block slugs for a given role.
     *
     * Important:
     * - Empty array means: nothing blocked → everything allowed.
     *
     * @param string $role
     * @return string[]
     */
    public function getBlockSlugsForRole(string $role): array
    {
        $blacklistConfig = $this->getBlacklist();
        $roleBlacklist = $blacklistConfig['blacklist'] ?? [];

        return $roleBlacklist[$role] ?? [];
    }


    /**
     * Saves the restricted block slugs for a specific role.
     *
     * Steps:
     * 1) Sanitize incoming block slugs.
     * 2) Replace blacklist entry for that role.
     * 3) Persist to database.
     * 4) Refresh in-memory cache.
     *
     * @param string $role
     * @param string[] $blocks
     * @return void
     */
    public function saveRestrictedBlockSlugsForRole(string $role, array $blocks): void
    {
        $sanitizedBlockSlugs = array_values
        (array_filter(
                array_map('sanitize_text_field', $blocks)
            )
        );

        $blacklistConfig = $this->getBlacklist();
        $blacklistConfig['blacklist'][$role] = $sanitizedBlockSlugs;

        update_option('rrze_block_control_blacklist', $blacklistConfig);

        $this->blacklist = $blacklistConfig;

    }


    /**
     * Removes all restrictions for a given role.
     *
     * In blacklist mode:
     * - Removing role entry means nothing is blocked.
     *
     * @param string $roleSlug
     * @return void
     */
    public function resetRole(string $roleSlug): void
    {
        $blacklistConfig = $this->getBlacklist();

        if (!isset( $blacklistConfig['blacklist'][$roleSlug])) {
            return;
        }

        unset( $blacklistConfig['blacklist'][$roleSlug]);

        update_option('rrze_block_control_blacklist',  $blacklistConfig);

        $this->blacklist =  $blacklistConfig;
    }

}

