<?php

namespace HMS\Prometheus\Collectors\Gatekeeper;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use HMS\Entities\Gatekeeper\DoorState;
use HMS\Facades\Meta;
use HMS\Repositories\Gatekeeper\DoorRepository;
use Spatie\Prometheus\Collectors\Collector;
use Spatie\Prometheus\Facades\Prometheus;

class DoorCollector implements Collector
{
    public function register(): void
    {
        Prometheus::addGauge('Door State')
            ->name('gatekeeper_door_state')
            ->helpText('Door state')
            ->label('door')
            ->value(fn () => app()->call([$this, 'getValue']));
    }

    public function getValue(DoorRepository $doorRepository)
    {
        $values = [];

        foreach ($doorRepository->findAll() as $door) {
            $values[] = [
                match ($door->getState()) {
                    DoorState::UNKNOWN => 0,
                    DoorState::OPEN => 1,
                    DoorState::CLOSED => 2,
                    DoorState::LOCKED => 3,
                    DoorState::FAULT => 4,
                },
                [$door->getShortName()],
            ];
        }

        return $values;
    }
}
