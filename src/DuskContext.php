<?php

namespace Nmflabs\LaravelBehatDusk;

use Laravel\Dusk\Browser;
use Illuminate\Support\Str;
use PHPUnit\Framework\Assert as PHPUnit;
use Facebook\WebDriver\Exception\NoSuchElementException;

trait DuskContext
{
    /**
     * Checks, that element with specified selector exists on page source.
     *
     * Example: Then I should see a "body" element in source
     * Example: And I should see an "input" element in source
     *
     * @Then /^(?:|I )should see an? "(?P<element>[^"]*)" element in source$/
     */
    public function assertElementOnPage($element)
    {
        $this->browse(function (Browser $browser) use ($element) {
            $browser->assertPresent($element);
        });
    }

    /**
     * Checks, that element with specified selector doesn't exist on page source.
     *
     * Example: Then I should not see a "canvas" element in source
     * Example: And I should not see an "apex" element in source
     *
     * @Then /^(?:|I )should not see an? "(?P<selector>[^"]*)" element in source$/
     */
    public function assertElementNotOnPage($selector)
    {
        $this->browse(function (Browser $browser) use ($selector) {
            $fullSelector = $this->resolver->format($selector);

            PHPUnit::assertFalse(
                ! is_null($this->resolver->find($selector)),
                "Element [{$fullSelector}] is not present."
            );

            return $this;
        });
    }

    /**
     * Checks, that element with specified selector is visible.
     *
     * Example: Then I should see a visible "canvas" element
     * Example: And I should see a visible "canvas" element
     *
     * @Then /^(?:|I )should see a visible "(?P<selector>[^"]*)" element$/
     */
    public function assertElementVisible($selector)
    {
        $this->browse(function (Browser $browser) use ($selector) {
            $browser->assertVisible($selector);
        });
    }

    /**
     * Checks, that element with specified selector is invisible.
     *
     * Example: Then I should not see an invisible "canvas" element
     * Example: And I should not see an invisible "canvas" element
     *
     * @Then /^(?:|I )should not see an invisible "(?P<selector>[^"]*)" element$/
     */
    public function assertElementInvisible($selector)
    {
        $this->browse(function (Browser $browser) use ($selector) {
            $fullSelector = $this->resolver->format($selector);

            try {
                $invisible = ! $this->resolver->findOrFail($selector)->isDisplayed();
            } catch (NoSuchElementException $e) {
                $invisible = true;
            }

            PHPUnit::assertTrue($invisible, "Element [{$fullSelector}] is visible.");

            return $this;
        });
    }

    /**
     * Checks, that text is exists on the page.
     *
     * Example: Then I should see "hello world" on the page
     * Example: And I should see "welcome" on the page
     *
     * @Then /^(?:|I )should see "(?P<text>(?:[^"]|\\")*)" on the page$/
     */
    public function assertPageContainsText($text)
    {
        $this->browse(function (Browser $browser) use ($text) {
            $browser->assertSee($text);
        });
    }

    /**
     * Checks, that text doesn't exist on the page.
     *
     * Example: Then I should not see "foo bar" on the page
     * Example: And I should not see "You are a looser" on the page
     *
     * @Then /^(?:|I )should not see "(?P<text>(?:[^"]|\\")*)" on the page$/
     */
    public function assertPageDoesntContainsText($text)
    {
        $this->browse(function (Browser $browser) use ($text) {
            $browser->assertDontSee($text);
        });
    }

    /**
     * Fills in form field with specified selector.
     *
     * Example: When I fill in "username" with: "bwayne"
     * Example: And I fill in "bwayne" for "username"
     *
     * @When /^(?:|I )fill in "(?P<selector>(?:[^"]|\\")*)" with "(?P<value>(?:[^"]|\\")*)"$/
     * @When /^(?:|I )fill in "(?P<selector>(?:[^"]|\\")*)" with:$/
     * @When /^(?:|I )fill in "(?P<value>(?:[^"]|\\")*)" for "(?P<selector>(?:[^"]|\\")*)"$/
     */
    public function fillField($selector, $value)
    {
        $this->browse(function (Browser $browser) use ($selector, $value) {
            $browser->type($selector, $value);
        });
    }

    /**
     * Selects value in select field with specified selector.
     *
     * Example: When I select "Bats" from "user_fears"
     * Example: And I select "Bats" from "user_fears"
     *
     * @When /^(?:|I )select "(?P<value>(?:[^"]|\\")*)" from "(?P<selector>(?:[^"]|\\")*)"$/
     */
    public function selectOption($selector, $value)
    {
        $this->browse(function (Browser $browser) use ($selector, $value) {
            $browser->select($selector, $value);
        });
    }

