<?php

namespace controller;

use model\db\connector;
use model\modelDao\taskDao;
use model\modelDao\assignmentDao;
use model\mainClasses\tasks;
use model\mainClasses\priority;
use model\mainClasses\status;

require_once __DIR__ . '/../model/db/connection.php';
require_once __DIR__ . '/../model/modelDao/taskDao.php';
require_once __DIR__ . '/../model/modelDao/assignmentDao.php';
require_once __DIR__ . '/../model/mainClasses/priority.php';
require_once __DIR__ . '/../model/mainClasses/status.php';
require_once __DIR__ . '/../model/mainClasses/categories.php';
require_once __DIR__ . '/../model/mainClasses/tasks.php';

class TaskController {
    private taskDao $taskDao;
    private assignmentDao $assignmentDao;
    private connector $connector;

    public function __construct() {
        $this->connector = new connector();
        $this->taskDao = new taskDao($this->connector);
        $this->assignmentDao = new assignmentDao($this->connector);
    }

    public function getAllTasks(): array {
        return $this->taskDao->getAllTasks();
    }

    public function handleRequest(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['create_task'])) {
                $this->createTask();
            } elseif (isset($_POST['update_task'])) {
                $this->updateTask();
            } elseif (isset($_POST['assign_task'])) {
                $this->assignTask();
            } elseif (isset($_POST['unassign_task'])) {
                $this->unassignTask();
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($_GET['action']) && $_GET['action'] === 'delete_task' && isset($_GET['id'])) {
                $this->deleteTask((int)$_GET['id']);
            }
        }
    }

    private function createTask(): void {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        $priorityRaw = $_POST['priority_id'] ?? 'normal';
        $priorityMap = [
            '1' => 'low',
            '2' => 'normal',
            '3' => 'high',
            'medium' => 'normal'
        ];
        $priority = $priorityMap[$priorityRaw] ?? $priorityRaw;

        $statusRaw = $_POST['status'] ?? $_POST['status_id'] ?? 'not_started';
        $statusMap = [
            '1' => 'not_started',
            '2' => 'in_progress',
            '3' => 'done',
            'pending' => 'not_started',
            'in_progress' => 'in_progress',
            'completed' => 'done'
        ];
        $status = $statusMap[$statusRaw] ?? $statusRaw;

        $dueDate = $_POST['due_date'] ?? date('Y-m-d');
        $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        $userId = $_POST['user_id'] ?? null;

        if (empty($title)) {
            header('Location: ../view/admin/dashbord.php?error=empty_title');
            exit();
        }

        $taskData = [
            'title' => $title,
            'description' => $description,
            'priority' => $priority,
            'status' => $status,
            'due_date' => $dueDate,
            'category_id' => $categoryId
        ];

        $task = new tasks($taskData);
        $taskId = $this->taskDao->createTask($task);

        if ($taskId > 0) {
            if (!empty($userId)) {
                $this->assignmentDao->assignUserToTask($taskId, (int)$userId);
            }
            header('Location: ../view/admin/dashbord.php?success=task_created');
        } else {
            header('Location: ../view/admin/dashbord.php?error=task_create_failed');
        }
        exit();
    }

    private function updateTask(): void {
        $taskId = (int)($_POST['task_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        $priorityRaw = $_POST['priority_id'] ?? 'normal';
        $priorityMap = [
            '1' => 'low',
            '2' => 'normal',
            '3' => 'high',
            'medium' => 'normal'
        ];
        $priority = $priorityMap[$priorityRaw] ?? $priorityRaw;

        $statusRaw = $_POST['status'] ?? $_POST['status_id'] ?? 'not_started';
        $statusMap = [
            '1' => 'not_started',
            '2' => 'in_progress',
            '3' => 'done',
            'pending' => 'not_started',
            'in_progress' => 'in_progress',
            'completed' => 'done'
        ];
        $status = $statusMap[$statusRaw] ?? $statusRaw;

        $dueDate = !empty($_POST['due_date']) ? $_POST['due_date'] : date('Y-m-d');
        $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;

        $redirectPage = isset($_POST['user_id']) ? '../view/admin/users_list.php' : '../view/admin/dashbord.php';

        if ($taskId <= 0 || empty($title)) {
            header("Location: {$redirectPage}?error=empty_title");
            exit();
        }

        $taskData = [
            'id' => $taskId,
            'title' => $title,
            'description' => $description,
            'priority' => $priority,
            'status' => $status,
            'due_date' => $dueDate,
            'category_id' => $categoryId
        ];

        $task = new tasks($taskData);

        if ($this->taskDao->updateTask($task)) {
            header("Location: {$redirectPage}?success=task_updated");
        } else {
            header("Location: {$redirectPage}?error=task_update_failed");
        }
        exit();
    }

    private function assignTask(): void {
        $taskId = $_POST['task_id'] ?? null;
        $userId = $_POST['user_id'] ?? null;

        if ($taskId && $userId) {
            $this->assignmentDao->assignUserToTask((int)$taskId, (int)$userId);
            header('Location: ../view/admin/users_list.php?success=task_assigned');
        } else {
            header('Location: ../view/admin/users_list.php?error=assign_failed');
        }
        exit();
    }

    private function unassignTask(): void {
        $userId = (int)($_POST['user_id'] ?? 0);
        $taskId = (int)($_POST['task_id'] ?? 0);

        if ($userId > 0 && $taskId > 0) {
            $result = false;

                try {
                    $db = $this->connector->getConnection();
                    $stmt = $db->prepare("DELETE FROM task_assignments WHERE user_id = ? AND task_id = ?");
                    $result = $stmt->execute([$userId, $taskId]);
                } catch (\Exception $e) {
                    $result = false;
                }

            if ($result) {
                header('Location: ../view/admin/users_list.php?success=task_unassigned');
            } else {
                header('Location: ../view/admin/ users_list.php?error=unassign_failed');
            }
        } else {
            header('Location: ../view/admin/users_list.php?error=unassign_failed');
        }
        exit();
    }

    public function getTasksByUserId(int $userId): array {
        return $this->taskDao->getTaskByUserId($userId);
    }

    private function deleteTask(int $taskId): void {
        if ($this->taskDao->deleteTask($taskId)) {
            header('Location: ../view/admin/dashbord.php?success=task_deleted');
        } else {
            header('Location: ../view/admin/dashbord.php?error=delete_failed');
        }
        exit();
    }
}

$taskController = new TaskController();
$taskController->handleRequest();