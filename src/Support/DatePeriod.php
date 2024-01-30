<?php

namespace Weblebby\Framework\Support;

use Carbon\Carbon;
use DateInterval;
use DatePeriod as BaseDatePeriod;
use DateTimeInterface;
use Exception;

class DatePeriod
{
    protected BaseDatePeriod $period;

    /**
     * @throws Exception
     */
    public function __construct(string $duration, ?Carbon $startDate = null, int|Carbon $end = 1)
    {
        $this->period = new BaseDatePeriod($startDate ?? now(), new DateInterval($duration), $end);
    }

    public function getPeriod(): BaseDatePeriod
    {
        return $this->period;
    }

    public function getStartDate(): DateTimeInterface
    {
        return $this->period->getStartDate();
    }

    public function getEndDate(): ?DateTimeInterface
    {
        foreach ($this->period as $date) {
            //
        }

        return $date ?? null;
    }
}
