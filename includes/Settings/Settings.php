<?php

namespace RRZE\BlockControl\Settings;

defined('ABSPATH') || exit;


/**
 *  Encapsulates all persistence-related tasks:
 *  loading and caching the whitelist option, exposing per-role getters, saving updated block selections
 *  and detecting newly registered blocks to inform the admin.
 *  Any component that needs to read or write whitelist data should go through this class.
 */
class Settings
{
    // Cache
    protected ?array $whitelist = null;


    /**
     * Loads the complete whitelist payload from `wp_options`, creating a default structure when none exists yet.
     *
     * The first call ist null.
     * The method caches the resulting array in `$this->whitelist` to avoid repeated database lookups. On the first
     * invocation it attempts to read the option `rrze_block_control_whitelist`; if missing or invalid, a default data
     * set is built from `BlocksWhitelist::defaultBlocksPerRole()`, stored via `update_option()`, and returned. This
     * shared source of truth is later consumed by `getBlocksForRole()`, the settings UI, and `BlockControl`.
     *
     * @return array Full whitelist data including metadata and per-role block assignments.
     */
    public function getWhitelist(): array
    {
        if ($this->whitelist !== null) {
            return $this->whitelist;
        }

        $whitelistConfig = get_option('rrze_block_control_whitelist');

        $hasValidWhitelist = is_array($whitelistConfig) && isset($whitelistConfig['whitelist']);

        if (!$hasValidWhitelist) {
            $whitelistConfig = [
                'pluginVersion' => '1.0.0',
                'userGenerated' => false,
                'whitelist' => [],
            ];

            update_option('rrze_block_control_whitelist', $whitelistConfig); //

        }

        $this->whitelist = $whitelistConfig;

        return $this->whitelist;

    }


    /**
     * Retrieves the list of allowed block slugs for a given role, falling back to the default whitelist if no custom
     * selection has been stored yet.
     *
     * The method uses `getWhitelist()` as the single source of truth: It first checks whether the option contains
     * a `whitelist` entry for the requested `$role`. If yes, that array is returned as-is. If not (e.g. on fresh
     * installations or newly added roles), it gracefully falls back to `BlocksWhitelist::defaultBlocksPerRole()`
     * so the UI and BlockControl can still operate with sensible defaults.
     *
     * @param string $role Role identifier (e.g. `author`, `editor`).
     * @return string[] Array of block slugs that are currently permitted for that role.
     */
    public function getBlockSlugsForRole(string $role): array
    {
        $whitelistConfig = $this->getWhitelist();
        $roleWhitelist = $whitelistConfig['whitelist'] ?? [];

        return $roleWhitelist[$role] ?? [];
    }


    /**
     * Persists the selected block slugs for a specific role by updating the shared whitelist option and refreshing
     * the in-memory cache.
     *
     * Expected flow:
     *  1. Sanitize every submitted slug (`sanitize_text_field`) to avoid storing arbitrary payload from the form.
     *  2. Load the current whitelist data via `getWhitelist()` and replace the entry of the requested role with
     *     the sanitized array. At this stage you could also add validation against `BlockRegistry`, if desired.
     *  3. Write the updated structure back to the option `rrze_block_control_whitelist` and mirror it in
     *     `$this->whitelist` so subsequent reads see the newly saved state without another database call.
     *
     * @param string $role Role identifier that should receive the updated whitelist.
     * @param string[] $blockSlugs Array of block slugs coming from the settings form (e.g. `core/paragraph`).
     * @return void
     */
    public function saveBlockSlugsForRole(string $role, array $blocks): void
    {
        $sanitizedBlockSlugs = array_values
        (array_filter(
                array_map('sanitize_text_field', $blocks)
            )
        );

        $whitelistConfig = $this->getWhitelist();
        $whitelistConfig['whitelist'][$role] = $sanitizedBlockSlugs;

        update_option('rrze_block_control_whitelist', $whitelistConfig);

        $this->whitelist = $whitelistConfig;

    }


    /**
     * Resets the block selection for a given role back to an empty state.
     *
     * Triggered when an admin clicks the reset button on the settings page.
     * The stored whitelist entry for the specified role is removed, the
     * `rrze_block_control_whitelist` option is updated, and the in-memory cache
     * (`$this->whitelist`) is refreshed so subsequent reads reflect the change.
     *
     * @param string $roleSlug
     * @return void
     */
    public function resetRole(string $roleSlug): void
    {
        $whitelistConfig = $this->getWhitelist();

        if (!isset($whitelistConfig['whitelist'][$roleSlug])) {
            return;
        }

        unset($whitelistConfig['whitelist'][$roleSlug]);

        update_option('rrze_block_control_whitelist', $whitelistConfig);

        $this->whitelist = $whitelistConfig;
    }

}

