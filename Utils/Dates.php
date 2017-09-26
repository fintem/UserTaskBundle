<?php

namespace Fintem\UserTaskBundle\Utils;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * Class Dates.
 */
class Dates
{
    /**
     * @param DateTimeInterface|null $date
     *
     * @return DateTimeImmutable|null
     */
    public static function toImmutable(DateTimeInterface $date = null)
    {
        if (null === $date) {
            return null;
        }
        if ($date instanceof \DateTimeImmutable) {
            return $date;
        }

        return DateTimeImmutable::createFromMutable($date);
    }
}