<?php


namespace RRZE\BlockControl;

defined('ABSPATH') || exit;

use RRZE\BlockRegistry;


/**
 * Data Source
 * knows roles, permitted blocks
 * Admin Notice
 */
class Settings
{

    //Todo: Constructor –> Der ruft irgendeinen Hook auf, der die Settings-Page letztlich registriert und vielleicht auch irgendwo SettingsPage aufrufen muss

    /**
     * Generiert eine WhiteList als Array
     */
    public function generateWhitelist()
    {
        // Gibt die Default WhiteList zurück, die gesetzt werden soll, wenn noch keine vom User gemachte WhiteList existiert.
    }

//    // Als Idee einfach: Linked Lists / bzw. ein Array mit sprechenden Keys
//    protected array $whiteList = [
//        'pluginVersion' => $pluginVersionNumber,
//        'userGenerated' => isUserGenerated(), // könntest du auch nennen, isDefaultWhiteList boolean,
//        'whitelist' => [
//            'editor' => ['core/table','core/list'],
//            'author' => [],
//        ],
//    ];


    /**
     * Soll die vordefinierten Liste pro Rolle holen
     *
     * @return array
     */
    function getDefaultBlocksPerRole()
    {
        /**  */

    }


    /**
     * Loads saved block settings per role
     *
     * @return html
     */

    public function loadRoleBlockSettings(): void
    {

        /** lödt die vordefinierten Rollen per Block
         * Würde vermutlich eher getOptions()
         */

    }

    /**
     * saves block settings per role
     *
     * @return html
     */
//    public function saveRoleBlockSettings(): void
//    {
//        /** ausgewählte Rolle ermitteln,
//         *ausgewählte Blöcke ermittenln,
//         *neue Blockliste speichern --> Settings
//         *
//         * setOptions();
//         */
//    }


//    public function validateBlockSettings(): void
//    {
//        /**
//         * Validierung ist wichtig und richtig
//         */
//    }

}

/**
 * Wenn neues Plugin aktiviert wird, check ob neuer Block registriert wurde
 */

//public function detectNewBlocks()
//{
//    /** Vergleich registrierte Blöcke-bereits bekannte
//     *  wenn neuer Block, dann Anzeige in der entsprechenden Kategorie der Settingsseite
//     *  keine Rolle bisher zugewiesen (muss durch Admin passieren)
//     *  Admin Notice: Admin über neue nicht zugewiesene Blökce informieren (evtl. eigene Notice-Klasse)
//     */
//
//}

/**
 * Save WhiteList / Save Blacklist
 * Speichert die übergebene WhiteList in die wp_options
 */
//public function saveWhitelist()
//{
//    // Falls schon eine Whitelist gespeichert wurde, rufe ich sie ab.
//    // getOption(PluginNamemeinOptionKey oder so);
//    // Falls die Option gar nicht existiert, initiiere ich sie.
//    // Entweder direkt hier initiieren, oder eine Funktion ausführen, die sie initiiert
//    // setOption(PluginNameOptionKey oder so, $defaultWhitelist); // WordPress erstellt eine option mit dem Namen und mit dem wert $defaultWhiteList in der Datenbank wp_option
//}




/**
 * Da könnte man sich auch noch fragen, ist es denn eine White-List, oder eine Black-List?
 * Oder gibt es vielleicht sogar beides? (Macht es bestimmt komplexer)
 */
//speichert und lädt alles
/**
 * Vielleicht macht es hier Sinn einen sogenannten Getter und Setter zu schreiben,
 * den man einfach in anderen PHP-Klassen später aufrufen kann. OOP
 * getOption(), setOption($value)
 */
//alle anderen Klassen fragen hier an
//kennt keine UI -> Eigene Klasse und vermutlich eigene Styles die in dieser Klasse dann registriert werden.
/**
 * Initiieren der SettingsPage? Also des UI in einer gesonderten Klasse.
 */

//Liste mit vordefinierten Blöcken pro Rolle definieren. / WhiteList vs BlackList?

/*
 * Wäre vielleicht sogar was für den besagten Getter / Setter. Überprüfen, ob bereits eine wp option existiert.
 * Falls noch nicht, kann man sie initiieren mit dem gesetzten Wert / Default.
 */

//Blockliste für Rolle zurückgeben

//Beim Ändern/Speichern:
//Validieren der übergebenen Blöcke
//Überschreiben der gespeicherten Blockliste für diese Rolle
//Speichern der Daten/Änderungen

