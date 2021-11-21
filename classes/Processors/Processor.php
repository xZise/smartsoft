<?php

namespace SmartSoft\Processors;

use SmartSoft\Exceptions\ProcessActionException;

abstract class Processor {

    private string $page;

    public function __construct(string $page) {
        $this->page = $page;
    }

    public function process(String $action) {
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

    protected abstract function processAction(string $action);

}