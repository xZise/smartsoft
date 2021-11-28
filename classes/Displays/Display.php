<?php

namespace SmartSoft\Displays;

/**
 * The Display class itself is the core of every subpage of index.php.
 *
 * It can only return the title and create the page contents placed into the content space of the page.
 */
abstract class Display {

    /**
     * Creates the HTML-Code which is placed into the content space of the page.
     *
     * @param bool $validPage Whether page is known to the system.
     * @param bool $validRights Whether page or action can be done by the user.
     */
    public abstract function createPage(bool $validPage, bool $validRights): string;

    /**
     * Returns the current title of this Display without HTML code. Will also be used inside the &lt;title&gt;.
     */
    public abstract function getTitle(): string;

    /**
     * Returns the JavaScript-function which should be called on load of the body. If null, it won't load any function.
     *
     * @return ?string The name of the JavaScript-function, or null.
     */
    public function getLoadMethod(): ?string {
        return null;
    }

}