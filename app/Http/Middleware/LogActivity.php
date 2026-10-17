<?php

namespace App\Http\Middleware;

use App\Services\ActivityLogger;
use Closure;
use Illuminate\Http\Request;

class LogActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (!$request->user() || !$request->route() || $request->isMethod('GET') || $request->isMethod('HEAD')) {
            return $response;
        }

        if ($response->getStatusCode() >= 400 || $request->session()->has('errors')) {
            return $response;
        }

        $routeAction = $request->route()->getActionName();
        $parts = explode('@', $routeAction);
        $method = strtolower(end($parts));
        $subjectType = class_basename(reset($parts));
        $subjectType = preg_replace('/Controller$/', '', $subjectType);

        $deleteMethods = ['destroy', 'deleteproduct'];
        $editMethods = ['update', 'updateroom', 'ubahstatus', 'splitinvoice', 'bulkinvoice'];
        $createMethods = ['save', 'store', 'actionregister'];

        if (in_array($method, $deleteMethods, true)) {
            $action = 'delete';
        } elseif (in_array($method, $editMethods, true)) {
            $action = 'edit';
        } elseif (in_array($method, $createMethods, true)) {
            $action = 'create';
        } else {
            return $response;
        }

        $subjectId = $request->route('id')
            ?: $request->route('ticket_id')
            ?: $request->route('passenger_id');
        $description = ucfirst($action) . ' ' . $subjectType;
        if ($subjectId !== null) {
            $description .= ' #' . $subjectId;
        }

        app(ActivityLogger::class)->record(
            $request,
            $action,
            $description,
            $request->user(),
            $subjectType,
            $subjectId
        );

        return $response;
    }
}
