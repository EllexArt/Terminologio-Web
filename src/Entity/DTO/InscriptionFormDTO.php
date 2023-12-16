<?php

namespace App\Entity\DTO;

class InscriptionFormDTO {
    protected string $user_name;
    protected string $user_mail;

    protected string $user_passwd;

    protected string $user_passwd_confirm;



    public function getUserName(): string
    {
        return $this->user_name;
    }

    public function setUserName(string $user_name): void
    {
        $this->user_name = $user_name;
    }

    public function getUserMail(): string
    {
        return $this->user_mail;
    }

    public function setUserMail(string $user_mail): void
    {
        $this->user_mail = $user_mail;
    }

    public function getUserPasswd(): string
    {
        return $this->user_passwd;
    }

    public function setUserPasswd(string $user_passwd): void
    {
        $this->user_passwd = $user_passwd;
    }

    public function getUserPasswdConfirm(): string
    {
        return $this->user_passwd_confirm;
    }

    public function setUserPasswdConfirm(string $user_passwd_confirm): void
    {
        $this->user_passwd_confirm = $user_passwd_confirm;
    }


}