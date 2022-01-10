<?php
/** For the documentation, see in the class. */

namespace SmartSoft\Displays;

/**
 * A generic form field allowing it to add options or attributes and generating the code based on them.
 */
abstract class FormField {

    private string $tag;
    private array $attributes;

    /**
     * Creates a new form field instance.
     *
     * @param string $tag The tag-value of this field.
     * @param string $name The name and id of this field.
     */
    public function __construct(string $tag, string $name) {
        $this->tag = $tag;
        $this->attributes = array("name" => $name, "id" => $name);
    }

    /**
     * Returns the value of the given attribute.
     *
     * @param string $attribute The name of the attribute.
     * @return string The value of the given attribute.
     */
    public function getAttribute(string $attribute): string {
        return $this->attributes[$attribute];
    }

    /**
     * Removes/Unsets the given attribute.
     *
     * @param string $attribute The name of the attribute.
     */
    public function removeAttribute(string $attribute) {
        unset($this->attributes[$attribute]);
    }

    /**
     * Sets the value of the given attribute.
     *
     * @param string $attribute The name of the attribute.
     * @param string $value The new value for the given attribute.
     */
    public function setAttribute(string $attribute, string $value) {
        $this->attributes[$attribute] = $value;
    }

    /**
     * When setting the option it'll add an attribute of the given name and sets it to the given name. When clearing it
     * it'll remove that attribute.
     *
     * @param string $option THe name of the option/attribtue.
     * @param bool $isSet Whether this option should be set or not.
     */
    public function setOption(string $option, bool $isSet = true) {
        if (!$isSet) {
            $this->removeAttribute($option);
        } else {
            $this->setAttribute($option, $option);
        }
    }

    /**
     * Returns the HTML code for this form field.
     *
     * @return string The HTML code.
     */
    public function generateHtml(): string {
        $content = $this->generateHtmlContent();
        $code = "<{$this->tag}";

        foreach ($this->attributes as $attrName => $attrValue) {
            $code .= " $attrName=\"$attrValue\"";
        }

        if ($content === null) {
            $code .= " />";
        } else {
            $code .= ">$content</{$this->tag}>";
        }
        return $code;
    }

    /**
     * Returns the HTML code for the content of the tag. If null, it is treated as a self-closing tag.
     *
     * @return ?string The HTML code or null.
     */
    protected abstract function generateHtmlContent(): ?string;
}