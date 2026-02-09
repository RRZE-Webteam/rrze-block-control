<?php

namespace RRZE\BlockControl;

use RRZE\BlockControl\Blocks\BlockRegistry;
use RRZE\BlockControl\Blocks\BlockControl;
use RRZE\BlockControl\Settings\Settings;
use RRZE\BlockControl\Settings\SettingsPage;
use RRZE\BlockControl\Settings\AdminNotice;

defined('ABSPATH') || exit;

/**
 * The Main Plugin class
 *
 * initiates classes
 */
class Main
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initHooks();


        //Write additional functions for example to load your CSS in the frontend, etc.
    }

    /**
     * Initializes plugin classes
     *
     * @return void
     */
    public function initHooks(): void
    {
        $registry = new BlockRegistry();
        $settings = new Settings();

        new BlockControl($settings, $registry);

        if (is_admin()) {
            new SettingsPage($settings, $registry);
            new AdminNotice($settings);
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