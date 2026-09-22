<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use DateTime;
use Yasumi\Provider\AbstractProvider;
use Yasumi\Yasumi;

class YasumiCalendarService
{
    /**
     * Cache in memory the holidays for each year to avoid recalculating them multiple times.
     *
     * @var array<int, AbstractProvider>
     */
    protected array $providers = [];

    protected function getProvider(int $year): AbstractProvider
    {
        if (! isset($this->providers[$year])) {
            $this->providers[$year] = Yasumi::create('USA', $year);
        }

        return $this->providers[$year];
    }

    public function isHoliday(CarbonInterface $date): bool
    {
        $provider = $this->getProvider((int) $date->format('Y'));
        return $provider->isHoliday(new DateTime($date->format('Y-m-d')));
    }

    /**
     * Determina si una fecha es día hábil considerando fines de semana y feriados federales.
     */
    public function isWorkingDay(CarbonInterface $date, bool $workWeekends = false): bool
    {
        if (! $workWeekends && $date->isWeekend()) {
            return false;
        }

        if ($this->isHoliday($date)) {
            return false;
        }

        return true;
    }

    /**
     * Calcula los días hábiles netos de un año calendario (CA-03.1).
     */
    public function getAnnualWorkingDays(int $year, bool $workWeekends = false): int
    {
        $start = Carbon::createFromDate($year, 1, 1)->startOfDay();
        $end = Carbon::createFromDate($year, 12, 31)->startOfDay();

        return $this->getWorkingDaysBetween($start, $end, $workWeekends);
    }

    /**
     * Calcula las horas hábiles anuales base (8 horas por día hábil).
     */
    public function getAnnualWorkingHours(int $year, bool $workWeekends = false): float
    {
        return (float) ($this->getAnnualWorkingDays($year, $workWeekends) * 8);
    }

    /**
     * Capacidad estándar mensual estabilizada sobre 12 meses (CA-03.2).
     */
    public function getMonthlyStandardCapacityHours(int $year, bool $workWeekends = false): float
    {
        return round($this->getAnnualWorkingHours($year, $workWeekends) / 12, 4);
    }

    /**
     * Calcula los días hábiles dentro de un rango de fechas (HU-07C).
     */
    public function getWorkingDaysBetween(CarbonInterface $startDate, CarbonInterface $endDate, bool $workWeekends = false): int
    {
        $current = $startDate->copy()->startOfDay();
        $end = $endDate->copy()->startOfDay();

        if ($current->greaterThan($end)) {
            [$current, $end] = [$end, $current];
        }

        $workingDays = 0;

        while ($current->lessThanOrEqualTo($end)) {
            if ($this->isWorkingDay($current, $workWeekends)) {
                $workingDays++;
            }

            $current->addDay();
        }

        return $workingDays;
    }
}
