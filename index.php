<?php
    namespace SmartSoft;

    /**
     * The user facing page which creates a display, depending on the current login state and the specified page.
     *
     * It then inserts the display information (title and content).
     */

    session_start();

    require_once("classes/User.php");
    require_once("classes/Database.php");

    require_once("classes/Displays/AccountDisplay.php");
    require_once("classes/Displays/CustomerDisplay.php");
    require_once("classes/Displays/MessageDisplay.php");
    require_once("classes/Displays/EmployeeDisplay.php");
    require_once("classes/Displays/InstallDisplay.php");
    require_once("classes/Displays/LoginDisplay.php");

    use SmartSoft\Displays;
    use SmartSoft\Database;

    $db = new Database(false);
    try {
        $isInstalled = $db->checkInstalled();
    } finally {
        $db = null;
    }

    if (!$isInstalled) {
        $display = new Displays\InstallDisplay();
        $validRights = true;
        $validPage = true;
    } else {
        $user = User::create();
        if ($user === null) {
            $display = new Displays\LoginDisplay();
            $validRights = true;
            $validPage = true;
        } else {
            if (isset($_GET["action"])) {
                $action = $_GET["action"];
            } else {
                $action = "list";
            }

            if (isset($_GET["page"])) {
                $page = $_GET["page"];
            } else {
                $page = "message";
            }

            $validPage = true;
            switch ($page) {
                case "account":
                    $display = new Displays\AccountDisplay($user, $action);
                    break;
                case "employee":
                    $display = new Displays\EmployeeDisplay($user, $action);
                    break;
                case "customer":
                    $display = new Displays\CustomerDisplay($user, $action);
                    break;
                default:
                    $validPage = false;
                case "message":
                    $display = new Displays\MessageDisplay($user, $action);
                    break;
            }

            $validRights = $display->checkRights();

            if (!$validRights || !$validPage) {
                $display = new Displays\MessageDisplay($user, "list");
                if (!$display->checkRights()) {
                    $validRights = false;
                }
            }
        }
    }

    $loadMethod = $display->getLoadMethod();
    if ($loadMethod !== null) {
        $loadMethod = "onload=\"$loadMethod\"";
    } else {
        $loadMethod = "";
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo("{$display->getTitle()} - Online-Portal"); ?></title>
        <link rel="stylesheet" href="style.css">
        <link rel="shortcut icon" href="favicon.ico">
        <link rel="icon" type="image/png" href="img/employee.png" sizes="64x64">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="script.js"></script>
    </head>
<body <?= $loadMethod; ?>>
    <?php echo($display->createPage($validPage, $validRights)); ?>
    <footer><div>Impressum</div><div>Grafiken von <a href="https://icons8.com">icons8.com</a></div><div>?? 2021</div></footer>
</body>
</html>