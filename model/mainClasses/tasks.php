<?php

namespace model\mainClasses;

use DateTime;
use model\mainClasses\priority;
use model\mainClasses\status;

class tasks{
    private int $id;
    private string $title;
    private string $description;
    private priority $priority;
    private status $status;
    private DateTime $due_date;
    private int $category_id; 
    private DateTime $created_at;

    public function __construct(array $data = []) {
        $this->id          = $data['id'] ?? 0;
        $this->title       = $data['title'] ?? 'بدون عنوان';
        $this->description = $data['description'] ?? '';
        $this->category_id = $data['category_id'] ?? 0;
        $this->priority = isset($data['priority']) ? priority::from($data['priority']) : priority::Normal;
        $this->status   = isset($data['status']) ? status::from($data['status']) : status::NotStarted;
        $this->due_date   = isset($data['due_date']) ? new DateTime($data['due_date']) : new DateTime();
        $this->created_at = isset($data['created_at']) ? new DateTime($data['created_at']) : new DateTime();
    }

    //getterها
   public function getId(): int { 
    return $this->id; }

   public function getTitle(): string { 
    return $this->title; }

   public function getDescription(): string { 
    return $this->description; }

   public function getPriority(): priority { 
    return $this->priority; }

   public function getStatus(): status { 
    return $this->status; }

   public function getDueDate(): DateTime { 
    return $this->due_date; }

   public function getCategoryId(): int { 
    return $this->category_id; }

   public function getCreatedAt(): DateTime { 
    return $this->created_at; }

    //setter ها
    public function setId(int $id): void { 
    $this->id = $id; }

    public function setTitle(string $title): void { 
    $this->title = $title; }

    public function setDescription(string $description): void { 
    $this->description = $description; }

    public function setPriority(priority $priority): void { 
    $this->priority = $priority; }
    
    public function setStatus(status $status): void { 
    $this->status = $status; }

    public function setDueDate(DateTime $dueDate): void { 
    $this->due_date = $dueDate; }

    public function setCategoryId(int $categoryId): void { 
    $this->category_id = $categoryId; }
}