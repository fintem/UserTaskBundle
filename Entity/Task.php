<?php

namespace Fintem\UserTaskBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Fintem\UserTaskBundle\Entity\Traits\TimestampableEntityTrait;
use Fintem\UserTaskBundle\Entity\Traits\UUIDTrait;

/**
 * Class Task.
 *
 * @ORM\Entity
 * @ORM\Table(indexes={
 *     @ORM\Index(name="referred_instance", columns={"referredInstanceId", "referredInstanceType"})
 * })
 * @ORM\HasLifecycleCallbacks
 */
class Task
{
    use UUIDTrait,
        TimestampableEntityTrait;

    /**
     * @var TaskAssignee|null
     * @ORM\OneToOne(targetEntity="Core\AppBundle\Entity\Task\TaskAssignee", cascade={"persist"})
     * @ORM\JoinColumn(name="taskAssigneeId", referencedColumnName="id", nullable=true)
     */
    private $assignee;
    /**
     * @var ArrayCollection|TaskAssignee[]
     * @ORM\OneToMany(targetEntity="Core\AppBundle\Entity\Task\TaskAssignee", mappedBy="task", cascade={"persist"})
     * @ORM\OrderBy({"assignedAt" = "DESC"})
     */
    private $assignees;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $referredInstanceId;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $referredInstanceType;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * Task constructor.
     */
    public function __construct()
    {
        $this->assignees = new ArrayCollection();
    }

    /**
     * @param TaskAssignee|null $assignee
     *
     * @return $this
     */
    public function assign(TaskAssignee $assignee)
    {
        $this->assignee = $assignee;
        if (!$this->assignees->contains($assignee)) {
            $this->assignees->add($assignee);
            $assignee->setTask($this);
        }

        return $this;
    }

    /**
     * @return TaskAssignee|null
     */
    public function getAssignee()
    {
        return $this->assignee;
    }

    /**
     * @return TaskAssignee[]|ArrayCollection
     */
    public function getAssignees()
    {
        return $this->assignees;
    }

    /**
     * @return string
     */
    public function getReferredInstanceId() : string
    {
        return $this->referredInstanceId;
    }

    /**
     * @param string $referredInstanceId
     *
     * @return $this
     */
    public function setReferredInstanceId(string $referredInstanceId)
    {
        $this->referredInstanceId = $referredInstanceId;

        return $this;
    }

    /**
     * @return string
     */
    public function getReferredInstanceType() : string
    {
        return $this->referredInstanceType;
    }

    /**
     * @param string $referredInstanceType
     *
     * @return $this
     */
    public function setReferredInstanceType(string $referredInstanceType)
    {
        $this->referredInstanceType = $referredInstanceType;

        return $this;
    }

    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAssigned() : bool
    {
        return null !== $this->assignee;
    }

    /**
     * @return $this
     */
    public function unassign()
    {
        $this->assignee->unassign();
        $this->assignee = null;

        return $this;
    }
}
