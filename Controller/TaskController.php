<?php

namespace Fintem\UserTaskBundle\Controller;

use Fintem\UserTaskBundle\Entity\Task;
use Fintem\UserTaskBundle\Entity\TaskUserInterface;
use Fintem\UserTaskBundle\Exception\TaskAssignedException;
use Fintem\UserTaskBundle\Model\TaskModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TaskController.
 *
 * @Route("/admin/task", service="admin.controller.task")
 */
class TaskController
{
    /**
     * @var TaskModel
     */
    private $model;

    /**
     * TaskController constructor.
     *
     * @param TaskModel             $model
     */
    public function __construct(TaskModel $model)
    {
        $this->model = $model;
    }

    /**
     * @Route("/assign/{referredId}/{referredType}/{taskType}/{userId}", name="admin_task_assign")
     * @ParamConverter("admin", converter="admin", class="Fintem\UserTaskBundle\TaskUserInterface")
     * @ParamConverter("task", converter="task", class="Fintem\UserTaskBundle\Entity\Task", options={"create": true})
     *
     * @param TaskUserInterface $admin
     * @param Task  $task
     * @param Request  $request
     *
     * @return RedirectResponse
     */
    public function assignAction(TaskUserInterface $admin, Task $task, Request $request) : RedirectResponse
    {
        try {
            $this->model->assign($admin, $task);
        } catch (TaskAssignedException $ex) {
            return new RedirectResponse($request->headers->get('referer'));
        }
    }

    /**
     * @Route("/unassign/{referredId}/{referredType}", name="admin_task_unassign")
     * @ParamConverter("admin", converter="admin", class="Fintem\UserTaskBundle\TaskUserInterface")
     * @ParamConverter("task", converter="task", class="Core\AppBundle\Entity\Task\Task")
     *
     * @param TaskUserInterface $admin
     * @param Task  $task
     * @param Request  $request
     *
     * @return RedirectResponse
     */
    public function unassignAction(TaskUserInterface $admin, Task $task, Request $request)
    {
        $this->model->unassign($task);

        return new RedirectResponse($request->headers->get('referer'));
    }
}
