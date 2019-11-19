<?php

namespace Nmflabs\LaravelBehatDusk;

use Laravel\Dusk\Browser;

trait DuskPatch
{
    /**
     * {@inheritdoc}
     */
    protected function newBrowser($driver)
    {
        return new Browser($driver, new ElementResolver($driver));
    }
}
