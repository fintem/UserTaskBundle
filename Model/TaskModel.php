<?php

namespace Fintem\UserTaskBundle\Model;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Fintem\UserTaskBundle\Entity\Task;
use Fintem\UserTaskBundle\Entity\TaskAssignee;
use Fintem\UserTaskBundle\Entity\TaskUserInterface;
use Fintem\UserTaskBundle\Exception\TaskAssignedException;
use Fintem\UserTaskBundle\Exception\TaskUnassignedException;

/**
 * Class TaskModel.
 */
class TaskModel
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * TaskModel constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param TaskUserInterface $user
     * @param Task              $task
     *
     * @return TaskAssignee
     *
     * @throws TaskAssignedException
     */
    public function assign(TaskUserInterface $user, Task $task) : TaskAssignee
    {
        if ($task->isAssigned()) {
            throw new TaskAssignedException('Task already assigned');
        }
        $taskAssignee = (new TaskAssignee())->setUser($user);
        $task->assign($taskAssignee);
        $this->em->persist($task);
        $this->em->flush();

        return $taskAssignee;
    }

    /**
     * @param string $referredInstanceId
     * @param string $referredInstanceType
     *
     * @return Task
     *
     * @throws EntityNotFoundException
     */
    public function getTask(string $referredInstanceId, string $referredInstanceType)
    {
        $repo = $this->em->getRepository(Task::class);
        $criteria = ['referredInstanceId' => $referredInstanceId, 'referredInstanceType' => $referredInstanceType];

        return $repo->findOneBy($criteria);
    }

    /**
     * @param Task $task
     *
     * @return Task
     * @throws TaskUnassignedException
     */
    public function unassign(Task $task) : Task
    {
        if (null === $task->getAssignee()) {
            throw new TaskUnassignedException('Task already unassigned');
        }

        $task->unassign();
        $this->em->flush();

        return $task;
    }
}
