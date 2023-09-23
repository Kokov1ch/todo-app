<?php

namespace App\Schema;

use DateTimeInterface;
use App\Entity\User;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

class TaskView
{
    #[OA\Property(property: "id", ref : "#/components/schemas/Task/properties/id")]
    #[Groups("default", "id")]
    public int $id;


    #[OA\Property(property: "name", ref : "#/components/schemas/Task/properties/name")]
    #[Groups("default", "name")]
    public string $name;

    #[OA\Property(property: "description", ref : "#/components/schemas/Task/properties/description")]
    #[Groups("default", "description")]
    public string $description;

    #[OA\Property(property: "user", ref : "#/components/schemas/Task/properties/user")]
    #[Groups("default", "user")]
    public User $user;

    #[OA\Property(property: "start_date", ref : "#/components/schemas/Task/properties/startDate")]
    #[Groups("default", "start_date")]
    public DateTimeInterface $startDate;


    #[OA\Property(property: "end_date", ref : "#/components/schemas/Task/properties/endDate")]
    #[Groups("default", "end_date")]
    public DateTimeInterface $endDate;


    #[OA\Property(property: "done", ref : "#/components/schemas/Task/properties/done")]
    #[Groups("default", "done")]
    public bool $done;

}