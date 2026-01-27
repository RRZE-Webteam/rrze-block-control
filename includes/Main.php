<?php

namespace RRZE\BlockControl;

use RRZE\BlockControl\Blocks\BlockRegistry;
use RRZE\BlockControl\Blocks\BlockControl;

defined('ABSPATH') || exit;

/**
 * The Main Plugin class
 *
 * initiates classes
 */
class Main
{

    protected BlockRegistry $blockRegistry;
    protected Settings $settings;
    protected BlockControl $blockControl;


    public function __construct()
    {
        $this->initHooks();


        //Write additional functions for example to load your CSS in the frontend, etc.
    }

    /**
     * Initializes plugin classes
     * @return void
     */
    public function initHooks(): void
    {
        $this -> blockRegistry = new Blocks\BlockRegistry(); // Wenn du trotzdem eine separate Init- oder Register-Funktion in den Klassen hast
        $this -> settings = new Settings(); // Was das hier auslöst: Der Konstruktor in der Klasse Settings wird gefeuert
        $this -> blockControl = new Blocks\BlockControl(
            $this -> settings,
            $this -> blockRegistry

        ); // Was das hier auslöst: Der Konstruktor in der Klasse BlockControl wird gefeuert

        if (is_admin()) {
            new SettingsPage(
                $this -> settings,
                $this -> blockRegistry
            );
            new AdminNotice();
        }
    }
}















// Registrieren der BlockEinschränkung (via PHP WP-Filter | Oder eventuell über JavaScript und die BlockEditor API-/Schnittstelle)
// für den Editor (Backend, Block-Editor) – (Eigene Klasse)
/**
 * Was ich mir noch vorstellen könnte. Es gibt ja im WordPress BlockEditor bereits die Möglichkeit Blöcke zu verstecken.
 * Eventuell kann man über die JavaScript-Schnittstelle diese Einstellung anpassen. So dass der User dann diese Blöcke nicht mehr sieht.
 * Müsste man testen / überlegen, ob das sinnig ist.
 *
 * Wie würde man ausprobieren, ob das sinnig ist. Indem man im BlockEditor einfach mal ein paar Blöcke für sich selbst versteckt
 * und schaut, wie das die Seiten-Bearbeitung beeinflusst. (Vielleicht ist eines der Beiden ja besser als das andere.)
 */

// Sicherstellen, dass alle Komponenten diesselbe Datenquelle benutzen.
/**
 * Die Daten kommen vermutlich dann über die gespeicherte Option in wp options. | Eventuell kann es auch später notwendig sein,
 * die Daten auch über die Rest-API zur Verfügung zu stellen, falls man auf JavaScript-Seite damit arbeiten will.
 */