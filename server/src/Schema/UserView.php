<?php

namespace App\Schema;

use OpenApi\Attributes as OA;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Annotation\Groups;

class UserView
{

    #[OA\Property(property: "id", ref : "#/components/schemas/User/properties/id")]
    #[Groups("default", "id")]
    public int $id;

    #[Property(example: "login1")]
    #[OA\Property(property: "username", ref : "#/components/schemas/User/properties/username")]
    #[Groups("default", "username")]
    public string $username;

    #[Property(example: "Харитонов Игнатий Иванович")]
    #[OA\Property(property: "fio", ref : "#/components/schemas/User/properties/fio")]
    #[Groups("default", "fio")]
    public string $fio;

    #[Property(example: "fedorFet@mail.ru")]
    #[OA\Property(property: "email", ref : "#/components/schemas/User/properties/email")]
    #[Groups("default", "email")]
    public string $email;

    #[OA\Property(property: "roles", ref : "#/components/schemas/User/properties/roles")]
    #[Groups("default", "roles")]
    public array $roles;

}
