<?php

namespace App\Entity\DTO;

class PasswordEditor
{
    private string $newPassword;
    private string $confirmNewPassword;
    private string $oldPassword;

    public function getNewPassword(): string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    public function getConfirmNewPassword(): string
    {
        return $this->confirmNewPassword;
    }

    public function setConfirmNewPassword(string $confirmNewPassword): void
    {
        $this->confirmNewPassword = $confirmNewPassword;
    }

    public function getOldPassword(): string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }


}