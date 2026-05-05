# omnipay-redsys

Redsys payment gateway driver for Omnipay. PHP 8.3+, PSR-4, MIT.

## Commands

- `make unit-tests` — run PHPUnit
- `make static-analysis` — run PHPStan
- `make coding-style-fix` — run php-cs-fixer

## Guidelines

- No comments unless the *why* is non-obvious
- Validate signatures; never trust gateway responses without HMAC check
- Use `mb_substr` (not `substr`) when trimming merchant fields