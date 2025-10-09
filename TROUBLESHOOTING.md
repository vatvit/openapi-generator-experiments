# Troubleshooting Guide

## Common Issues and Solutions

### Routes Not Found (404 errors)

**Symptom**: All API endpoints return 404

**Diagnosis**:
```bash
# Check if generated routes file exists in container
docker-compose exec app ls -la generated-v2/*/

# Check if routes are being included
docker-compose exec app grep "Generated routes file not found" storage/logs/laravel.log
```

**Solutions**:
1. **Verify Docker volume mounts**: Check `laravel-api/docker-compose.yml` mounts working directory

2. **Restart containers** after generation:
   ```bash
   cd laravel-api && docker-compose restart app
   ```

### Class Not Found Errors

**Symptom**: `Class "PetStoreApiController" does not exist` or `Class "DefaultController" does not exist`

**Diagnosis**:
```bash
# Check if autoloader knows about the class
docker-compose exec app php -r "var_dump(class_exists('PetStoreApi\Server\Http\Controllers\DefaultController'));"
```

**Solutions**:
1. **Run composer dumpautoload** after generating server:
   ```bash
   cd laravel-api && docker-compose exec app composer dumpautoload
   ```

2. **Verify PSR-4 configuration** in `laravel-api/composer.json`:
   ```json
   "autoload": {
       "psr-4": {
           "PetStoreApiV2\\Server\\": "generated-v2/petstore/lib/",
           "TicTacToeApiV2\\Server\\": "generated-v2/tictactoe/lib/"
       }
   }
   ```

3. **Check class name matches filename** for PSR-4 compliance

### Security Interface Not Found

**Symptom**: `Interface "TicTacToeApiV2\Server\Security\bearerHttpAuthenticationInterface" not found`

**Cause**: OpenAPI Generator doesn't generate security interface files by default

**Solution**: Run the appropriate make target which includes post-processing to create security interfaces:
```bash
make generate-tictactoe-v2  # Includes security interface creation
```

The Makefile automatically creates security interfaces as part of the generation process.

### Middleware Class Not Found

**Symptom**: `Target class [api.middlewareGroup.getBoard] does not exist`

**Cause**: Routes are trying to apply middleware groups that don't exist

**Solution**: This should not happen with V2 templates. They use conditional middleware application:
```php
if ($router->hasMiddlewareGroup('api.middlewareGroup.getBoard')) {
    $route->middleware('api.middlewareGroup.getBoard');
}
```

If you see this error, verify you're using the V2 templates:
```bash
make generate-tictactoe-v2  # Uses templates/php-laravel-server-v2/
```

### Middleware Not Working

**Symptom**: Middleware not executing, routes work but no middleware logs

**Diagnosis**:
```bash
# Check middleware registration
docker-compose exec app php artisan route:list --path=v1

# Check Laravel logs for middleware execution
docker-compose exec app tail -f storage/logs/laravel.log
```

**Solutions**:
1. **Verify middleware groups** in `bootstrap/app.php` (if you defined any):
   ```php
   $middleware->group('api.middlewareGroup.findPets', [
       \App\Http\Middleware\CacheResponse::class,
   ]);
   ```

2. **Check middleware class exists** at the path specified in your group definition

3. **Remember groups are only attached if defined** - Routes without defined middleware groups have no middleware

### Port 8080 vs 8000

**Symptom**: `curl http://localhost:8080/api/v1/...` returns connection error

**Cause**: Laravel webserver is on port 8000, not 8080

**Solution**: Check `laravel-api/docker-compose.yml` for actual port mapping:
```yaml
webserver:
  ports:
    - "8000:80"  # Host port 8000 maps to container port 80
```

Use correct port: `curl http://localhost:8000/api/v1/...`

### Docker Compose Version Warning

**Symptom**: `the attribute 'version' is obsolete`

**Cause**: Docker Compose v2 deprecates version field

**Solution**: Remove `version: '3.8'` from `docker-compose.yml` files (cosmetic issue, doesn't affect functionality)

### jq Parse Errors in Tests

**Symptom**: `jq: parse error: Invalid numeric literal at line 1, column 10`

**Cause**: API endpoints are returning HTML error pages instead of JSON responses

**Solution**: This indicates an underlying API error. Check Laravel logs to see the actual error:
```bash
docker-compose exec app tail -50 storage/logs/laravel.log
```

Common causes:
- Missing security interfaces (run appropriate make target)
- Middleware configuration errors
- Missing handler implementations

## Debugging Workflow

1. **Verify generation succeeded**:
   ```bash
   ls -la laravel-api/generated-v2/*/lib/Http/Controllers/
   ls -la laravel-api/generated-v2/*/routes.php
   ```

2. **Check files exist in container**:
   ```bash
   docker-compose exec app ls -la generated-v2/
   ```

3. **Verify autoloading**:
   ```bash
   cd laravel-api
   docker-compose exec app composer dumpautoload
   docker-compose exec app php -r "var_dump(class_exists('TicTacToeApiV2\\\Server\\\Http\\\Controllers\\\DefaultController'));"
   ```

4. **Test Service Container binding**:
   ```bash
   docker-compose exec app php artisan tinker
   >>> app('Tic Tac Toe')
   ```

5. **Check route registration**:
   ```bash
   docker-compose exec app php artisan route:list
   ```

6. **Test endpoint**:
   ```bash
   curl -v http://localhost:8000/api/v1/games
   ```

7. **Check logs**:
   ```bash
   docker-compose exec app tail -50 storage/logs/laravel.log
   ```
