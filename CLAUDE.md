# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is an OpenAPI Generator experiments repository. The project is currently empty and ready for experimentation with OpenAPI code generation tools and workflows.

## Repository Status

This repository has been initialized with Git but does not yet contain any source code, build configuration, or documentation. As the project develops, this file should be updated to include:

- Build and development commands
- Project architecture and structure
- Testing and linting procedures
- OpenAPI generator configurations and workflows

## Development Setup

**Docker-Only Environment**: This project uses Docker containers for all development tools and runtimes. No local installation of Node.js, PHP, Python, or other development tools is required - only Docker.

All development commands should be executed through Docker containers:
- Use `docker run` or `docker-compose` for running development tools
- Mount the project directory as a volume for file access
- Consider using development containers or docker-compose for consistent environments

When adding project files, include:
- Dockerfile(s) for development environments
- docker-compose.yml for multi-service setups
- Package configuration files (package.json, requirements.txt, Cargo.toml, etc.)
- Scripts that wrap Docker commands for common tasks

## Common Commands

All commands should be executed through Docker containers. Examples of Docker-based command patterns:

```bash
# Example Node.js commands
docker run --rm -v $(pwd):/app -w /app node:18 npm install
docker run --rm -v $(pwd):/app -w /app node:18 npm run build
docker run --rm -v $(pwd):/app -w /app node:18 npm test

# Example PHP commands
docker run --rm -v $(pwd):/app -w /app php:8.2 composer install
docker run --rm -v $(pwd):/app -w /app php:8.2 php vendor/bin/phpunit

# Example Python commands
docker run --rm -v $(pwd):/app -w /app python:3.11 pip install -r requirements.txt
docker run --rm -v $(pwd):/app -w /app python:3.11 python -m pytest
```

Specific build, test, and lint commands will be added as the project structure is established.

## Architecture

Project architecture will be documented here as the codebase develops.