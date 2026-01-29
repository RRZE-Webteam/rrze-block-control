<?php

namespace RRZE\BlockControl\Settings;

defined('ABSPATH') || exit;

use RRZE\BlockControl\BlockRegistry;


/**
 * SettingsPage
 *
 * Input & Rendering
 * Shows Tabs, full block lists, Whitelist with activated check boxes
 * saves changes via Settings.php (setOption())
 */
class SettingsPage
{
    /**
     *
     */
    public function __construct()
    {
        add_action('admin_menu', [$this, 'blockControlSettings']);

    }

    /**
     *Adds a sub menu settings page to Options
     *
     * @return void
     */
    public function blockControlSettings(): void
    {
        add_submenu_page(
                'options-general.php',
                'RRZE Block Control',
                'RRZE Block Control',
                'read',
                'rrze-block-control',
                [$this, 'renderSettingsPage']
        );
    }


    /**
     * Render SettingsPage
     *
     * @return html
     */

    public function renderSettingsPage(): void
    {

        /** Für jede Rolle einen Tab mit allen Blöcken anzeigen. Die vordefinierten Blöcke markieren.
         * Blöcke erst nach Gruppierung anzeigen (Core, Plugins Themes, Widgets, darin nach Kategorien  --> Block Registry
         *
         */
        echo '<div class="wrap">';
        echo '<h1>' . esc_html(__('RRZE Block Control', 'rrze-block-control')) . '</h1>';
        echo '<p>' . esc_html(__('Wählen Sie aus, welche Blöcke Sie für bestimmte Nutzerrollen anzeigen lassen möchten.', 'rrze-block-control')) . '</p>';
        echo '</div>';

//Todo: wo finde ich die verschiedenen Rollen?

        // --- Tabs (hart definiert, bewusst simpel)
        //$tabs = [
        // 'author' => 'Autor', //kann Beiträge anlegen, bearbeiten und veröffentliche, Medien hochladen
        //    'editor' => 'Redakteur', //kann Inhalte freigeben, editieren, löschen, Medien hochladen, Kategorien erstellen und löschen


        //   'll' => 'Mitarbeiter',//kann Beiträge anlegen und bearbeiten, kann keine Medien hochladen, kann keine Beiträge veröffentlichen

        //  ];


        // VIelleicht hast du später noch für verschiedene Abschnitte funktionen, etc.
        // Nach Bedarf.
//
//
//        // Gibt vielleicht nur das HTML für die WhiteList Section zurück.
//        /** ausgewählte Rolle ermitteln,
//         *ausgewählte Blöcke ermittenln,
//         *neue Blockliste speichern --> Settings
//         * speichert Änderungen (du kannst dir überlegen, ob diese Klasse, oder via Settings-Klasse gespeichert wird Settings.php setOption()).
//         */
//    }

    }
}



