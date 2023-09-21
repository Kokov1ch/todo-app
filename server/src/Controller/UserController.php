<?php

namespace App\Controller;

use App\Entity\User;
use App\Previewer\TaskPreviewer;
use App\Previewer\UserPreviewer;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class UserController extends ApiController
{
    private TaskRepository $taskRepository;
    private TaskPreviewer $taskPreviewer;

    public function __construct(TaskRepository $taskRepository,
                                TaskPreviewer $taskPreviewer)
    {
        $this->taskRepository = $taskRepository;
        $this->taskPreviewer = $taskPreviewer;
    }

    #[Route('/test')]
    public function index(UserRepository $userRepository, TaskRepository $taskRepository): JsonResponse
    {
        $task = $this->taskRepository->find(1);
        return $this->response($this->taskPreviewer->preview($task), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }
}
