<?php
/** For the documentation, see in the class. */

namespace SmartSoft\Exceptions;

/**
 * This exception is thrown when processing a command fails for some reason.
 */
final class InvalidParameterException extends ProcessActionException {

    const PARAM_ACTION = "action";
    const PARAM_PAGE = "page";

    private string $param;

    public function __construct(string $param) {
        parent::__construct("Ungültiger Parameter '$param'");
        $this->param = $param;
    }

    public function getHtmlCode(): string {
        return "Ungültiger Parameter <span class=\"code\">{$this->param}</span>";
    }
}