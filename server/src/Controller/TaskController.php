<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Previewer\TaskPreviewer;
use App\Previewer\UserPreviewer;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    /**
     *  Add a new task
     */
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties:[
                new OA\Property(property: "name", ref:"#/components/schemas/TaskView/properties/name"),
                new OA\Property(property: "description", ref:"#/components/schemas/TaskView/properties/description"),
                new OA\Property(property: "start_date", ref:"#/components/schemas/TaskView/properties/start_date", nullable:true),
                new OA\Property(property:"end_date", ref:"#/components/schemas/TaskView/properties/end_date", nullable:true),
                new OA\Property(property: "user",  ref:"#/components/schemas/TaskView/properties/user"),
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
    public function postTask(Request $request, UserRepository $userRepository): JsonResponse
    {
        //TODO: Проверять таску на уникальность, а не только название
        $request = $request->toArray();
        $this->setSoftDeleteable($this->em, false);
        $task = $this->taskRepository->findOneBy(['name' => $request['name']]);

//        if ($task) {
//            if (!$task->getDeletedAt()) {
//                return $this->respondValidationError("Task with this name is already exist");
//            }
//            else $task->setDeletedAt(null);
//        }

        $user = $userRepository->findOneBy(['id' => $request['user_id']]);

        if (!$user || $user->getDeletedAt()){
            return $this->respondNotFound("User is not found");
        }

        $task = new Task();
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

    /**
     *  Delete multiple tasks
     */
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties:[
                new OA\Property(property: "task_ids",  type: "array", items: new OA\Items(ref: "#/components/schemas/Task/properties/id"))
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Tasks deleted successfully"
    )]
    #[OA\Response(
        response: 403,
        description: "Permission denied"
    )]
    #[OA\Response(
        response: 404,
        description: "User is not found"
    )]
    #[Route(name: 'delete', methods: ['DELETE'])]
    public function deleteTasks(Request $request): JsonResponse
    {
        // TODO: Сделать каскадное удаление юзеров

        $request = $request->toArray();

        try {
            $taskIds = $request['task_ids'];

            $this->em->beginTransaction();
            foreach ($taskIds as $taskId) {
                $task = $this->taskRepository->find($taskId);
                if (!$task) {
                    $this->em->rollback();
                    return $this->respondNotFound("Task is not found");
                }
                $this->em->remove($task);
            }
            $this->em->flush();
            $this->em->commit();

            return $this->respondWithSuccess("Tasks deleted successfully");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    /**
     *  Get task by id
     */
    #[OA\Response(
        response: 200,
        description: "HTTP_OK",
        content: new OA\JsonContent(ref: "#/components/schemas/TaskView")
    )]
    #[OA\Response(
        response: 403,
        description: "Permission denied"
    )]
    #[OA\Response(
        response: 404,
        description: "Task is not found"
    )]
    #[Route('/{taskId}', name: 'get_by_id', requirements: ['taskId' => '\d+'], methods: ['GET'])]
    public function getUserById(TaskPreviewer $taskPreviewer, int $taskId): JsonResponse
    {
        $task = $this->taskRepository->find($taskId);
        if (!$task) {
            return $this->respondNotFound("Task is not found");
        }

        $this->setSoftDeleteable($this->em, false);

        return $this->response($taskPreviewer->preview($task));
    }

    /**
     *  Change task data
     */
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties:[
                new OA\Property(property: "name", ref:"#/components/schemas/TaskView/properties/name"),
                new OA\Property(property: "description", ref:"#/components/schemas/TaskView/properties/description", nullable:true),
                new OA\Property(property: "start_date", ref:"#/components/schemas/TaskView/properties/start_date", nullable:true),
                new OA\Property(property: "end_date", ref:"#/components/schemas/TaskView/properties/end_date", nullable:true),
                new OA\Property(property: "done", ref:"#/components/schemas/TaskView/properties/done"),
                new OA\Property(property: "user",  ref:"#/components/schemas/TaskView/properties/user")
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
        response: 404,
        description: "Task if not found"
    )]
    #[OA\Response(
        response: 422,
        description: "Unprocessable Content"
    )]
    #[Route('/{taskId}', name: 'put_by_id', requirements: ['taskId' => '\d+'], methods: ['PUT'])]
    public function updateTask(Request        $request,
                               int            $taskId,
                               UserRepository $userRepository): JsonResponse
    {
        $task = $this->taskRepository->find($taskId);
        if (!$task ) {
            return $this->respondNotFound("Task is not found");
        }

        $request = $request->toArray();
        try {
            if (isset($request['name'])) {
                $name = $request['name'];
                if (!$name) {
                    throw new Exception();
                }
                $task->setName($name);
                }

            if (isset($request['description'])) {
                $task->setDescription($request['description']);
            }

            if (isset($request['user_id'])) {
                if (!$userRepository->find($request['user_id'])) {
                    throw new Exception();
                }
                $task->setUser($this->em->getReference(User::class, $request['user_id']));
            }

            if (isset($request['start_date'])) {
                $task->setStartDate(new DateTime($request['start_date']));
            }

            if (isset($request['end_date'])) {
                $task->setStartDate(new DateTime($request['end_date']));
            }

            $this->em->flush();

            return $this->respondWithSuccess("Task updated successfully");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    /**
     *  Delete task by id
     */
    #[OA\Response(
        response: 200,
        description: "Task deleted successfully"
    )]
    #[OA\Response(
        response: 403,
        description: "Permission denied"
    )]
    #[OA\Response(
        response: 404,
        description: "Task is not found"
    )]
    #[Route('/{taskId}', name: 'delete_by_id', requirements: ['taskId' => '\d+'], methods: ['DELETE'])]
    public function deleteTask(int $taskId): JsonResponse
    {
        // TODO: Сделать каскадное удаление тасков

        $task = $this->taskRepository->find($taskId);
        if (!$task) {
            return $this->respondNotFound("Task is not found");
        }

        $this->em->remove($task);
        $this->em->flush();

        return $this->respondWithSuccess("Task deleted successfully");
    }

    /**
     *  Recover task by id
     */
    #[OA\Response(
        response: 200,
        description: "Task recovered successfully"
    )]
    #[OA\Response(
        response: 403,
        description: "Permission denied"
    )]
    #[OA\Response(
        response: 404,
        description: "Task is not found"
    )]
    #[OA\Response(
        response: 422,
        description: "Task is not deleted"
    )]
    #[Route('/recover/{taskId}', name: 'recover_by_id', requirements: ['taskId' => '\d+'], methods: ['PUT'])]
    public function recoverTask(int $taskId): JsonResponse
    {
        $this->setSoftDeleteable($this->em, false);
        $task = $this->taskRepository->find($taskId);

        if (!$task) {
            return $this->respondNotFound("Task is not found");
        }

        if (!$task->getDeletedAt()) {
            return $this->respondValidationError("Task is not deleted");
        }

        $task->setDeletedAt(null);
        $this->em->flush();
        return $this->respondWithSuccess("Task recovered successfully");
    }

    /**
     *  Get all tasks by user id
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
    #[OA\Response(
        response: 404,
        description: "Task is not found"
    )]
    #[Route('/user/{userId}', name: 'get_by_user', requirements: ['userId' => '\d+'], methods: ['GET'])]
    public function getTasksUser(TaskPreviewer $taskPreviewer, int $userId): JsonResponse
    {
        $this->setSoftDeleteable($this->em, false);
        $tasks = $this->taskRepository->findBy(["user" => $userId]);

        $taskPreviews = array_map(
            fn(Task $task): array => $taskPreviewer->preview($task),
            $tasks
        );

        return $this->response($taskPreviews);
    }

}
