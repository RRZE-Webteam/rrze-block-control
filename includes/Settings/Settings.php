<?php

namespace RRZE\BlockControl\Settings;

defined('ABSPATH') || exit;

use RRZE\BlockControl\Blocks\BlocksWhitelist;


/**
 * Data Source
 * knows roles, permitted blocks
 * Admin Notice
 */
class Settings
{
    // Cache, damit wir nicht mehrmals die DB fragen
    protected $whitelist = null;

    /**
     * Constructor
     */
    public function __construct()
    {

    }


    /**
     * Generiert eine WhiteList als Array
     * wird gebraucht, um mit Metadaten zu arbeiten oder alle Rollen auf einmal anzuzeigen (z.B. SettingsPage beim Rendern).
     * = Komplette Datenquelle
     * @return array
     */
    public function getWhitelist(): array
    {
        //erster Aufruf= null
        if ($this->whitelist !== null) {
            return $this->whitelist;
        }

        //get data from wp_options
        $stored = get_option('rrze_block_control_whitelist');

        //if not available, build default
        if (!is_array($stored) || !isset($stored['whitelist'])) {

            $stored = [
                'pluginVersion' => '1.0.0',
                'userGenerated' => false,
                'whitelist' => BlocksWhitelist::defaultBlocksPerRole(),
            ];

            //speichert es via update_option und gibt array zurück.
            update_option('rrze_block_control_whitelist', $stored); //

        }

        //das geladene Array wird im Objekt gespeichert nicht in der DB (optionaler Cache)
        $this->whitelist = $stored;

        return $this->whitelist;

    }


    /**
     * Den nutzt die SettingsPage, um Checkboxen vorzufüllen, und später auch BlockControl, um die
     *  erlaubten Blöcke zu kennen. Durch den Fallback auf die Defaults bleiben neue Rollen/Blöcke sofort
     *  sichtbar.
     *
     * @return array
     */
    public function getBlocksForRole(string $role): array
    {
        // 1) Vollständige Daten holen (liefert gespeicherte Werte oder legt Defaults an).
        $whitelist = $this->getWhitelist();


        // 2) Prüfen, ob für die Rolle schon eine Liste gespeichert ist.
        if (isset($whitelist['whitelist'][$role])) {
            return $whitelist['whitelist'][$role];
        }

        $defaults = BlocksWhitelist::defaultBlocksPerRole();

        return $defaults[$role] ?? [];

    }


    /**
     * Saves block settings per role
     *
     * @return html
     */

    public function saveBlocksForRole(string $role, array $blocks): void
    {
        //Strings aus dem Formular trimmen
        $cleanSelection = array_map('sanitize_text_field', $blocks);

        //Aktuelle Daten laden und Rolle überschreiben
        $whitelist = $this->getWhitelist();
        $whitelist['whitelist'][$role] = $cleanSelection;

        //in wp_options speichern und Cache aktualisieren
        update_option('rrze_block_control_whitelist', $whitelist);
        $this->whitelist = $whitelist;


    }


    /**
     * Wenn neues Plugin aktiviert wird, check ob neuer Block registriert wurde
     *
     * @return array
     */
    public function detectNewBlocks(): array
    {
        //Die Methode speichert die aktuelle Liste sofort als „bekannt“. Wenn du eine Admin-Meldung anzeigen willst,
        // bevor der Admin reagiert, solltest du die neuen Block-Slugs separat sichern (z.B.
        //update_option('rrze_block_control_new_blocks', $newBlocks)) und erst nach Bestätigung als „gesehen“ markieren.
        // In der SettingsPage kannst du daneben array_diff neu berechnen, um „neu“ auszugeben, ohne die
        // Info gleich zu verlieren.


        // aktuelle Block-Slugs holen
        $registry = new \RRZE\BlockControl\Blocks\BlockRegistry(); //neue Registry-Instanz
        $grouped = $registry->getBlocksByCategory(); //liefert Blöcke gruppiert nach Kategorie
        $currentSlugs = array_values(array_unique(array_merge(...array_values($grouped)))); //Kategorien Array auflösen und in flache Liste umwandeln

        // holt gespeicherte Referenzliste/Fallback leeres Array
        $known = get_option('rrze_block_control_known_blocks', []);

        //Sicherheitsprüfung, Schutz vor kaputten Werten
        if (!is_array($known)) {
            $known = [];
        }

        // Differenz berechnen, neue Blöcke berechnen
        $newBlocks = array_values(array_diff($currentSlugs, $known));

        // neue Referenzliste speichern, damit beim nächsten Aufruf der aktuelle Stand als „bekannt“ gilt
        update_option('rrze_block_control_known_blocks', $currentSlugs);

        return $newBlocks;

    }



}

