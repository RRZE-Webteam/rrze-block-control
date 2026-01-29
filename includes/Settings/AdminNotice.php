<?php

namespace RRZE\BlockControl\Settings;

defined('ABSPATH') || exit;

use RRZE\BlockRegistry;


/**
 * Admin Notice
 *
 * checks if there are new blocks
 * notice, if a new block is detected.
 */

class AdminNotice
{

    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('admin_notices', [$this, 'showNewBlockNotice']);

    }

    /**
     * checks if new blocks were detected
     *
     * @return void
     */
    public function showNewBlockNotice()
    {
    /** prüfst du per Settings::getNewBlocks() (eine Helfer-Methode, die den oben beschriebenen Diff zurückgibt), ob es neue Slugs gibt. Wenn ja, gibst du ein <div class="notice notice-warning"> mit einer Liste der
     * Slugs aus und ergänzt einen Link zur Settings-Seite. Vergiss nicht, die Option erst nach dem Anzeigen zu aktualisieren, damit die Meldung verschwindet, sobald der Admin die Seite gesehen hat.  */
    }




}