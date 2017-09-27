<?php

namespace Fintem\UserTaskBundle\Entity;

/**
 * Interface TaskReferredInstanceInterface.
 */
interface TaskReferredInstanceInterface
{
    /**
     * @return string
     */
    public function getTaskReferredId();

    /**
     * @return string
     */
    public function getTaskReferredType();
}
