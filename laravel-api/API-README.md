# Laravel API Server

This Laravel API server is designed to serve endpoints based on the OpenAPI specification. It provides a RESTful API for user management and is containerized with Docker for development.

## Features

- **Laravel 12** (Latest version)
- **Docker-based development environment**
- **RESTful API endpoints** matching OpenAPI specification
- **User management** (CRUD operations)
- **API documentation** accessible via endpoints
- **Input validation** and error handling
- **Database migrations** ready for MySQL/PostgreSQL
- **PHPUnit testing framework** included

## Project Structure

```
laravel-api/
├── app/
│   └── Http/
│       └── Controllers/
│           └── Api/
│               └── UserController.php    # API controllers
├── routes/
│   └── api.php                          # API routes definition
├── database/
│   └── migrations/                      # Database structure
├── docker/                              # Docker configuration files
├── docker-compose.yml                   # Container orchestration
├── Dockerfile                           # Application container
└── public/
    └── openapi.yaml                     # OpenAPI specification
```

## Available API Endpoints

### Health Check
- `GET /api/v1/health` - API health status

### User Management (RESTful)
- `GET /api/v1/users` - List all users (with pagination)
- `POST /api/v1/users` - Create a new user
- `GET /api/v1/users/{id}` - Get user by ID
- `PUT /api/v1/users/{id}` - Update existing user
- `DELETE /api/v1/users/{id}` - Delete user

### Documentation
- `GET /api/docs` - API documentation and endpoint list
- `GET /openapi.yaml` - OpenAPI specification file

## Docker Development Environment

### Services
- **app**: Laravel PHP-FPM application
- **webserver**: Nginx reverse proxy
- **db**: MySQL 8.0 database
- **redis**: Redis cache/session storage

### Getting Started

1. **Start the development environment:**
   ```bash
   docker-compose up -d
   ```

2. **Install PHP dependencies:**
   ```bash
   docker-compose exec app composer install
   ```

3. **Set up environment:**
   ```bash
   docker-compose exec app cp .env.example .env
   docker-compose exec app php artisan key:generate
   ```

4. **Run migrations:**
   ```bash
   docker-compose exec app php artisan migrate
   ```

5. **Access the API:**
   - Base URL: `http://localhost:8000`
   - API Base: `http://localhost:8000/api/v1`
   - Health Check: `http://localhost:8000/api/v1/health`
   - API Documentation: `http://localhost:8000/api/docs`

### Common Commands

```bash
# Start services
docker-compose up -d

# View logs
docker-compose logs -f

# Execute Artisan commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan tinker

# Run tests
docker-compose exec app php artisan test

# Stop services
docker-compose down
```

## API Response Format

### Success Response
```json
{
  "users": [...],
  "total": 150,
  "limit": 20,
  "offset": 0
}
```

### Error Response
```json
{
  "code": 404,
  "message": "User not found"
}
```

### Validation Error Response
```json
{
  "code": 422,
  "message": "Validation failed",
  "errors": [
    {
      "field": "email",
      "message": "Email address is required"
    }
  ]
}
```

## User Model Fields

- `id` (integer) - Unique identifier
- `email` (string) - User email address
- `name` (string) - User full name
- `avatar` (string, nullable) - Avatar image URL
- `role` (enum) - User role: admin, moderator, user, guest
- `is_active` (boolean) - Account active status
- `created_at` (datetime) - Creation timestamp
- `updated_at` (datetime) - Last update timestamp

## Database Configuration

The application is configured to work with MySQL by default. Database connection settings:

- **Host**: `db` (Docker service name)
- **Port**: `3306`
- **Database**: `laravel_api`
- **Username**: `laravel`
- **Password**: `password`

## Environment Variables

Key environment variables in `.env`:

```
APP_ENV=local
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel_api
DB_USERNAME=laravel
DB_PASSWORD=password
REDIS_HOST=redis
```

## Future Integration with Generated Code

This Laravel API is prepared for integration with OpenAPI-generated PHP client code:

1. **Generated Models**: Can replace or supplement Eloquent models
2. **Generated DTOs**: For request/response validation
3. **Generated API Clients**: For external service communication
4. **Generated Documentation**: Enhanced API documentation

## Testing

Run the test suite:

```bash
# All tests
docker-compose exec app php artisan test

# Specific test file
docker-compose exec app php artisan test tests/Feature/UserApiTest.php

# With coverage
docker-compose exec app php artisan test --coverage
```

## Development Notes

- The API follows RESTful conventions
- Input validation matches OpenAPI specification requirements
- Error responses follow standard HTTP status codes
- All endpoints return JSON responses
- Database queries are optimized with pagination
- The application is ready for production deployment with proper environment configuration

## Next Steps

1. Add authentication/authorization middleware
2. Implement rate limiting
3. Add comprehensive test coverage
4. Set up CI/CD pipeline
5. Configure production environment
6. Integrate with OpenAPI-generated client code