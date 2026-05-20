<?php
declare(strict_types=1);

class Session {
    private const SESSION_KEY_USER = 'user_id';
    private const SESSION_KEY_MESSAGES = 'messages';

    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function isLoggedIn(): bool {
        self::start();
        return isset($_SESSION[self::SESSION_KEY_USER]) && !empty($_SESSION[self::SESSION_KEY_USER]);
    }

    public static function getUserId(): ?int {
        if (!self::isLoggedIn()) {
            return null;
        }
        return (int)$_SESSION[self::SESSION_KEY_USER];
    }

    public static function setUser(int $userId): void {
        self::start();
        $_SESSION[self::SESSION_KEY_USER] = $userId;
    }

    public static function destroy(): void {
        self::start();
        session_unset();
        session_destroy();
    }

    public static function addMessage(string $type, string $message): void {
        self::start();
        if (!isset($_SESSION[self::SESSION_KEY_MESSAGES])) {
            $_SESSION[self::SESSION_KEY_MESSAGES] = [];
        }
        $_SESSION[self::SESSION_KEY_MESSAGES][] = [
            'type' => $type,
            'text' => $message
        ];
    }

    public static function getMessages(): array {
        self::start();
        $messages = $_SESSION[self::SESSION_KEY_MESSAGES] ?? [];
        unset($_SESSION[self::SESSION_KEY_MESSAGES]);
        return $messages;
    }

    public static function hasMessages(): bool {
        self::start();
        return isset($_SESSION[self::SESSION_KEY_MESSAGES]) && !empty($_SESSION[self::SESSION_KEY_MESSAGES]);
    }
}
?>
