<?php

namespace SmartSoft\Displays;

require_once("classes/Displays/FormField.php");

/**
 * A form field placeholder implementing the &lt;input&gt;-tag, by default of type "text".
 */
class InputField extends FormField {

    /**
     * __construct
     *
     * @param string $name Name and ID of the form field
     * @param string $type The type of the input field, by default "text".
     */
    public function __construct(string $name, string $type = "text") {
        parent::__construct("input", $name);
        $this->setAttribute("type", $type);
    }

    /**
     * Returns the content itself. As it is self-closing it'll always return null.
     *
     * @return ?string Always null.
     */
    protected function generateHtmlContent(): ?string {
        return null;
    }

}

