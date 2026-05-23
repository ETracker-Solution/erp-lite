<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PreventDuplicateSubmission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST')) {
            // 1. Traditional session token check (if token is provided in the request)
            $token = $request->input('submission_token');
            if ($token) {
                $sessionToken = session('submission_token');
                if ($sessionToken && $token !== $sessionToken) {
                    return redirect()->to(url()->previous())->withErrors(['error' => 'Duplicate submission detected.']);
                }
                // Update the session token for the next request
                session(['submission_token' => Str::random(40)]);
            }

            // 2. Cache-lock duplicate submission prevention for all state-changing requests
            // Ignore standard transient fields in request signature
            $exclude = ['_token', 'submission_token', '_method'];
            $inputData = $request->except($exclude);

            // Normalize any uploaded file objects to prevent serialization errors
            array_walk_recursive($inputData, function (&$val) {
                if ($val instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                    $val = $val->getClientOriginalName() . '|' . $val->getSize();
                }
            });

            // Generate a unique signature for the request based on user, url, and data
            $signature = md5(serialize([
                'user_id' => auth()->id() ?? session()->getId(),
                'url' => $request->fullUrl(),
                'data' => $inputData
            ]));

            $lockKey = 'dup_lock:' . $signature;

            // Attempt to acquire cache lock for 3 seconds
            $acquired = Cache::add($lockKey, true, 3);

            if (!$acquired) {
                Log::warning('Duplicate submission blocked by cache lock: ' . $request->fullUrl());

                if ($request->ajax()) {
                    return response()->json(['error' => 'Duplicate submission detected.'], 422);
                }

                return redirect()->to(url()->previous())
                    ->withInput($request->except('_token'))
                    ->withErrors(['error' => 'Duplicate submission detected. Please wait a moment.']);
            }
        }

        return $next($request);
    }
}

