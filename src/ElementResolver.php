<?php

namespace Nmflabs\LaravelBehatDusk;

use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\ElementResolver as BaseElementResolver;

/**
 * Automatically scroll to the viewport.
 */
class ElementResolver extends BaseElementResolver
{
    /**
     * {@inheritdoc}
     */
    public function findOrFail($selector)
    {
        if (! is_null($element = $this->findById($selector))) {
            return $element;
        }

        $element = $this->driver->findElement(
            WebDriverBy::cssSelector($this->format($selector))
        );

        if ($element) {
            $element->getLocationOnScreenOnceScrolledIntoView();
        }

        return $element;
    }

    /**
     * {@inheritdoc}
     */
    protected function findById($selector)
    {
        if (preg_match('/^#[\w\-:]+$/', $selector)) {
            $element = $this->driver->findElement(WebDriverBy::id(substr($selector, 1)));

            if ($element) {
                $element->getLocationOnScreenOnceScrolledIntoView();
            }

            return $element;
        }
    }
}
