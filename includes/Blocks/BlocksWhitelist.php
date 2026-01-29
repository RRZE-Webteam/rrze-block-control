<?php


namespace RRZE\BlockControl\Blocks;

defined('ABSPATH') || exit;


/**
 * Default whitelist of blocks
 *
 */
class BlocksWhitelist
{

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
     *Default blocks whitelists for standard user roles
     *
     * @return array
     */
    public static function defaultBlocksPerRole(): array
    {
        return [
            'author' => [
                'core/heading',
                'core/list',
                'core/list-item',
                'core/paragraph',
                'core/preformatted',
                'core/paragraph',
                'core/quote',
                'core/table',
                'core/button',
                'core/column',
                'core/columns',
                'core/group',
                'core/gallery',
                'core/image',
                'core/media-texth',
                'core/paragraph',
                'core/paragraph',
                'core/paragraph',
                'core/paragraph',
                'core/paragraph',
            ],

            'editor' => [
                'core/paragraph',
                'core/paragraph',
                'core/paragraph',
                'core/paragraph',
                'core/paragraph',
            ],
            'contributor' => [
                'core/paragraph'

            ],
            'administrator' => [
                'core/paragraph'
            ],

        ];
    }
}
