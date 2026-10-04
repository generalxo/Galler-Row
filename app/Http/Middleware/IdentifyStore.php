<?php

namespace App\Http\Middleware;

use App\Models\Store;
use App\Support\CurrentStore;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sets the current store from the request host.
 *
 * The root domain is the platform (no store), "www." redirects to it, and any
 * other host must belong to an active store or the request is a 404.
 */
class IdentifyStore
{
    public function __construct(protected CurrentStore $currentStore) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = strtolower($request->getHost());
        $root = strtolower(config('tenancy.root_domain'));

        if ($host === $root) {
            return $next($request);
        }

        if ($host === "www.{$root}") {
            $url = $request->getScheme().'://'.$root.$this->port($request).$request->getRequestUri();

            return redirect()->away($url, 301);
        }

        $store = Store::findForHost($host);

        abort_if($store === null || ! $store->isActive(), 404);

        $this->currentStore->set($store);
        View::share('currentStore', $store);

        return $next($request);
    }

    protected function port(Request $request): string
    {
        $port = $request->getPort();

        return in_array($port, [80, 443, null], true) ? '' : ":{$port}";
    }
}
