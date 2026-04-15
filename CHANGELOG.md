# Changelog

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

No unreleased changes

## 1.1.0 - 2026-04-14

This release addresses critical security vulnerabilities and updates dependencies:

### Dependency Updates
- Updated `auth0/auth0-php` from `^8.3.1` to `^8.19.0`, pulling in fixes for 5 CVEs:
  - [GHSA-v9m8-9xxp-q492](https://github.com/auth0/auth0-PHP/security/advisories/GHSA-v9m8-9xxp-q492) - High: Insecure deserialization
  - [GHSA-g98g-r7gf-2r25](https://github.com/auth0/auth0-PHP/security/advisories/GHSA-g98g-r7gf-2r25) - Medium: CookieStore auth-tag brute forcing
  - [GHSA-9mh6-g99m-ppcw](https://github.com/auth0/auth0-PHP/security/advisories/GHSA-9mh6-g99m-ppcw) - Medium: Bulk user import file handling
  - [GHSA-j2vm-wrq3-f7gf](https://github.com/auth0/auth0-PHP/security/advisories/GHSA-j2vm-wrq3-f7gf) - High: Improper audience validation
  - [GHSA-w3wc-44p4-m4j7](https://github.com/auth0/auth0-PHP/security/advisories/GHSA-w3wc-44p4-m4j7) - Critical (CVSS 9.1): Insufficient cookie encryption entropy

### Breaking Changes
- Minimum PHP version raised to 8.2 (required by auth0-php 8.19.0)

### Documentation
- Added "Migrating from Earlier Versions" section to README
- Updated installation requirements and configuration examples

## 1.0.2 - 2024-05-07

Improve documentation

## 1.0.1 - 2024-05-06

Fixes and cleanups:

* Fix composer dependency (to allow PHP 7.x)
* Move migration from src --> templates folder.
* Update README.md documentation and included a logo image.
* Delete installation.md and laravel-auth0-pattern-logo (accidentally committed from parent library).


## 1.0.0 - 2024-04-23

* Created repo as new composer package from pre-existing source files to provide a simpler alternative to the 'auth0/login' package for Auth0 authentication in Laravel.
