<?php

namespace App\Previewer;

use App\Entity\Task;
use JetBrains\PhpStorm\ArrayShape;

class TaskPreviewer
{
    private UserPreviewer $userPreviewer;

    public function __construct(UserPreviewer $userPreviewer)
    {
        $this->userPreviewer = $userPreviewer;
    }
    #[ArrayShape([
        "id" => "int",
        "name" => "string",
        "description" => "string",
        "start_date" => "date",
        "end_date" => "date",
        "done" => "bool",
            "user" => [
                "id" => "int",
                "login" => "string",
                "fio" => "string",
                "email" => "string",
            ]
    ])]

    public function preview(Task $task): array
    {
        $user = $task->getUser();

        return array_merge([
            "id" => $task->getId(),
            "name" => $task->getName(),
            "description" => $task->getDescription(),
            "start_date" => $task->getStartDate(),
            "end_date" => $task->getEndDate(),
            "done" => $task->isDone(),
        ], [
            "user" => $this->userPreviewer->preview($user)
        ]
        );
    }

    public function previewWithUserId(Task $task): array
    {
        $user = $task->getUser();

        return array_merge([
            "id" => $task->getId(),
            "name" => $task->getName(),
            "description" => $task->getDescription(),
            "user_id" => $user->getId(),
            "start_date" => $task->getStartDate(),
            "end_date" => $task->getEndDate(),
            "done" => $task->isDone(),
        ]
        );
    }
}