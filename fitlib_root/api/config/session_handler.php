<?php

require_once __DIR__ . '/database.php';

class DatabaseSessionHandler implements SessionHandlerInterface {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function open($savePath, $sessionName): bool {
        return true;
    }

    public function close(): bool {
        return true;
    }

    public function read($id): string {
        $stmt = $this->pdo->prepare("SELECT data FROM sessions WHERE id = :id");
        $stmt->execute([':id' => $id]);
        if ($row = $stmt->fetch()) {
            return $row['data'];
        }
        return "";
    }

    public function write($id, $data): bool {
        $timestamp = time();
        $stmt = $this->pdo->prepare(
            "REPLACE INTO sessions (id, data, timestamp) VALUES (:id, :data, :timestamp)"
        );
        return $stmt->execute([':id' => $id, ':data' => $data, ':timestamp' => $timestamp]);
    }

    public function destroy($id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM sessions WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function gc($maxlifetime): int|false {
        $old = time() - $maxlifetime;
        $stmt = $this->pdo->prepare("DELETE FROM sessions WHERE timestamp < :old");
        $stmt->execute([':old' => $old]);
        return $stmt->rowCount();
    }
}

// Cria uma instância do nosso handler, passando a conexão PDO do arquivo database.php
$sessionHandler = new DatabaseSessionHandler($pdo);

// Define o nosso handler como o manipulador de sessão padrão do PHP.
session_set_save_handler($sessionHandler, true);