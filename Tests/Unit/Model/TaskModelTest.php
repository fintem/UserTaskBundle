<?php

namespace Fintem\UserTaskBundle\Test\Unit\Model;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Fintem\UnitTestCase\UnitTestCase;
use Fintem\UserTaskBundle\Entity\Task;
use Fintem\UserTaskBundle\Entity\TaskAssignee;
use Fintem\UserTaskBundle\Entity\TaskUserInterface;
use Fintem\UserTaskBundle\Exception\TaskAssignedException;
use Fintem\UserTaskBundle\Model\TaskModel;

/**
 * Class TaskModelTest.
 */
class TaskModelTest extends UnitTestCase
{
    /**
     * @test
     */
    public function assign()
    {
        $user = $this->getUser();
        $task = new Task();
        static::assertFalse($task->isAssigned());

        /** @var TaskModel|\PHPUnit_Framework_MockObject_MockObject $model */
        $model = $this->getBasicMock(
            TaskModel::class,
            ['em' => $this->getBasicMock(EntityManager::class, null, ['persist', 'flush'])]
        );

        $taskAssignee = $model->assign($user, $task);

        // Test TaskAssignee entity
        static::assertInstanceOf(TaskAssignee::class, $taskAssignee);
        static::assertSame($task, $taskAssignee->getTask());
        static::assertSame($user, $taskAssignee->getUser());

        // Test Task entity
        $assignees = $task->getAssignees();
        static::assertCount(1, $assignees);
        static::assertSame($taskAssignee, $assignees->first());
        static::assertSame($taskAssignee, $task->getAssignee());
        static::assertTrue($task->isAssigned());
    }

    /**
     * @test
     */
    public function getTaskCriteria()
    {
        $task = new Task();

        $repo = $this->getBasicMock(EntityRepository::class, null, ['findOneBy']);
        $repo
            ->expects(static::once())
            ->method('findOneBy')
            ->with(
                static::callback(
                    function (array $criteria) {
                        static::assertArrayHasKey('referredInstanceId', $criteria);
                        static::assertArrayHasKey('referredInstanceType', $criteria);
                        static::assertEquals('xyz', $criteria['referredInstanceId']);
                        static::assertEquals('zyx', $criteria['referredInstanceType']);

                        return true;
                    }
                )
            )
            ->willReturn($task);

        $em = $this->getBasicMock(EntityManager::class, null, ['getRepository']);
        $em->expects(static::once())->method('getRepository')->willReturn($repo);

        /** @var TaskModel|\PHPUnit_Framework_MockObject_MockObject $model */
        $model = $this->getBasicMock(TaskModel::class, ['em' => $em]);

        static::assertSame($task, $model->getTask('xyz', 'zyx'));
    }

    /**
     * @test
     */
    public function throwExceptionOnAssignIfTaskIsAssigned()
    {
        /** @var Task|\PHPUnit_Framework_MockObject_MockObject $task */
        $task = $this->getBasicMock(Task::class, null, ['isAssigned']);
        $task->expects(static::once())->method('isAssigned')->willReturn(true);

        /** @var TaskModel|\PHPUnit_Framework_MockObject_MockObject $model */
        $model = $this->getBasicMock(TaskModel::class);
        $user = $this->getUser();
        $this->expectException(TaskAssignedException::class);
        $model->assign($user, $task);
    }

    /**
     * @test
     */
    public function unassign()
    {
        $assignee = new TaskAssignee();
        $task = (new Task())
            ->assign($assignee);
        static::assertTrue($task->isAssigned());

        /** @var TaskModel|\PHPUnit_Framework_MockObject_MockObject $model */
        $model = $this->getBasicMock(
            TaskModel::class,
            ['em' => $this->getBasicMock(EntityManager::class, null, ['flush'])]
        );

        $model->unassign($task);

        $assignees = $task->getAssignees();
        static::assertFalse($task->isAssigned());
        static::assertNull($task->getAssignee());
        static::assertSame(1, $assignees->count());
        /** @var TaskAssignee $assignee */
        $assignee = $assignees->first();
        static::assertNotNull($assignee->getUnassignedAt());
    }

    /**
     * @return TaskUserInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getUser() : \PHPUnit_Framework_MockObject_MockObject
    {
        /** @var TaskUserInterface|\PHPUnit_Framework_MockObject_MockObject $user */
        $user = $this->getBasicMock(TaskUserInterface::class, null, ['getId']);
        $user->method('getId')->willReturn(1);

        return $user;
    }
}
