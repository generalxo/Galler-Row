<?php

namespace App\Http\Middleware;

use App\Support\CurrentStore;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Limits a route to store hosts: a 404 on the platform domain.
 */
class EnsureStore
{
    public function __construct(protected CurrentStore $currentStore) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($this->currentStore->has(), 404);

        return $next($request);
    }
}