    /**
     * Selects value in radio field with specified selector.
     *
     * Example: When I select radio "Bats" from "user_fears"
     * Example: And I select radio "Bats" from "user_fears"
     *
     * @When /^(?:|I )select radio "(?P<value>(?:[^"]|\\")*)" from "(?P<selector>(?:[^"]|\\")*)"$/
     */
    public function selectRadio($selector, $value)
    {
        $this->browse(function (Browser $browser) use ($selector, $value) {
            $browser->radio($selector, $value);
        });
    }

    /**
     * Checks checkbox field with specified selector.
     *
     * Example: When I check "animal"
     * Example: And I check "animal"
     *
     * @When /^(?:|I )check "(?P<selector>(?:[^"]|\\")*)"$/
     */
    public function selectCheckbox($selector)
    {
        $this->browse(function (Browser $browser) use ($selector) {
            $browser->check($selector);
        });
    }

    /**
     * Unchecks checkbox field with specified selector.
     *
     * Example: When I uncheck "animal"
     * Example: And I uncheck "animal"
     *
     * @When /^(?:|I )uncheck "(?P<selector>(?:[^"]|\\")*)"$/
     */
    public function uncheckCheckbox($selector)
    {
        $this->browse(function (Browser $browser) use ($selector) {
            $browser->uncheck($selector);
        });
    }

    /**
     * Checks checkbox field with specified selector & value.
     *
     * Example: When I check "animal" with value "Bats"
     * Example: And I check "animal" with value "Bats"
     *
     * @When /^(?:|I )check "(?P<selector>(?:[^"]|\\")*)" with value "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function selectCheckboxWithValue($selector, $value)
    {
        $this->browse(function (Browser $browser) use ($selector, $value) {
            $browser->check($selector, $value);
        });
    }

    /**
     * Unchecks checkbox field with specified selector & value.
     *
     * Example: When I uncheck "animal" with value "Bats"
     * Example: And I uncheck "animal" with value "Bats"
     *
     * @When /^(?:|I )uncheck "(?P<selector>(?:[^"]|\\")*)" with value "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function uncheckCheckboxWithValue($selector, $value)
    {
        $this->browse(function (Browser $browser) use ($selector, $value) {
            $browser->uncheck($selector, $value);
        });
    }

    /**
     * Attaches file to field with specified selector.
     *
     * Example: When I attach "bwayne_profile.png" to "profileImageUpload"
     * Example: And I attach "bwayne_profile.png" to "profileImageUpload"
     *
     * @When /^(?:|I )attach the file "(?P<path>[^"]*)" to "(?P<selector>(?:[^"]|\\")*)"$/
     */
    public function attachFileToField($selector, $path)
    {
        $this->browse(function (Browser $browser) use ($selector, $path) {
            $browser->attach(
                $selector,
                $this->fileBasePath . (Str::startsWith($path, '/') ? $path : DIRECTORY_SEPARATOR . $path));
        });
    }

    /**
     * Presses button with specified selector.
     *
     * Example: When I press "Log In"
     * Example: And I press "Log In"
     *
     * @When /^(?:|I )press "(?P<selector>(?:[^"]|\\")*)"$/
     */
    public function pressButton($selector)
    {
        $this->browse(function (Browser $browser) use ($selector) {
            $browser->press($selector);
        });
    }

    /**
     * Checks, that current page PATH is equal to specified.
     *
     * Example: Then I should be on "/"
     * Example: And I should be on "/bats"
     * Example: And I should be on "http://google.com"
     *
     * @Then /^(?:|I )should be on "(?P<page>[^"]+)"$/
     */
    public function assertPageAddress($path)
    {
        $this->browse(function (Browser $browser) use ($path) {
            $browser->assertPathIs($path);
        });
    }

    /**
     * Checks, that element with specified selector contains specified text/
     *
     * Example: Then I should see "Batman" in the "heroes_list" element
     * Example: And I should see "Batman" in the "heroes_list" element
     *
     * @Then /^(?:|I )should see "(?P<text>(?:[^"]|\\")*)" in the "(?P<selector>[^"]*)" element$/
     */
    public function assertElementContainsText($selector, $text)
    {
        $this->browse(function (Browser $browser) use ($selector, $text) {
            $browser->assertSeeIn($selector, $text);
        });
    }

    /**
     * Checks, that element with specified selector doesn't contain specified text.
     *
     * Example: Then I should not see "Bruce Wayne" in the "heroes_alter_egos" element
     * Example: And I should not see "Bruce Wayne" in the "heroes_alter_egos" element
     *
     * @Then /^(?:|I )should not see "(?P<text>(?:[^"]|\\")*)" in the "(?P<selector>[^"]*)" element$/
     */
    public function assertElementNotContainsText($selector, $text)
    {
        $this->browse(function (Browser $browser) use ($selector, $text) {
            $browser->assertDontSeeIn($selector, $text);
        });
    }
}
