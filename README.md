# Laravel Login API – OAuth2 (JWT-Based)

This project implements a **secure login system** in Laravel using the **OAuth2**, powered by **JWT tokens** (access and refresh tokens).
---

## Features

- OAuth2 (no Passport, custom implementation)
- JWT-based authentication
- Secure access token & refresh token issuance
- Refresh token rotation
- Secure storage via HTTP-only cookies (optional)
- Multilingual validation and error messages (optional)

---

## Requirements

- PHP 8.1+
- Laravel 12+
- MySQL/PostgreSQL/SQLite
- Composer

---

## Security Notes

- Access tokens have short expiration (e.g., 1 hour)
- Refresh tokens are long-lived but rotated on each use
- Store refresh tokens securely (preferably in HTTP-only secure cookies)
- All sensitive routes protected via middleware

