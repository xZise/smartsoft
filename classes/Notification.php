<?php
/** For the documentation, see in the class. */

namespace SmartSoft;

/**
 * Defines and creates a simple notification.
 */
final class Notification {

    private string $text;
    private bool $error;

    /**
     * Creates a new notification with the corresponding text.
     *
     * @param string $text The text of the notification.
     * @param bool $error Whether this notification is an error.
     */
    public function __construct(string $text, bool $error) {
        $this->text = $text;
        $this->error = $error;
    }

    /**
     * Returns the text for this notification.
     */
    public function getText(): string {
        return $this->text;
    }

    /**
     * Returns whether this notification is an error.
     */
    public function getError(): bool {
        return $this->error;
    }

    /**
     * Returns the HTML code for this notification with surrounding div.
     */
    public function getHtmlCode(): string {
        $class = $this->error ? ' class="error"' : "";
        return "<div$class>{$this->text}</div>";
    }

    /**
     * Creates HTML code for an array of notifications by calling getHtmlCode for each item. It then wraps that into
     * a div with the class "notifications". If the array is empty it just returns an empty string.
     */
    public static function createNotificationBox(array $notifications): string {
        if (isset($_SESSION["processException"])) {
            $exception = $_SESSION["processException"];
            $notifications[] = new Notification($exception, true);
            unset($_SESSION["processException"]);
        }
        if ($notifications) {
            $code = '<div class="notifications">';
            foreach ($notifications as $notification) {
                $code .= $notification->getHtmlCode();
            }
            return "$code</div>";
        } else {
            return "";
        }
    }
}