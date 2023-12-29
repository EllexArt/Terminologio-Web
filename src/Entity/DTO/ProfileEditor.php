<?php

namespace App\Entity\DTO;

class ProfileEditor
{
    private string $fieldToEdit;

    private string $plainPassword;

    public function getFieldToEdit(): string
    {
        return $this->fieldToEdit;
    }

    public function setFieldToEdit(string $fieldToEdit): void
    {
        $this->fieldToEdit = $fieldToEdit;
    }

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }
}