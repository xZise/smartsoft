<?php
/** For the documentation, see in the class. */

namespace SmartSoft\Processors;

require_once("classes/Database.php");

require_once("classes/Types/EmployeeType.php");

require_once("classes/Processors/Processor.php");

use SmartSoft\Database;
use SmartSoft\Types\EmployeeType;

/**
 * This processor handles changing the password of the currently logged in user.
 */
class InstallProcessor extends Processor {

    /** Creates a new processor instance. */
    public function __construct() {
        parent::__construct("install");
    }

    /**
     * Creates the database with at least one user (Administrator), as long as the database does not exists. When the
     * option to import the test data is selected as well, it'll import them afterwards too.
     *
     * @param string $action The action, ignored in this case.
     */
    protected function processAction(string $action): void {
        $db = new Database(false);
        try {
            if (!$db->checkInstalled()) {
                $sql = file_get_contents("SQL/install-mysql.sql");
                $db->getDatabase()->exec($sql);

                // Create at least one administrative user
                $type = EmployeeType::getInstance();
                $type->insertUser(
                    $db,
                    array(
                        "Administrator" => "on",
                        "Name" => "Administrator",
                        "Username" => "administrator"));

                if ($_POST["withdata"] === "on") {
                    $sql = file_get_contents("SQL/testdata.sql");
                    $db->getDatabase()->exec($sql);
                }
            }
        } finally {
            $db = null;
        }
    }

    protected function getRedirectAction(): string {
        return "";
    }

    protected function getRedirectPage(): string {
        return "";
    }
}