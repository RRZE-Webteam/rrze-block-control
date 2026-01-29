<?php

namespace RRZE\BlockControl\Blocks;

use RRZE\BlockControl\Helper;

defined('ABSPATH') || exit;

/**
 * Wissenssammler, kennt alle registrierten Blöcke, Kategorien und neue Blöcke.
 * Stellt verschiedene Getter bereit.
 * Fragt nicht. Wird gefragt von SettingsPage und BlockControl
 */
class BlockRegistry
{

    protected array $blocksByCategory = []; //Zwischenspeicher für Daten


    /**
     * Registrieren von Filtern, oder von Hooks, etc.
     */
    public function __construct()
    {
        add_action('init', [$this, 'getRegisteredBlocksWithCategories'], 99);
    }


    /**
     * Get all registered Blocks with their categories
     *
     * @return array
     */
    public function getRegisteredBlocksWithCategories(): array
    {
        $registry = \WP_Block_Type_Registry::get_instance();
        $allBlocks = $registry->get_all_registered(); //Array von WP_Block_Type Objekten

        $reducedBlocks = [];

        //foreach (ARRAY as WAS_SOLL_REIN), stecke den Schlüssel in $blockName, den Wert in $blockValue
        foreach ($allBlocks as $blockName => $blockValue) {

            $reducedBlocks[$blockName] = [
                'title' => $blockValue->title ?? $blockName,
                'category' => $blockValue->category ?? 'uncategorized',
            ];
        }
        Helper::debug('BlocklistemitKategorie');
        Helper::debug($reducedBlocks);
        //error_log(print_r($reducedBlocks, true));

        $this->blocksByCategory = $this->groupBlocksByCategory($reducedBlocks);

        return $this->blocksByCategory;

    }


    /**
     * Groups registered blocks by their category
     *
     * @param array $reducedBlocks
     * @return array – blocks grouped by categories
     */
    public function groupBlocksByCategory(array $reducedBlocks): array
    {
        $groupedBlocks = [];

        foreach ($reducedBlocks as $blockName => $blockValue) {
            //Kategorie aus der Liste auslesen
            $category = $blockValue ['category'];
            $title = $blockValue['title'] ?? $blockName;

            //Blockname und Title der Kategorie hinzufügen, [] = $blockName ist der Wert, der an die Kategorie angefügt wird.
            $groupedBlocks[$category][] = [
                'slug' => $blockName,
                'title' => $title,
            ];
        }

        Helper::debug('Gruppierte Blöcke');
        Helper::debug($groupedBlocks);

        return $groupedBlocks;
    }


    /**
     * Returns the list of registered blocks grouped by their categories.
     *
     * This method uses lazy loading:
     * - If the blocks have not been loaded yet, it retrieves them via
     *   getRegisteredBlocksWithCategories() and stores the result internally.
     * - On subsequent calls, the already stored data is returned without
     *   recalculating the block list.
     *
     * This avoids unnecessary repeated processing and ensures a consistent
     * data structure for all consumers of this method (e.g. settings pages).
     *
     * @return array An array of blocks grouped by category.
     */
    public function getBlocksByCategory(): array
    {
        if (empty($this->blocksByCategory)) {
            $this->blocksByCategory = $this->getRegisteredBlocksWithCategories();
        }

        return $this->blocksByCategory;
    }
}