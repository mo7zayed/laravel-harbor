<?php

declare(strict_types=1);

namespace App\Services\Forge\Pipeline;

use App\Services\Forge\ForgeService;
use App\Traits\Outputifier;
use Closure;

class RestartQueueWorkers
{
    use Outputifier;

    public function __invoke(ForgeService $service, Closure $next)
    {
        if (! $service->setting->queueWorkers || $service->siteNewlyMade) {
            return $next($service);
        }

        $workers = $service->forge->workers($service->server->id, $service->site->id);

        $this->information('Restarting queue workers.');

        foreach ($workers as $worker) {
            $service->forge->restartWorker(
                serverId: $service->server->id,
                siteId: $service->site->id,
                workerId: $worker->id
            );
        }

        return $next($service);
    }
}
