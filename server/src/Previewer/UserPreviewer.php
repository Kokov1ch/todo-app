<?php

namespace App\Previewer;

use App\Entity\User;
use JetBrains\PhpStorm\ArrayShape;

class UserPreviewer
{
    #[ArrayShape([
        "id" => "int",
        "username" => "string",
        "fio" => "string",
        "email" => "string",
    ])]
    public function preview(User $user): array
    {
        return [
                "id" => $user->getId(),
                "username" => $user->getLogin(),
                "fio" => $user->getFio(),
                "email" => $user->getEmail(),
            ];
    }
}
