<?php

namespace RRZE\BlockControl;
defined('ABSPATH' || exit);

/**
 * The Main Plugin class
 */
class Main
{
    public function __construct()
    {
        $this->initiate_block_registration();

        //Write additional functions for example to load your CSS in the frontend, etc.
    }

    /**
     * Initiate the Block Registration process
     *
     * @return void
     */
    public function initiate_block_registration(): void
    {
        new BlockLimitation();
    }
}