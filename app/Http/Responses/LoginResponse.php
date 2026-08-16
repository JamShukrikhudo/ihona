<?php

namespace App\Http\Responses;

use App\Http\Middleware\RoleBasedRedirect;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    protected function shouldRedirect(Request $request, $redirect)
    {
        // Check if the current request path matches the redirect path
        return ! $request->is($redirect) && ! $request->is($redirect.'/*');
    }

    public function toResponse($request)
    {
        $user = Auth::user();

        foreach (RoleBasedRedirect::PANELS as $role => $panel) {
            if ($user->hasRole($role)) {
                $redirect = '/'.$panel;

                return $request->wantsJson()
                    ? new JsonResponse(['two_factor' => false], 200)
                    : ($this->shouldRedirect($request, $redirect)
                        ? redirect()->to($redirect)
                        : redirect()->intended($redirect));
            }
        }

        // If user has a role outside the map, redirect to /{role}
        $userRoles = $user->getRoleNames();
        if ($userRoles->isNotEmpty()) {
            $firstRole = $userRoles->first();
            $roleRedirect = '/'.$firstRole;

            return $request->wantsJson()
                ? new JsonResponse(['two_factor' => false], 200)
                : ($this->shouldRedirect($request, $roleRedirect)
                    ? redirect()->to($roleRedirect)
                    : redirect()->intended($roleRedirect));
        }

        // Default redirection
        return $request->wantsJson()
            ? new JsonResponse(['two_factor' => false], 200)
            : redirect()->intended('/app');
    }
}
