<?php

namespace SmartSoft;

final class Notification {

    private string $text;
    private bool $error;

    public function __construct(string $text, bool $error) {
        $this->text = $text;
        $this->error = $error;
    }

    public function getText(): string {
        return $this->text;
    }

    public function getError(): bool {
        return $this->error;
    }

    public function getHtmlCode(): string {
        $class = $this->error ? ' class="error"' : "";
        return "<div$class>{$this->text}</div>";
    }

    public static function createNotificationBox(array $notifications): string {
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