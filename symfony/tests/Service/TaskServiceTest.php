<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Service\TaskService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;

class TaskServiceTest extends TestCase
{
    /**
     * @dataProvider taskProvider
     */
    public function testAddTask(string $title, string $description, array $localizedData)
    {
        /** @var TranslationRepository $repository */
        $repository = $this->createMock(TranslationRepository::class);

        $manager = $this->createMock(EntityManager::class);

        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($repository);

        /** @var EntityManager $manager */
        $service = new TaskService($manager);

        $task = new Task();
        $task->setTitle($title);
        $task->setDescription($description);

        $service->addTask($task, $localizedData);

        $this->assertEquals($title, $task->getTitle());
        $this->assertEquals($description, $task->getDescription());
        $this->assertEquals(Task::STATUS_TODO, $task->getStatus());
    }

    public function taskProvider()
    {
        return [
            ['titleEn', 'descriptionEn', []],
            ['titleEn', 'descriptionEn', ['pl' => ['title' => 'titlePl', 'description' => 'descriptionPl']]],
            [
                'titleEn',
                'descriptionEn',
                [
                    'pl' => ['title' => 'titlePl', 'description' => 'descriptionPl'],
                    'de' => ['title' => 'titleDe', 'description' => 'descriptionDe'],
                ]
            ],
        ];
    }

    public function testUpdateStatusOfNotExistingTask()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Task with provided id: 1 does not exist');

        /** @var TranslationRepository $repository */
        $repository = $this->createMock(TranslationRepository::class);

        $manager = $this->createMock(EntityManager::class);

        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($repository);

        /** @var EntityManager $manager */
        $service = new TaskService($manager);

        $service->updateStatus(1, Task::STATUS_COMPLETED);
    }

    public function testUpdateStatus()
    {
        $task = new Task();
        $task->setTitle('titleEn');
        $task->setDescription('descriptionEn');
        $task->setStatus(Task::STATUS_TODO);

        $repository = $this->createMock(TranslationRepository::class);
        $repository
            ->expects($this->once())
            ->method('find')
            ->willReturn($task);

        $manager = $this->createMock(EntityManager::class);
        $manager
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($repository);

        /** @var TranslationRepository $repository */
        /** @var EntityManager $manager */
        $service = new TaskService($manager);

        $task = $service->updateStatus(1, Task::STATUS_COMPLETED);

        $this->assertEquals(Task::STATUS_COMPLETED, $task->getStatus());
    }
}
