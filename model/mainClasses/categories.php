<?php

namespace model\mainClasses;

class categories {
    private int $id;
    private string $name;

    public function __construct(array $data = []) {
        $this->id          = $data['id'] ?? 0;
        $this->name        = $data['name'] ?? 'بدون نام';
    }

    //Getterها
    public function getId(): int { 
        return $this->id; }

    public function getName(): string { 
        return $this->name; }

    //Setterها 
    public function setId(int $id): void { 
        $this->id = $id; }

    public function setName(string $name): void { 
        $this->name = $name; }
}
