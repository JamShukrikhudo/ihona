<?php

namespace App\Http\Responses;

use App\Http\Middleware\RoleBasedRedirect;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    protected function shouldRedirect(Request $request, $redirect)
    {
        // Check if the current request path matches the redirect path
        return ! $request->is($redirect) && ! $request->is($redirect.'/*');
    }

    /**
     * @param  Request  $request
     * @return RedirectResponse|JsonResponse
     */
    public function toResponse($request)
    {
        $user = Auth::user();

        // Check if the user has a role and redirect accordingly
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
                : redirect()->intended($roleRedirect);
        }

        // Default redirection
        return $request->wantsJson()
            ? new JsonResponse(['two_factor' => false], 200)
            : redirect()->intended('/app');
    }
}
