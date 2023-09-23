<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Previewer\TaskPreviewer;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;

#[OA\Tag(name:"Task")]
#[Security(name:"Bearer")]
#[Route('/tasks', name: 'tasks_')]
class TaskController extends ApiController
{
    private TaskRepository $taskRepository;
    private UserRepository $userRepository;
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em,
                                TaskRepository         $taskRepository,
                                UserRepository         $userRepository)
    {
        $this->em = $em;
        $this->taskRepository = $taskRepository;
        $this->userRepository = $userRepository;
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
            fn(Task $task): array => $taskPreviewer->previewWithUserId($task),
            $tasks
        );

        return $this->response($taskPreviews);
    }

    /**
     *  Add a new task
     */
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties:[
                new OA\Property(property: "name", ref:"#/components/schemas/TaskView/properties/name"),
                new OA\Property(property: "description", ref:"#/components/schemas/TaskView/properties/description"),
                new OA\Property(property: "user_id",  ref:"#/components/schemas/User/properties/id"),
                new OA\Property(property: "start_date", ref:"#/components/schemas/TaskView/properties/start_date", nullable:true),
                new OA\Property(property:"end_date", ref:"#/components/schemas/TaskView/properties/end_date", nullable:true)
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Task added successfully"
    )]
    #[OA\Response(
        response: 403,
        description: "Permission denied"
    )]
    #[OA\Response(
        response: 422,
        description: "Unprocessable Content"
    )]
    #[Route(name: 'post', methods: ['POST'])]
    public function postTask(Request $request,): JsonResponse
    {
        $request = $request->toArray();
        $this->setSoftDeleteable($this->em, false);
        $task = $this->taskRepository->findOneBy(['name' => $request['name']]);

        if ($task) {
            if (!$task->getDeletedAt()) {
                return $this->respondValidationError("Task with this name is already exist");
            }
            else $task->setDeletedAt(null);
        }

        $user = $this->userRepository->findOneBy(['id' => $request['user_id']]);

        if (!$user || $user->getDeletedAt()){
            return $this->respondNotFound("User is not found");
        }

        $task = $task ?? new Task();
        try {
            $task
                ->setName($request['name'])
                ->setUser($user)
                ->setDone(false);

            if(isset($request['description'])) {
                $task->setDescription($request['description']);
            }

            if(isset($request['start_date'])) {
                $task->setStartDate(new DateTime($request['start_date']));
            }

            if(isset($request['end_date'])) {
                $task->setEndDate(new DateTime($request['end_date']));
            }

            $this->em->persist($task);
            $this->em->flush();

            return $this->respondWithSuccess("Task added successfully");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }
}
