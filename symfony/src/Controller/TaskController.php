<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller used to manage tasks
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/", defaults={"page": "1", "_format"="html"}, name="task_index")
     * @Route("/page/{page}", defaults={"_format"="html"}, requirements={"page": "[1-9]\d*"}, name="task_index_paginated")
     * @Method("GET")
     * @Cache(smaxage="10")
     */
    public function index(int $page, string $_format, TaskRepository $tasks): Response
    {
        $latestTasks = $tasks->findLatest($page);

        return $this->render('task/index.'.$_format.'.twig', ['tasks' => $latestTasks]);
    }

    /**
     * @Route("/tasks/{id}", name="task_show")
     * @Method("GET")
     */
    public function taskShow(Task $task): Response
    {
        return $this->render('task/show.html.twig', ['task' => $task]);
    }
}
