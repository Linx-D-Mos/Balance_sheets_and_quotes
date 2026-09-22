<?php

use App\Services\YasumiCalendarService;
use Carbon\Carbon;

describe('YasumiCalendarService Global setting feature Test', function () {
    beforeEach(function () {
        $this->service = new YasumiCalendarService();
    });

    it('identifies official USA federal holidays correctly', function () {
        // Año Nuevo (Jueves 1 de Enero de 2026)
        $newYear = Carbon::parse('2026-01-01');
        expect($this->service->isHoliday($newYear))->toBeTrue();

        // Día laborable ordinario (Viernes 2 de Enero de 2026)
        $normalDay = Carbon::parse('2026-01-02');
        expect($this->service->isHoliday($normalDay))->toBeFalse();

        // Juneteenth (Viernes 19 de Junio de 2026)
        $juneteenth = Carbon::parse('2026-06-19');
        expect($this->service->isHoliday($juneteenth))->toBeTrue();

        // Navidad (Viernes 25 de Diciembre de 2026)
        $christmas = Carbon::parse('2026-12-25');
        expect($this->service->isHoliday($christmas))->toBeTrue();
    });

    it('determines working days respecting weekends and federal holidays', function () {
        // Sábado ordinario (sin feriado)
        $saturday = Carbon::parse('2026-01-03');
        expect($this->service->isWorkingDay($saturday, workWeekends: false))->toBeFalse()
            ->and($this->service->isWorkingDay($saturday, workWeekends: true))->toBeTrue();

        // Feriado en día de semana: Navidad 2026 (Viernes)
        $christmas = Carbon::parse('2026-12-25');
        expect($this->service->isWorkingDay($christmas, workWeekends: false))->toBeFalse()
            ->and($this->service->isWorkingDay($christmas, workWeekends: true))->toBeFalse();

        // Día hábil ordinario: Lunes 5 de Enero de 2026
        $monday = Carbon::parse('2026-01-05');
        expect($this->service->isWorkingDay($monday, workWeekends: false))->toBeTrue();
    });

    it('calculates annual working days within expected calendar boundaries for 2026', function () {
        $workingDays = $this->service->getAnnualWorkingDays(2026);

        // En un año de 365 días con 104 fines de semana y ~10-11 feriados federales,
        // los días hábiles netos deben oscilar entre 248 y 252 días.
        expect($workingDays)->toBeGreaterThanOrEqual(248)
            ->toBeLessThanOrEqual(252);
    });

    it('calculates annual working hours based on standard 8 hours per day', function () {
        $days = $this->service->getAnnualWorkingDays(2026);
        $hours = $this->service->getAnnualWorkingHours(2026);

        expect($hours)->toBe((float) ($days * 8));
    });

    it('calculates monthly standard capacity hours stabilized over 12 months (CA-03.2)', function () {
        $annualHours = $this->service->getAnnualWorkingHours(2026);
        $monthlyCapacity = $this->service->getMonthlyStandardCapacityHours(2026);

        $expectedMonthly = round($annualHours / 12, 4);

        expect($monthlyCapacity)->toBe($expectedMonthly)
            ->and($monthlyCapacity)->toBeGreaterThan(160.00); // Rango promedio esperado (~166.00 hrs)
    });

    it('calculates working days in a custom date range (HU-07C)', function () {
        // Rango de 2 semanas: Lunes 5 Ene 2026 al Viernes 16 Ene 2026 (10 días laborables)
        $start = Carbon::parse('2026-01-05');
        $end = Carbon::parse('2026-01-16');

        $workingDays = $this->service->getWorkingDaysBetween($start, $end, workWeekends: false);

        expect($workingDays)->toBe(10);
    });
});
