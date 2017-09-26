<?php

namespace Fintem\UserTaskBundle\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Fintem\UserTaskBundle\Entity\Traits\UUIDTrait;
use Fintem\UserTaskBundle\Utils\Dates;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class TaskAssignee.
 *
 * @ORM\Entity
 * @ORM\Table
 * @ORM\HasLifecycleCallbacks
 */
class TaskAssignee
{
    use UUIDTrait;

    /**
     * @var DateTimeImmutable
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $assignedAt;
    /**
     * @var Task
     * @ORM\ManyToOne(targetEntity="Fintem\UserTaskBundle\Entity\Task", inversedBy="assignees")
     * @ORM\JoinColumn(name="taskId", referencedColumnName="id")
     */
    private $task;
    /**
     * @var DateTimeImmutable
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $unassignedAt;
    /**
     * @var TaskUserInterface
     * @ORM\ManyToOne(targetEntity="Fintem\UserTaskBundle\Entity\TaskUserInterface")
     */
    private $user;

    /**
     * @return DateTimeImmutable
     */
    public function getAssignedAt() : DateTimeImmutable
    {
        return Dates::toImmutable($this->assignedAt);
    }

    /**
     * @param DateTimeImmutable $assignedAt
     *
     * @return $this
     */
    public function setAssignedAt(DateTimeImmutable $assignedAt)
    {
        $this->assignedAt = $assignedAt;

        return $this;
    }

    /**
     * @return Task
     */
    public function getTask() : Task
    {
        return $this->task;
    }

    /**
     * @param Task $task
     *
     * @return $this
     */
    public function setTask(Task $task)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getUnassignedAt()
    {
        return $this->unassignedAt ? Dates::toImmutable($this->unassignedAt) : null;
    }

    /**
     * @return TaskUserInterface
     */
    public function getUser() : TaskUserInterface
    {
        return $this->user;
    }

    /**
     * @param TaskUserInterface $user
     *
     * @return $this
     */
    public function setUser(TaskUserInterface $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @internal Use Task::unassign
     *
     * @return $this
     */
    public function unassign()
    {
        $this->unassignedAt = new \DateTime();

        return $this;
    }
}
