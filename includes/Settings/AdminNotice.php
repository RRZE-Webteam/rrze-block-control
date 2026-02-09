<?php

namespace RRZE\BlockControl\Settings;

defined('ABSPATH') || exit;


/**
 * Admin Notice
 *
 * checks if there are new blocks
 * notice, if a new block is detected.
 */
class AdminNotice
{
    private Settings $settings;

    /**
     * Constructor
*/
    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
        add_action('admin_notices', [$this, 'showNewBlockNotice']);

    }


    /**
     * checks if new blocks were detected
     *
     * @return void
     */
     public function showNewBlockNotice(): void
     {
         if (!current_user_can('manage_options')) {
             return;
         }

         $newBlocks = $this->settings->getNewBlocks();
         if (empty($newBlocks)) {
             return;
         }

         $settingsUrl = esc_url(admin_url('options-general.php?page=rrze-block-control'));

         echo '<div class="notice notice-warning is-dismissible">';
         echo '<p>' . esc_html__('New blocks have been registered. Please check the whitelist..', 'rrze-block-control') . '</p>';
         echo '<ul>';
         foreach ($newBlocks as $slug) {
             echo '<li><code>' . esc_html($slug) . '</code></li>';
         }
         echo '</ul>';
         echo '<p><a class="button button-primary" href="' . $settingsUrl . '">'
             . esc_html__('Open Settings', 'rrze-block-control') . '</a></p>';
         echo '</div>';
         
         $this->settings->markNewBlocksAsSeen();
     }

}