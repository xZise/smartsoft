<?php
namespace SmartSoft\Exceptions;

/**
 * This exception is thrown when processing a command fails for some reason.
 */
class ProcessActionException extends \Exception {

    const INVALID_PAGE = 'Ungültiger Parameter <span class="code">page</span>';
    const MISSING_PERMISSION = 'Der Nutzer ist nicht berechtigt';

    private string $description;

    public function __construct(string $description) {
        $this->description = $description;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getHtmlCode(): string {
        return $this->getDescription();
    }
}