<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    // Accept any number of additional arguments so middleware can be used like
    //   AdminMiddleware:Admin,Staff or AdminMiddleware:Admin,Staff,Owner
    // Laravel passes each comma-separated value as a separate parameter, so we
    // capture them all with a splat and then flatten whatever we were given.
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // flatten a single comma-separated string (legacy/shortcut) into an array
        if (count($roles) === 1 && is_string($roles[0])) {
            $roles = explode(',', $roles[0]);
        }

        // normalize everything to lowercase for comparison
        $roles = array_map('strtolower', $roles);

        $current = strtolower($request->user()->role ?? '');
        if (in_array($current, $roles)) {
            return $next($request);
        }

        // redirect to dashboard instead of raw '/' so user stays on home after denial
        return redirect()->route('home');
    }
}
