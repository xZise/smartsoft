<?php

namespace SmartSoft\Displays;

require_once("classes/Database.php");
require_once("classes/User.php");
require_once("classes/Exceptions/InvalidActionException.php");

use SmartSoft\Database;
use SmartSoft\User;
use SmartSoft\Role;
use SmartSoft\Exceptions\InvalidActionException;

/**
 * A display for showing messages, sending new ones and replying to existing ones.
 */
class MessageDisplay extends UserDisplay {

    public function __construct(User $user, String $action) {
        parent::__construct($user, $action, "message");
    }
    
    /**
     * Creates the HTML code for one thread.
     *
     * @param mixed $completeThread The thread with the messages inside it.
     * @return string The HTML code.
     */
    private function buildMessage($completeThread): string {
        $tariffText = str_repeat("🪙 ", $completeThread["Tariff"]);

        $subject = htmlspecialchars($completeThread["Subject"]);
        
        setlocale(LC_TIME, "de_DE");
        $code = "<div class=\"thread header-container\">
                    <div>
                      <div class=\"subject\">Betreff: $subject</div>
                      <div class=\"tariff\">$tariffText</div>
                      <div class=\"actions\"><form class=\"message\"><input type=\"hidden\" name=\"page\" value=\"{$this->pageName}\" /><input type=\"hidden\" name=\"ID\" value=\"$completeThread[ID]\" /><button name=\"action\" value=\"reply\" class=\"anim-button bordered\"><img src=\"img/msg-reply.png\" /><span>Antworten</span></button></form></div>
                    </div><div class=\"content\">";
        
        foreach ($completeThread["Messages"] as $message) {
            $sender = htmlspecialchars($message["Sender"]);
            $text = htmlspecialchars($message["Text"]);

            $code .= "<div class=\"message header-container\">
                        <div>
                          <div class=\"sender\">Von: {$sender}</div>
                          <div class=\"timestamp\">{$message["Timestamp"]->format("D, d M y H:i:s")}</div>
                        </div>
                        <div class=\"body\">{$text}</div>
                      </div>";
        }
        $code .= "</div></div>";
        return $code;
    }
    
