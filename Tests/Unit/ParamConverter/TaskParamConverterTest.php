<?php

namespace Fintem\UserTaskBundle\Test\Unit\ParamConverter;

use Fintem\UnitTestCase\UnitTestCase;
use Fintem\UserTaskBundle\Entity\Task;
use Fintem\UserTaskBundle\Model\TaskModel;
use Fintem\UserTaskBundle\ParamConverter\TaskParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TaskParamConverterTest.
 */
class TaskParamConverterTest extends UnitTestCase
{
    /**
     * @test
     */
    public function applyReturnExistingTask()
    {
        $task = new Task();

        $taskModel = $this->getBasicMock(TaskModel::class, null, ['getTask']);
        $taskModel
            ->expects(static::once())
            ->method('getTask')
            ->with('xyz_referredId', 'xyz_referredType')
            ->willReturn($task);

        /** @var TaskParamConverter|\PHPUnit_Framework_MockObject_MockObject $paramConverter */
        $paramConverter = $this->getBasicMock(TaskParamConverter::class, ['model' => $taskModel]);

        $request = new Request();
        $request->query->set('referredId', 'xyz_referredId');
        $request->query->set('referredType', 'xyz_referredType');

        /** @var ParamConverter|\PHPUnit_Framework_MockObject_MockObject $configuration */
        $configuration = $this->getBasicMock(ParamConverter::class, null, ['getName']);
        $configuration->expects(static::once())->method('getName')->willReturn('task');

        $apply = $paramConverter->apply($request, $configuration);
        static::assertTrue($apply);

        static::assertSame($task, $request->attributes->get('task'));
    }

    /**
     * @test
     */
    public function applyReturnFalseIfReferredParamsIsNotInRequest()
    {
        /** @var TaskParamConverter $paramConverter */
        $paramConverter = $this->getBasicMock(TaskParamConverter::class);

        $request = new Request();
        /** @var ParamConverter|\PHPUnit_Framework_MockObject_MockObject $configuration */
        $configuration = $this->getBasicMock(ParamConverter::class);

        static::assertFalse($paramConverter->apply($request, $configuration));
    }

    /**
     * @test
     */
    public function applyReturnFalseIfTaskNotFoundAndCreateParameterIsNotSet()
    {
        $taskModel = $this->getBasicMock(TaskModel::class, null, ['getTask']);
        $taskModel->expects(static::once())->method('getTask')->willReturn(null);

        /** @var TaskParamConverter|\PHPUnit_Framework_MockObject_MockObject $paramConverter */
        $paramConverter = $this->getBasicMock(TaskParamConverter::class, ['model' => $taskModel]);

        $request = new Request();
        $request->query->set('referredId', 'xyz_referredId');
        $request->query->set('referredType', 'xyz_referredType');
        $request->query->set('taskType', 'xyz_taskType');

        $options = [];
        /** @var ParamConverter|\PHPUnit_Framework_MockObject_MockObject $configuration */
        $configuration = $this->getBasicMock(ParamConverter::class, null, ['getOptions']);
        $configuration->expects(static::once())->method('getOptions')->willReturn($options);

        static::assertFalse($paramConverter->apply($request, $configuration));
    }

    /**
     * @test
     */
    public function applyReturnFalseIfTaskNotFoundAndTaskTypeIsNotDefined()
    {
        $taskModel = $this->getBasicMock(TaskModel::class, null, ['getTask']);
        $taskModel->expects(static::once())->method('getTask')->willReturn(null);

        /** @var TaskParamConverter|\PHPUnit_Framework_MockObject_MockObject $paramConverter */
        $paramConverter = $this->getBasicMock(TaskParamConverter::class, ['model' => $taskModel]);

        $request = new Request();
        $request->query->set('referredId', 'xyz_referredId');
        $request->query->set('referredType', 'xyz_referredType');

        $options = ['create' => true];
        /** @var ParamConverter|\PHPUnit_Framework_MockObject_MockObject $configuration */
        $configuration = $this->getBasicMock(ParamConverter::class, null, ['getOptions']);
        $configuration->expects(static::once())->method('getOptions')->willReturn($options);

        static::assertFalse($paramConverter->apply($request, $configuration));
    }

    /**
     * @test
     */
    public function applyReturnNewTask()
    {
        $taskModel = $this->getBasicMock(TaskModel::class, null, ['getTask']);
        $taskModel->expects(static::once())->method('getTask')->willReturn(null);

        /** @var TaskParamConverter|\PHPUnit_Framework_MockObject_MockObject $paramConverter */
        $paramConverter = $this->getBasicMock(TaskParamConverter::class, ['model' => $taskModel]);

        $request = new Request();
        $request->query->set('referredId', 'xyz_referredId');
        $request->query->set('referredType', 'xyz_referredType');
        $request->query->set('taskType', 'xyz_taskType');

        $options = ['create' => true];
        /** @var ParamConverter|\PHPUnit_Framework_MockObject_MockObject $configuration */
        $configuration = $this->getBasicMock(ParamConverter::class, null, ['getName', 'getOptions']);
        $configuration->expects(static::once())->method('getName')->willReturn('task');
        $configuration->expects(static::once())->method('getOptions')->willReturn($options);

        $apply = $paramConverter->apply($request, $configuration);
        static::assertTrue($apply);

        static::assertInstanceOf(Task::class, $request->attributes->get('task'));
    }

    /**
     * @test
     */
    public function supports()
    {
        /** @var TaskParamConverter $paramConverter */
        $paramConverter = $this->getBasicMock(TaskParamConverter::class);

        /** @var ParamConverter|\PHPUnit_Framework_MockObject_MockObject $configuration */
        $configuration = $this->getBasicMock(ParamConverter::class, null, ['getClass']);
        $configuration->expects(static::once())->method('getClass')->willReturn(Task::class);
        static::assertTrue($paramConverter->supports($configuration));

        $configuration = $this->getBasicMock(ParamConverter::class, null, ['getClass']);
        $configuration->expects(static::once())->method('getClass')->willReturn('xyz_class');
        static::assertFalse($paramConverter->supports($configuration));
    }
}
