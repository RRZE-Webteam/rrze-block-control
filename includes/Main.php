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
        $this->init();

    }

    /**
     * Initializes plugin classes
     *
     * @return void
     */
    public function init(): void
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

