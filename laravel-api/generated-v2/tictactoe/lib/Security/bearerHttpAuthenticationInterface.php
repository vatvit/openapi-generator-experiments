<?php declare(strict_types=1);

namespace TicTacToeApiV2\Server\Security;

/**
 * Security Interface: bearerHttpAuthentication
 *
 * Generated from OpenAPI security scheme
 * Type: http
 * Scheme: Bearer
 * Bearer Format: JWT
 */
interface bearerHttpAuthenticationInterface
{
    /**
     * Handle incoming request with http authentication
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(\Illuminate\Http\Request $request, \Closure $next): \Symfony\Component\HttpFoundation\Response;
}
