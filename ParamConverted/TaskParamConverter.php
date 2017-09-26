<?php

namespace Fintem\UserTaskBundle\ParamConverter;

use Fintem\UserTaskBundle\Entity\Task;
use Fintem\UserTaskBundle\Model\TaskModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TaskParamConverter.
 */
class TaskParamConverter implements ParamConverterInterface
{
    /**
     * @var TaskModel
     */
    private $model;

    /**
     * TaskParamConverter constructor.
     *
     * @param TaskModel $model
     */
    public function __construct(TaskModel $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $referredInstanceId = $request->get('referredId');
        $referredInstanceType = $request->get('referredType');
        if (null === $referredInstanceId || null === $referredInstanceType) {
            return false;
        }
        $task = $this->model->getTask($referredInstanceId, $referredInstanceType);
        if (null === $task) {
            $options = $configuration->getOptions();
            $taskType = $request->get('taskType');
            if (!isset($options['create']) || !$options['create'] || null === $taskType) {
                return false;
            }
            $task = (new Task())
                ->setReferredInstanceId($referredInstanceId)
                ->setReferredInstanceType($referredInstanceType)
                ->setType($taskType);
        }

        $request->attributes->set($configuration->getName(), $task);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return Task::class === $configuration->getClass();
    }
}
