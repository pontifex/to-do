<?php

namespace App\Service;

use App\Entity\Task;
use Doctrine\ORM\EntityManager;
use Gedmo\Translatable\Entity\Translation;

class TaskService
{
    /** @var EntityManager */
    private $manager;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addTask(Task $task, array $localizedData)
    {
        $task->setStatus(Task::STATUS_TODO);
        $task->setCreatedAt(new \DateTime());
        $task->setUpdatedAt(new \DateTime());

        /** @var \Gedmo\Translatable\Entity\Repository\TranslationRepository $repository */
        $repository = $this->manager->getRepository(Translation::class);

        foreach ($localizedData as $locale => $localeData) {
            if (!empty($localeData['title']) || !empty($localeData['description'])) {
                $repository->translate($task, 'title', $locale, $localeData['title']);
                $repository->translate($task, 'description', $locale, $localeData['description']);
            }
        }

        $this->manager->persist($task);
        $this->manager->flush();
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateStatus(string $taskId, string $status): Task
    {
        $repository = $this->manager->getRepository(Task::class);

        $task = $repository->find($taskId);

        if (! $task instanceof Task) {
            throw new \LogicException('Task with provided id: ' . $taskId . ' does not exist');
        }

        /* @var Task $task */
        $task->setStatus($status);
        $this->manager->persist($task);
        $this->manager->flush();

        return $task;
    }
}
