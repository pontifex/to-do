<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Gedmo\Translatable\Entity\Translation;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $this->loadTags($manager);
    }

    private function loadTags(ObjectManager $manager)
    {
        /** @var \Gedmo\Translatable\Entity\Repository\TranslationRepository $repository */
        $repository = $manager->getRepository(Translation::class);

        foreach ($this->getTaskData() as [$titleEn, $titlePl, $descriptionEn, $descriptionPl, $publishedAt, $status]) {
            $task = new Task();
            $task->setTranslatableLocale('en');
            $task->setTitle($titleEn);
            $task->setDescription($descriptionEn);
            $task->setStatus($status);
            $task->setPublishedAt($publishedAt);

            $repository->translate($task, 'title', 'pl', $titlePl);
            $repository->translate($task, 'description', 'pl', $descriptionPl);

            $manager->persist($task);
        }

        $manager->flush();
    }

    private function getTaskData()
    {
        return [
            [
                'First task title',
                'Tytuł pierwszego zadania',
                'Description of first task goes here.',
                'Opis pierwszego zadania umieszczę tutaj',
                new \DateTime(),
                Task::STATUS_COMPLETED
            ],
            [
                'Second task title',
                'Tytuł drugiego zadania',
                'Description of second task goes here.',
                'Opis drugiego zadania umieszczę tutaj',
                new \DateTime(),
                Task::STATUS_IN_PROGRESS
            ],
            [
                'Third task title',
                'Tytuł trzeciego zadania',
                'Description of third task goes here.',
                'Opis trzeciego zadania umieszczę tutaj',
                new \DateTime(),
                Task::STATUS_TODO
            ],
            [
                'Fourth task title',
                'Tytuł czwartego zadania',
                'Description of fourth task goes here.',
                'Opis czwartego zadania umieszczę tutaj',
                new \DateTime(),
                Task::STATUS_TODO
            ],
            [
                'Fifth task title',
                'Tytuł piątego zadania',
                'Description of fifth task goes here.',
                'Opis piatego zadania umieszczę tutaj',
                new \DateTime(),
                Task::STATUS_TODO
            ],
        ];
    }
}
