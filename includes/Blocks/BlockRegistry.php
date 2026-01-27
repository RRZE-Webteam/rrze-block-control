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
    /**
     * Registrieren von Filtern, oder von Hooks, etc.
     */
    public function __construct()
    {
        add_action('init', [$this, 'getRegisteredBlocksWithCategories'],99);
        /**
         * Läuft der Hook zu früh, um alle Blöcke abzufragen, die registriert werden?
         */

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
                'category' => $blockValue->category ?? 'uncategorized',
            ];
        }
        Helper::debug('BlocklistemitKategorie');
        Helper::debug($reducedBlocks);

        return $this->groupBlocksByCategory($reducedBlocks);

    }


    /**
     * Groups registered blocks by their category
     *
     * @param array $reducedBlocks
     * @return array – blocks grouped by categories
     */
    public function groupBlocksByCategory(array $reducedBlocks): array
    {
        //ToDo: „Ich bekomme Blöcke mit Kategorien und gebe Blöcke nach Kategorien gruppiert zurück.“

        $groupedBlocks = [];

        foreach ($reducedBlocks as $blockName => $blockValue) {
            //Kategorie aus der Liste auslesen
            $category = $blockValue ['category'];

            //Blockname der Kategorie hinzufügen, [] = $blockName ist der Wert, der an die Kategorie angefügt wird.
            $groupedBlocks[$category][] = $blockName;
        }

        Helper::debug('Gruppierte Blöcke');
        Helper::debug($groupedBlocks);

        return $groupedBlocks;
    }


}