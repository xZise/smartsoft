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

    protected function getRedirectPage(): string {
        return $this->page;
    }

    protected function getRedirectAction(): string {
        return "list";
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
            $_SESSION["processException"] = $e->getHtmlCode();
        }

        $params = array();
        $redirectPage = $this->getRedirectPage();
        if ($redirectPage !== "") {
            $params["page"] = $redirectPage;
        }
        $redirectAction = $this->getRedirectAction();
        if ($redirectAction !== "") {
            $params["action"] = $redirectAction;
        }

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