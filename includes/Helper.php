<?php

declare(strict_types=1);

namespace RRZE\BlockControl;

defined('ABSPATH') || exit;

class Helper
{
    /**
     * Writes debug entries into the WordPress log if WP_DEBUG is enabled.
     *
     * Handles scalar and array/object input, resolves the log path from
     * WP_DEBUG_LOG and normalizes the provided log level shorthand.
     *
     * @param mixed  $input Arbitrary debug payload.
     * @param string $level Log level shorthand (i, d, e), defaults to info.
     * @return void
     */
    public static function debug($input, string $level = 'i'): void
    {
        if (!WP_DEBUG) {
            return;
        }
        if (in_array(strtolower((string) WP_DEBUG_LOG), ['true', '1'], true)) {
            $logPath = WP_CONTENT_DIR . '/debug.log';
        } elseif (is_string(WP_DEBUG_LOG)) {
            $logPath = WP_DEBUG_LOG;
        } else {
            return;
        }
        if (is_array($input) || is_object($input)) {
            $input = print_r($input, true);
        }
        switch (strtolower($level)) {
            case 'e':
            case 'error':
                $level = 'Error';
                break;
            case 'i':
            case 'info':
                $level = 'Info';
                break;
            case 'd':
            case 'debug':
                $level = 'Debug';
                break;
            default:
                $level = 'Info';
        }
        error_log(
            date("[d-M-Y H:i:s \U\T\C]")
            . " WP $level: "
            . basename(__FILE__) . ' '
            . $input
            . PHP_EOL,
            3,
            $logPath
        );
    }

    /**
     * Checks whether WordPress currently runs in debug mode.
     *
     * @return bool
     */
    public static function isDebug(): bool
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            return true; // Debug ON
        } else {
            return false; // Debug OFF
        }
    }

    /**
     * Returns a better readable label for a block category.
     *
     * @param string $categorySlug
     * @return string
     */
    public static function getCategoryLabel(string $categorySlug): string
    {
        $customLabels = [
            'rrze-plugins' => 'RRZE Plugins',
            'rrze_elements' => 'RRZE Elements',
            'rrze' => 'RRZE',
            'fau-elemental/FAU' => 'FAU Elemental',
            'fau'=> 'FAU'
        ];

        if (isset($customLabels[$categorySlug])) {
            return $customLabels[$categorySlug];
        }

        return ucfirst(str_replace('-', ' ', $categorySlug));
    }

}
