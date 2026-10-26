<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect('/login');
        }

        $allowed = collect($roles)
            ->flatMap(fn (string $role): array => explode(',', $role))
            ->map(fn (string $role): string => trim($role))
            ->filter()
            ->all();

        if (! in_array($request->user()->role?->name, $allowed, true)) {
            $panelPath = $request->user()->panelPath();

            // A user without a valid role would bounce back to this same panel forever.
            abort_if($request->is(ltrim($panelPath, '/'), ltrim($panelPath, '/').'/*'), 403);

            return redirect($panelPath);
        }

        return $next($request);
    }
}
