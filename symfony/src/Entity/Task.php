<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 * @ORM\Table(name="todo_task")
 */
class Task implements Translatable
{
    public const NUM_ITEMS = 3;

    public const STATUS_TODO = 'Todo';
    public const STATUS_IN_PROGRESS = 'In Progress';
    public const STATUS_COMPLETED = 'Completed';

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=180)
     * @Gedmo\Translatable
     * @Assert\Length(
     *      min = 2,
     *      max = 180
     * )
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @Gedmo\Translatable
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(type="string", columnDefinition="ENUM('Todo', 'In Progress', 'Completed')")
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @Assert\DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @Assert\DateTime
     */
    private $updatedAt;

    /**
     * @Gedmo\Locale
     */
    private $locale;

    public function getId(): int
    {
        return $this->id;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        if (!in_array($status, [self::STATUS_TODO, self::STATUS_IN_PROGRESS, self::STATUS_COMPLETED], true)) {
            throw new \InvalidArgumentException('Invalid status');
        }

        $this->status = $status;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }
}
