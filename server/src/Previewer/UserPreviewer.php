<?php

namespace App\Previewer;

use App\Entity\User;
use JetBrains\PhpStorm\ArrayShape;

class UserPreviewer
{
    #[ArrayShape([
        "id" => "int",
        "login" => "string",
        "fio" => "string",
        "email" => "string",
        "tasks" => [
            "id" => "int",
            "name" => "string",
            "description" => "string|null",
            "startDate" => "string|null",
            "endDate" => "string|null",
            "done" => "bool|null",
        ]
    ])]
    public function preview(User $user): array
    {
        return [
                "id" => $user->getId(),
                "login" => $user->getLogin(),
                "fio" => $user->getFio(),
                "email" => $user->getEmail(),
            ];
    }
}