<?php

namespace SmartSoft\Displays;

abstract class Display {

    public abstract function createPage(bool $validPage, bool $validRights): string;

    public abstract function getTitle(): String;

}