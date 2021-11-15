<?php
namespace SmartSoft;

final class HtmlOption {

    private static $selected;
    private static $checked;
    private static $disabled;

    private $text;

    public static function __constructStatic() {
        HtmlOption::$selected = new HtmlOption("selected");
        HtmlOption::$checked = new HtmlOption("checked");
        HtmlOption::$disabled = new HtmlOption("disabled");
    }

    private function __construct(string $text) {
        $this->text = $text;
    }

    public function create(bool $value = true): string {
        if ($value) {
            return " {$this->text}=\"{$this->text}\"";
        } else {
            return "";
        }
    }

    public static function selected(bool $value = true): string {
        return HtmlOption::$selected->create($value);
    }

    public static function checked(bool $value = true): string {
        return HtmlOption::$checked->create($value);
    }

    public static function disabled(bool $value = true): string {
        return HtmlOption::$disabled->create($value);
    }

}


HtmlOption::__constructStatic();