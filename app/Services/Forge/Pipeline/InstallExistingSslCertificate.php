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
use Throwable;

class InstallExistingSslCertificate
{
    use Outputifier;

    public function __invoke(ForgeService $service, Closure $next)
    {
        if (! $service->setting->sslRequired || ! $service->siteNewlyMade || ! $service->setting->sslInstallExisting) {
            return $next($service);
        }

        $this->information('Installing existing SSL certificate.');

        dump([
            'certificate' => $service->setting->sslExistingCertificate,
            'private_key' => $service->setting->sslExistingPrivateKey,
        ]);

        try {
            $certificate = $service->forge->createCertificate(
                $service->server->id,
                $service->site->id,
                [
                    'type' => 'existing',
                    'key' => $service->setting->sslExistingPrivateKey,
                    'certificate' => $service->setting->sslExistingCertificate,
                ],
                $service->setting->waitOnSsl
            );

            $this->information('Existing SSL certificate installed successfully.');
            $this->information('SSL Certificate ID: ' . $certificate->id);
            $this->information('Server ID: ' . $certificate->serverId);
            $this->information('Site ID: ' . $certificate->siteId);
            $this->information('SSL Certificate Domain: ' . $certificate->domain);
            $this->information('SSL Certificate Type: ' . $certificate->type);
            $this->information('SSL Certificate Request Status: ' . $certificate->requestStatus);
            $this->information('SSL Certificate Status: ' . $certificate->status);
            $this->information('SSL Certificate Active: ' . $certificate->active);
            $this->information('SSL Certificate Activation Status: ' . $certificate->activationStatus);
            $this->information('SSL Certificate Created At: ' . $certificate->createdAt);
        } catch (Throwable $e) {
            $this->failCommand("---> Something's wrong with SSL certification. Check your Forge site Log for more info.");
            $this->failCommand("---> " . get_class($e) . ": " . $e->getMessage());
        }

        return $next($service);
    }
}
