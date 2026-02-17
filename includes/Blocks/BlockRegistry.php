<?php

namespace RRZE\BlockControl\Blocks;

use RRZE\BlockControl\Helper;

defined('ABSPATH') || exit;

/**
 * Provides read-only access to all registered Gutenberg blocks.
 *
 *  This class acts as a lightweight wrapper around WP_Block_Type_Registry.
 *  It reduces block objects to the information needed by this plugin
 *  (slug, title, category) and exposes the data grouped by category.
 *
 *  The class uses lazy loading and does NOT hook into WordPress actions.
 *  Consumers decide when the block list is needed.
 */
class BlockRegistry
{

    /**
     * Cached block list grouped by category.
     *
     * Format:
     * [
     *   'text' => [
     *     [ 'slug' => 'core/paragraph', 'title' => 'Paragraph' ],
     *     [ 'slug' => 'core/heading',   'title' => 'Heading'   ],
     *   ],
     *   'media' => [
     *     [ 'slug' => 'core/image', 'title' => 'Image' ],
     *   ],
     * ]
     *
     * @var array
     */
    protected array $blockSlugsByCategory = [];


    /**
     * Returns all registered block slugs grouped by category.
     *
     * This method uses lazy loading:
     * - On first call, it queries WP_Block_Type_Registry,
     *   reduces the data, groups it by category and caches the result.
     * - On subsequent calls, the cached data is returned.
     *
     * @return array Blocks grouped by category, including slug and title.
     */
    public function getBlockSlugsByCategory(): array
    {
        if (empty($this->blockSlugsByCategory)) {
            $this->blockSlugsByCategory = $this->loadBlockSlugsByCategory();
        }

        return $this->blockSlugsByCategory;

    }

    /**
     * Loads all registered blocks from WordPress and groups them by category.
     *
     * This method should not be called directly from outside.
     * It is separated from the public getter to keep responsibilities clear
     * and make the class easier to test.
     *
     * @return array Blocks grouped by category.
     */
    protected function loadBlockSlugsByCategory(): array
    {
        $registry = \WP_Block_Type_Registry::get_instance();
        $allBlocks = $registry->get_all_registered();

        $groupedBlocks = [];

        foreach ($allBlocks as $blockSlug => $blockType) {
            $category = $blockType->category ?? 'uncategorized';
            $title = $blockType->title ?? $blockSlug;

            $parent = $blockType->parent ?? [];
            if (!is_array($parent)) {
                $parent = [];
            }

            $groupedBlocks[$category][] = [
                'slug' => $blockSlug,
                'title' => $title,
                'parent' => is_array($parent) ? $parent : [],
            ];
        }

        return $groupedBlocks;
    }


    /**
     * Returns a flat list of all registered block slugs.
     *
     * This is a convenience helper for consumers that only need "slugs"
     * (e.g. whitelist validation or "new block" detection).
     *
     * @return string[] List of block slugs (e.g. ['core/paragraph', 'core/image']).
     */
    public function getAllBlockSlugs(): array
    {
        $grouped = $this->getBlockSlugsByCategory();
        $allSlugs = [];

        foreach ($grouped as $blocks) {
            foreach ($blocks as $block) {
                $allSlugs[] = $block['slug'];
            }
        }

        return array_values(array_unique($allSlugs));
    }


    /**
     * Detects block types that have been registered since the last snapshot.
     *
     * The method compares the current list of registered block slugs with the
     * previously stored reference in the option `rrze_block_control_known_blocks`.
     *
     * @return string[] List of newly detected block slugs.
     */
    public function getNewBlockSlugs(): array
    {

        $currentSlugs = $this->getAllBlockSlugs();
        $knownSlugs = get_option('rrze_block_control_known_blocks', []);

        if (!is_array($knownSlugs)) {
            $knownSlugs = [];
        }

        return array_values(array_diff($currentSlugs, $knownSlugs));
    }

    /**
     * Admin Notice!
     * Resolves category/title details for a given list of block slugs.
     *
     * Filters duplicates, looks each slug up in the cached block registry
     * and returns the matching metadata. Slugs without a matching block entry
     * are skipped silently.
     *
     * @param string[] $slugs List of block slugs (e.g. ['core/paragraph']).
     * @return array[] Each entry contains `slug`, `title`, and `category`.
     */
    public function getBlockDetailsForSlugs(array $slugs): array
    {
        $slugs = array_values(array_filter(array_unique($slugs)));
        if (!$slugs) {
            return [];
        }

        $blocksByCategory = $this->getBlockSlugsByCategory();
        $details = [];

        foreach ($blocksByCategory as $category => $blocks) {
            foreach ($blocks as $block) {
                if (in_array($block['slug'], $slugs, true)) {
                    $title = trim((string)($block['title'] ?? ''));
                    if ($title === '') {
                        continue;
                    }

                    $details[] = [
                        'slug' => $block['slug'],
                        'title' => $title,
                        'category' => $category,
                    ];
                }
            }
        }

        return $details;
    }


    /**
     * Marks all currently registered blocks as known.
     *
     * @return void
     */
    public function markNewBlocksAsSeen(): void
    {
        update_option(
            'rrze_block_control_known_blocks',
            $this->getAllBlockSlugs()
        );
    }

}




