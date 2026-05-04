<?php

namespace App\Http\Middleware;

use App\Services\AuditLogService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditRequestActivity
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldAudit($request)) {
            $this->auditLogService->logRequest($request, $response);
        }

        return $response;
    }

    private function shouldAudit(Request $request): bool
    {
        if ($request->attributes->get('audit_force') === true) {
            return true;
        }

        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return false;
        }

        if ($request->is('livewire/*')) {
            return false;
        }

        return true;
    }
}
