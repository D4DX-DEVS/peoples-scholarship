<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Authenticates the People-ERP portal by a shared secret.
 *
 * The caller signs the raw request body and sends the digest in
 * X-Bridge-Signature. Signing the body rather than issuing a bearer token
 * means a captured request cannot be edited, and combining it with a
 * timestamp bounds how long a captured request stays usable.
 */
class VerifyBridgeSignature
{
    /**
     * How far apart the two servers' clocks may drift before a request is
     * rejected as a replay, in seconds.
     */
    private const TOLERANCE = 300;

    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('services.bridge.secret');

        // An unset secret must fail closed. Were it allowed through, an empty
        // secret would make every signature trivially forgeable.
        if (empty($secret)) {
            return response()->json(['message' => 'Bridge is not configured.'], 503);
        }

        $timestamp = $request->header('X-Bridge-Timestamp');

        if (! is_numeric($timestamp) || abs(time() - (int) $timestamp) > self::TOLERANCE) {
            return response()->json(['message' => 'Invalid or expired timestamp.'], 401);
        }

        $provided = (string) $request->header('X-Bridge-Signature');
        $expected = 'sha256='.hash_hmac('sha256', $timestamp.'.'.$request->getContent(), $secret);

        if (! hash_equals($expected, $provided)) {
            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        return $next($request);
    }
}
