<?php


namespace RRZE\BlockControl\Blocks;

defined('ABSPATH') || exit;


/**
 * Central place for the plugin’s default whitelist presets. The static methods defined here return
 *  arrays of block slugs grouped by role and act as the initial data source for Settings as well as
 *  the fallback whenever no user-defined whitelist exists yet.
 */
class BlockWhitelist
{
    /**
     * Provides the built-in whitelist presets for each supported role.
     *
     * This static helper returns a simple associative array where each key is a role slug (e.g. `author`,
     * `editor`) and the value is a list of block slugs that should be enabled by default for that role.
     *
     * The data serves as the initial seed for `Settings::getWhitelist()` and acts as a fallback whenever
     * no user-defined whitelist exists yet.
     *
     * @return array<string,string[]> Default block slugs per role.
     */
    public static function defaultBlockSlugsPerRole(): array
    {
        return [
            'author' => [
                'core/heading',
                'core/list',
                'core/list-item',
                'core/paragraph',
                'core/preformatted',
                'core/paragraph',
                'core/quote',
                'core/table',
                'core/button',
                'core/column',
                'core/columns',
                'core/group',
                'core/gallery',
                'core/image',
                'core/media-texth',
                'core/paragraph',
                'core/paragraph',
                'core/paragraph',
                'core/paragraph',
                'core/paragraph',
            ],

            'editor' => [
                'core/paragraph',
                'core/paragraph',
                'core/paragraph',
                'core/paragraph',
                'core/paragraph',
            ],
            'contributor' => [
                'core/paragraph'

            ],
            'administrator' => [
                'core/paragraph'
            ],

        ];
    }
}
