# Test Template Development

A [Forma](https://github.com/launchforma) template for small business websites.

## Deploy on Railway

[![Deploy on Railway](https://railway.app/button.svg)](https://railway.app/new/template?template=https://github.com/launchforma/template-test-template-development)

## Requirements

- PHP 8.5
- Node.js 22
- PostgreSQL

## Local Development

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install && npm run dev
php artisan serve
```

## Testing

```bash
./vendor/bin/pest
```
