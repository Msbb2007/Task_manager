<?php

namespace model\mainClasses;

use DateTime;

class task_assignments {
    private int $id;
    private int $task_id;
    private int $user_id;
    private DateTime $assigned_at;

    public function __construct(array $data = []) {
        $this->id          = $data['id'] ?? 0;
        $this->task_id     = $data['task_id'] ?? 0;
        $this->user_id     = $data['user_id'] ?? 0;
        $this->assigned_at = isset($data['assigned_at']) ? new DateTime($data['assigned_at']) : new DateTime();
    }

    //Getterها
    public function getId(): int { 
        return $this->id; }
    public function getTaskId(): int { 
        return $this->task_id; }
    public function getUserId(): int { 
        return $this->user_id; }
    public function getAssignedAt(): DateTime { 
        return $this->assigned_at; }

    //Setterها
    public function setId(int $id): void { 
        $this->id = $id; }
    public function setTaskId(int $task_id): void { 
        $this->task_id = $task_id; }
    public function setUserId(int $user_id): void { 
        $this->user_id = $user_id; }
    public function setAssignedAt(DateTime $assigned_at): void { 
        $this->assigned_at = $assigned_at; }
}
