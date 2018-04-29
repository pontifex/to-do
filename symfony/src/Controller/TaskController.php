<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Gedmo\Translatable\Entity\Translation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller used to manage tasks
 */
class TaskController extends AbstractController
{
    /** @var string */
    private $defaultLocale;

    /** @var string */
    private $locales;

    public function __construct(string $defaultLocale, string $locales)
    {
        $this->defaultLocale = $defaultLocale;
        $this->locales = $locales;
    }

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

    /**
     * Creates a new Task entity.
     *
     * @Route("/new", name="task_new")
     * @Method({"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $locales = explode('|', $this->locales);

        $task = new Task();
        $task->setTranslatableLocale('en');

        $form = $this->createForm(TaskType::class, $task,
            [
                'locales' => $locales,
                'defaultLocale' => $this->defaultLocale
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();

            $task->setStatus(Task::STATUS_TODO);
            $task->setCreatedAt(new \DateTime());
            $task->setUpdatedAt(new \DateTime());

            /** @var \Gedmo\Translatable\Entity\Repository\TranslationRepository $repository */
            $repository = $manager->getRepository(Translation::class);

            foreach ($locales as $locale) {
                if ($locale != $this->defaultLocale) {
                    $titleLocale = $form->get('title_' . $locale)->getData();
                    $descriptionLocale = $form->get('description_' . $locale)->getData();

                    if (! empty($titleLocale) || ! empty($descriptionLocale)) {
                        $repository->translate($task, 'title', $locale, $titleLocale);
                        $repository->translate($task, 'description', $locale, $descriptionLocale);
                    }
                }
            }

            $manager->persist($task);
            $manager->flush();

            $this->addFlash('success', 'task.created_successfully');

            return $this->redirectToRoute('task_index');
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
            'locales' => $locales,
            'defaultLocale' => $this->defaultLocale
        ]);
    }

    /**
     * Updates status of Task entity.
     *
     * @Route("/tasks/{taskId}", name="task_update_status")
     * @Method({"PATCH"})
     */
    public function updateStatus(int $taskId, Request $request)
    {
        if ($request->request->get('status') === Task::STATUS_COMPLETED) {
            $manager = $this->getDoctrine()->getManager();
            $repository = $manager->getRepository(Task::class);

            $task = $repository->find($taskId);

            if ($task instanceof Task) {
                /** @var Task $task */
                $task->setStatus(Task::STATUS_COMPLETED);
                $manager->persist($task);
                $manager->flush();

                return new JsonResponse(
                    ['status' => 'success']
                );
            }
        }

        return new JsonResponse(
            [
                'status' => 'error',
                'messages' => [
                    'status' => 'Not valid or missing'
                ]
            ]
        );
    }
}
