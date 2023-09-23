<?php

namespace App\Schema;

use DateTimeInterface;
use App\Entity\User;
use App\Entity\Task;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\Property;
use Symfony\Component\Serializer\Annotation\Groups;


//TODO: разобраться с форматированием даты
class TaskView
{
    #[OA\Property(property: "id", ref : "#/components/schemas/Task/properties/id")]
    #[Groups("default", "id")]
    public int $id;

    #[Property(example: "Make coffee")]
    #[OA\Property(property: "name", ref : "#/components/schemas/Task/properties/name")]
    #[Groups("default", "name")]
    public string $name;

    #[Property(example: "Latte with caramel syrup")]
    #[OA\Property(property: "description", ref : "#/components/schemas/Task/properties/description")]
    #[Groups("default", "description")]
    public string $description;

    #[OA\Property(property: "user_id", ref : "#/components/schemas/User/properties/id")]
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