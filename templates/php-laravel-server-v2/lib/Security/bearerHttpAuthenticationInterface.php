{{>php_file_header}}

namespace {{invokerPackage}}\Security;

/**
 * Security Interface: bearerHttpAuthentication
 *
 * Generated from OpenAPI security scheme
 * Type: http
 * Scheme: Bearer
 * Bearer Format: JWT
 *
 * Implementation Requirements:
 * - Middleware MUST implement this interface
 * - Validates JWT tokens from Authorization header
 *
 * Example Implementation:
 * ```php
 * namespace App\Http\Middleware;
 *
 * use {{invokerPackage}}\Security\bearerHttpAuthenticationInterface;
 * use Closure;
 * use Illuminate\Http\Request;
 * use Symfony\Component\HttpFoundation\Response;
 *
 * class ValidateBearerToken implements bearerHttpAuthenticationInterface
 * {
 *     public function handle(Request $request, Closure $next): Response
 *     {
 *         // Validate Bearer token from Authorization header
 *         $authHeader = $request->header('Authorization');
 *         if (!str_starts_with($authHeader, 'Bearer ')) {
 *             return response()->json(['error' => 'Bearer token required'], 401);
 *         }
 *         $token = substr($authHeader, 7);
 *         // TODO: Validate JWT token
 *         return $next($request);
 *     }
 * }
 * ```
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
