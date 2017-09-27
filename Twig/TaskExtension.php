<?php

namespace Fintem\UserTaskBundle\Twig;

use Fintem\UserTaskBundle\Entity\Task;
use Fintem\UserTaskBundle\Model\TaskModel;

/**
 * Class TaskExtension.
 */
class TaskExtension extends \Twig_Extension
{
    /**
     * @var TaskModel
     */
    private $taskModel;

    /**
     * TaskExtension constructor.
     *
     * @param TaskModel $model
     */
    public function __construct(TaskModel $model)
    {
        $this->taskModel = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('is_task_assigned', [$this, 'isTaskAssigned']),
            new \Twig_SimpleFunction('get_task', [$this, 'getTask']),
        ];
    }

    /**
     * @param string $referredInstanceId
     * @param string $referredInstanceType
     *
     * @return Task|null
     */
    public function getTask(string $referredInstanceId, string $referredInstanceType)
    {
        $task = $this->taskModel->getTask($referredInstanceId, $referredInstanceType);

        return $task ? $task : null;
    }

    /**
     * @param string $referredInstanceId
     * @param string $referredInstanceType
     *
     * @return bool
     */
    public function isTaskAssigned(string $referredInstanceId, string $referredInstanceType)
    {
        $task = $this->taskModel->getTask($referredInstanceId, $referredInstanceType);

        return $task ? $task->isAssigned() : false;
    }
}