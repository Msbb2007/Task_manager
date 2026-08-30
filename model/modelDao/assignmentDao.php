<?php

namespace model\modelDao;

use model\db\connector;
use model\mainClasses\task_assignments;

class assignmentDao {
    private \PDO $db;

    public function __construct(Connector $connector) {
        $this->db = $connector->getConnection();
    }

    public function assignUserToTask(int $taskId, int $userId): bool {
        $sql = "INSERT INTO task_assignments (task_id, user_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$taskId, $userId]);
    }

    public function removeUserFromTask(int $taskId, int $userId): bool {
        $sql = "DELETE FROM task_assignments WHERE task_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$taskId, $userId]);
    }

    public function getUsersByTaskId(int $taskId): array {
        $sql = "SELECT u.* FROM users u 
                JOIN task_assignments ta ON u.id = ta.user_id 
                WHERE ta.task_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$taskId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
