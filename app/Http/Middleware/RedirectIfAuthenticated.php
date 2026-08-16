<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                foreach (RoleBasedRedirect::PANELS as $role => $panel) {
                    if ($user->hasRole($role)) {
                        return redirect('/'.$panel);
                    }
                }
                // If user has a role outside the map, redirect to /{role}
                $userRoles = $user->getRoleNames();
                if ($userRoles->isNotEmpty()) {
                    $firstRole = $userRoles->first();

                    return redirect('/'.$firstRole);
                }

                // If user has no roles, redirect to default home
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
