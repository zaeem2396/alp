# Apryse Laravel Platform (ALP)

ALP is a Laravel-first document intelligence platform that converts unstructured files into extraction artifacts, metadata, and pipeline-ready outputs.

## Current Scope

- v0.1.0 implementation in progress with ingestion, normalization, extraction, and event foundations.
- PHP 8.2+ and Laravel 11+ support.
- CI guardrails for style, static analysis, and tests.

## Quality Commands

- `composer format:test`
- `composer analyse`
- `composer test`
- `composer pre-push`

## Structure

- `src/` core ALP codebase
- `config/alp.php` ALP config
- `database/migrations/` ALP schema
- `docs/` product and implementation docs
