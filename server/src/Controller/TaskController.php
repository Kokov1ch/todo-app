<?php

namespace App\Controller;

use App\Entity\Task;
use App\Previewer\TaskPreviewer;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name:"Task")]
#[Security(name:"Bearer")]
#[Route('/tasks', name: 'tasks_')]
class TaskController extends ApiController
{
    private TaskRepository $taskRepository;
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em,
                                TaskRepository         $taskRepository)
    {
        $this->em = $em;
        $this->taskRepository = $taskRepository;
    }

    /**
     *  Get all tasks
     */
    #[OA\Response(
        response: 200,
        description: "HTTP_OK",
        content: new OA\JsonContent(
            type: "array",
            items: new OA\Items(ref: "#/components/schemas/TaskView")
        )
    )
    ]
    #[OA\Response(
        response: 403,
        description: "Permission denied"
    )]
    #[Route(name: 'get', methods: ['GET'])]
    public function getTasks(TaskPreviewer $taskPreviewer): JsonResponse
    {
        $tasks = $this->taskRepository->findAll();
        $this->setSoftDeleteable($this->em, false);

        $taskPreviews = array_map(
            fn(Task $task): array => $taskPreviewer->preview($task),
            $tasks
        );

        return $this->response($taskPreviews);
    }
}