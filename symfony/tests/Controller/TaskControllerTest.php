<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    /**
     * @group functional
     */
    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/');

        $this->assertCount(
            Task::NUM_ITEMS,
            $crawler->filter('article.post'),
            'The homepage displays the right number of posts.'
        );
    }

    /**
     * @group functional
     */
    public function testTaskShow()
    {
        $client = static::createClient();

        /** @var Task $task */
        $task = $client->getContainer()->get('doctrine')->getRepository(Task::class)->find(1);
        $client->request('GET', sprintf('/en/tasks/%d', $task->getId()));

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}
