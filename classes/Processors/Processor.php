<?php

namespace SmartSoft\Processors;

use SmartSoft\Exceptions\ProcessActionException;

/**
 * A processor handles applying the change to the database and returning to the previous list.
 */
abstract class Processor {

    private string $page;

    /**
     * Creates a new processor for the specific page. That page is required to redirect later to it.
     *
     * @param string $page
     */
    public function __construct(string $page) {
        $this->page = $page;
    }

    /**
     * Executes the handler for the given action and creates an redirect to the previous list.
     *
     * @param string $action The action which should be executed.
     */
    public function process(string $action) {
        //TODO: We need to check the return value and do something (maybe?)
        try {
            $this->processAction($action);
        } catch (ProcessActionException $e) {

        }

        $params = array("page" => $this->page, "action" => "list");

        $paramsText = "";
        foreach ($params as $name => $value) {
            $paramsText .= $paramsText ? "&" : "?";
            $paramsText .= "$name=$value";
        }

        header("Location: " . dirname($_SERVER['REQUEST_URI']) . "/$paramsText");
    }

    /**
     * Handles the specific action and can throw ProcessActionException.
     *
     * @param string $action The action which should be handled.
     */
    protected abstract function processAction(string $action);

}