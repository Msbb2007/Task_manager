<?php

namespace model\modelDao;

use model\mainClasses\tasks;
use model\db\connector;
use model\mainClasses\status;

class taskDao{
    private \PDO $db;

    public function __construct(Connector $connector) {
        $this->db = $connector->getConnection();
    }
    public function createTask(tasks $task): bool {
        $sql = "INSERT INTO tasks (title, description, priority, status, due_date, category_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $task->getTitle(),
            $task->getDescription(),
            $task->getPriority()->value,
            $task->getStatus()->value,
            $task->getDueDate(),
            $task->getCategoryId()
        ]);
    }
    public function updateTask(tasks $task): bool {
        $sql = "UPDATE tasks SET title = ?, description = ?, priority = ?, status = ?, due_date = ?, category_id = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $task->getTitle(),
            $task->getDescription(),
            $task->getPriority()->value,
            $task->getStatus()->value,
            $task->getDueDate(),
            $task->getCategoryId(),
            $task->getId()
        ]);
    }
    public function deleteTask(tasks $task): bool {
        $sql = "DELETE FROM tasks WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$task->getId()]);
    }
    public function getTaskById(int $id): ?tasks {
        $sql = "SELECT * FROM tasks WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $result ? new tasks($result) : null;
    }

    public function getTaskByUserId(int $userId): array {
        $sql = "SELECT t.* FROM tasks t
                JOIN task_assignments ta ON t.id = ta.task_id
                WHERE ta.user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        
        $tasks = [];
        while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $tasks[] = new tasks($result);
        }
        return $tasks;
    }
    public function getTasksByStatus(int $userId, status $status): array {
        $sql = "SELECT t.* FROM tasks t
                JOIN task_assignments ta ON t.id = ta.task_id
                WHERE ta.user_id = ? AND t.status = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $status->cases()]);
        
        $tasks = [];
        while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $tasks[] = new tasks($result);
        }
        return $tasks;
    }

}