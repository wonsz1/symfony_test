<?php
namespace App\Service;

use Prometheus\CollectorRegistry;
use Prometheus\Gauge;
use Prometheus\Counter;

class AppMetrics
{
    private Counter $apiCallCounter;
    private Counter $callCounter;
    private Gauge $onlineUsersGauge;

    public function __construct(private CollectorRegistry $registry)
    {
        $this->apiCallCounter = $this->registry->getOrRegisterCounter(
            'app', // Namespace
            'api_calls_total', // Name
            'Total number of API calls', // Help text
            ['endpoint', 'method'] // Labels
        );
        $this->callCounter = $this->registry->getOrRegisterCounter(
            'app', // Namespace
            'calls_total', // Name
            'Total number of app calls', // Help text
            ['endpoint', 'method'] // Labels
        );
        $this->onlineUsersGauge = $this->registry->getOrRegisterGauge(
            'app',
            'online_users',
            'Current number of online users'
        );
    }

    public function incrementApiCall(string $endpoint, string $method): void
    {
        $this->apiCallCounter->inc([$endpoint, $method]);
    }

    public function setOnlineUsers(int $count): void
    {
        $this->onlineUsersGauge->set($count);
    }

    public function incrementCall(string $endpoint, string $method): void
    {
        $this->callCounter->inc([$endpoint, $method]);
    }
}