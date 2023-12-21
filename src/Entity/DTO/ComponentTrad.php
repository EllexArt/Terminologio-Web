<?php

namespace App\Entity\DTO;

class ComponentTrad
{
    public function __construct($id, $number, $name, $positionX, $positionY) {
        $this->id = $id;
        $this->number = $number;
        $this->name = $name;
        $this->positionX = $positionX;
        $this->positionY = $positionY;
    }

    private int $id;

    private int $positionX;

    private int $positionY;

    private string $name;

    private int $number;

    public function getPositionX(): int
    {
        return $this->positionX;
    }

    public function setPositionX(int $positionX): void
    {
        $this->positionX = $positionX;
    }

    public function getPositionY(): int
    {
        return $this->positionY;
    }

    public function setPositionY(int $positionY): void
    {
        $this->positionY = $positionY;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setNumber(int $number): void
    {
        $this->number = $number;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}