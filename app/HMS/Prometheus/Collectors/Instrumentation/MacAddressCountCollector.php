<?php

namespace HMS\Prometheus\Collectors\Instrumentation;

use HMS\Repositories\Instrumentation\MacAddressRepository;
use Spatie\Prometheus\Collectors\Collector;
use Spatie\Prometheus\Facades\Prometheus;

class MacAddressCountCollector implements Collector
{
    public function register(): void
    {
        $macAddressRepository = app(MacAddressRepository::class);

        Prometheus::addGauge('Max Addresses Count')
            ->name('instrumentation_mac_address_count')
            ->helpText('Count of MacAddresses seen in the last 5 minutes')
            ->value(fn () => $macAddressRepository->countSeenLastFiveMinutes());
    }
}