    /**
     * Creates the HTML code for the threads and messages using the given conditions.
     *
     * @param string $conditionColumn The column which should be checked in the WHERE-clause.
     * @param string $conditionValue The value to which the column should be checked.
     * @return string The HTML code for the threads and messages.
     */
    private function buildMessages(string $conditionColumn, string $conditionValue): string {
        $db = new Database();
        try {
            $threads = $db->fetchAll("SELECT thread.ID, thread.Subject, thread.Customer, customer.Tariff
                                      FROM thread
                                      JOIN (SELECT MAX(Time) newest_time, Thread FROM message GROUP BY Thread) newest_message ON newest_message.Thread = thread.ID
                                      JOIN customer ON customer.ID = thread.Customer
                                      WHERE $conditionColumn = ?
                                      ORDER BY newest_message.newest_time DESC", \PDO::FETCH_NAMED, array($conditionValue));
            $messages = $db->fetchAll("SELECT message.ID, message.Thread, IFNULL(employee.Name, customer.CustomerNo) AS Sender, message.Time AS Timestamp, message.Text FROM message
                                       LEFT JOIN employee ON employee.ID = message.Sender
                                       JOIN thread ON thread.ID = message.Thread
                                       JOIN customer ON customer.ID = thread.Customer
                                       WHERE $conditionColumn = ?
                                       ORDER BY message.Time ASC", \PDO::FETCH_NAMED, array($conditionValue));
        } finally {
            $db = null;
        }

        $threadsWithMessages = array();
        foreach ($threads as $thread) {
            $threadId = $thread["ID"];
            $threadsWithMessages[$threadId] = $thread;
            $threadsWithMessages[$threadId]["Messages"] = array();
        }
        foreach ($messages as $message) {
            $message["Timestamp"] = new \DateTimeImmutable($message["Timestamp"]);
            $threadId = $message["Thread"];
            $threadsWithMessages[$threadId]["Messages"][] = $message;
        }
        $code = "<div>";
        if (count($threads) > 0) {
            foreach ($threads as $thread) {
                $threadId = $thread["ID"];
                $completeThread = $threadsWithMessages[$threadId];
                $code .= $this->buildMessage($completeThread);
            }
        } else {
            $code .= "Es gibt keine Nachrichten";
        }
        $code .= "</div>";
        return $code;
    }
    
    /**
     * Returns the list of all threads and messages corresponding to the current user.
     *
     * @return string The HTML code for those threads and messages.
     */
    private function getList(): string {
        if ($this->user->getRole() == Role::Customer) {
            $column = "customer.ID";
        } else {
            $column = "customer.Contact";
        }
        $code = $this->buildMessages($column, $this->user->getId());
        return "$code";
    }

    public function getTitle(): String {
        switch ($this->action) {
            case "reply": return "Antwort verfassen";
            case "send": return "Nachricht verfassen";
            default: return "Nachrichten";
        }
    }

    public function getTitleRow(): string {
        $title = parent::getTitleRow();
        if ($this->user->getRole() == Role::Customer) {
            $title .= "<form class=\"message\"><input type=\"hidden\" name=\"action\" value=\"send\" /><button class=\"anim-button bordered\" name=\"page\" value=\"{$this->pageName}\" type=\"submit\"><img src=\"img/msg-send.png\" />Neue Nachricht verfassen</button></form>";
        }
        return "<div class=\"main-title\">$title</div>";
    }

    public function checkRights(): bool {
        if ($this->action == "list" || ($this->action == "send" && $this->user->getRole() == Role::Customer)) {
            return true;
        } else if ($this->action == "reply") {
            $db = new Database();
            try {
                $threadId = $_GET["ID"];
                $userId = $this->user->getId();
                if ($this->user->getRole() == Role::Customer) {
                    $column = "ID";
                } else {
                    $column = "Contact";
                }
                $number = $db->fetchValue("SELECT COUNT(*) FROM thread JOIN customer ON thread.Customer = customer.ID WHERE thread.ID = ? AND customer.$column = ?", array($threadId, $userId));
            } finally {
                $db = null;
            }

            return $number > 0;
        } else {
            return false;
        }
    }
    
    /**
     * Returns the HTML code for a message input form. Will include a hidden field with the ID if it is not null and
     * otherwise a field for the subject.
     *
     * @param ?int $id The ID of the thread.
     * @return string The HTML code for a message input form.
     */
    private function getMessageForm(?int $id): string {
        $code = "<form class=\"message\" method=\"POST\" action=\"process.php\"><input type=\"hidden\" name=\"page\" value=\"{$this->pageName}\" /><input type=\"hidden\" name=\"action\" value=\"{$this->action}\" />";
        if ($id === null) {
            $code .= "<label for=\"Subject\">Betreff:</label><input type=\"text\" name=\"Subject\" value=\"\" />";
            $type = "Nachricht";
        } else {
            $code .= "<input type=\"hidden\" name=\"ID\" value=\"$id\" />";
            $type = "Antwort";
        }
        $code .= "<label for=\"Text\">Nachricht:</label><textarea name=\"Text\"></textarea><button class=\"anim-button bordered\">$type senden</button></form>";

        return $code;
    }

    private function getReplyForm(): String {
        $id = $_GET["ID"];
        $code = $this->buildMessages("thread.ID", $id);

        $code .= $this->getMessageForm($id);

        return $code;

    }

    private function getSendForm(): String {
        $code = $this->getMessageForm(null);

        return $code;
    }

    public function handleAction(): String {
        switch ($this->action) {
            case "reply": return $this->getReplyForm();
            case "send": return $this->getSendForm();
            case "list": return $this->getList();
            default: throw new InvalidActionException();
        }
    }
}