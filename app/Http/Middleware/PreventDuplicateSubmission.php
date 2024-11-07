<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class PreventDuplicateSubmission
{

    public function handle(Request $request, Closure $next): Response
    {
        // Retrieve the submission token from the request
        if($request->isMethod('POST')){
            $token = $request->input('submission_token');
            // If the token exists in the request, compare it with the session token
            if (($token && session('submission_token')) &&  $token !== session('submission_token')) {
                // If tokens do not match, it’s a duplicate submission
                return redirect()->to(url()->previous())->withErrors(['error' => 'Duplicate submission detected.']);
            }

            // Update the session token for the next request
            session(['submission_token' => Str::random(40)]);
        }

        return $next($request);
    }
}
