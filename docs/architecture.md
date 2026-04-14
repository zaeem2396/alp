# ALP Architecture (Current)

## Core Layers

- `src/Core`: immutable domain objects (`Document`, `Pipeline`).
- `src/Contracts`: interfaces for repository, storage, normalizer, and Apryse client.
- `src/Services`: orchestration services for ingestion and extraction.
- `src/Events` and `src/Jobs`: event-driven processing primitives.
- `src/Facades` and `src/Providers`: Laravel DX integration surface.

## v0.1.0 Flow

1. Request enters `DocumentController@store`.
2. `DocumentService` delegates to `DocumentIngestionService`.
3. Ingestion stores raw content, normalizes document, stores processed output.
4. Lifecycle events are dispatched during upload/normalization/processing.
5. Extraction services use the Apryse client contract for text/metadata payloads.

## Quality Gates

- Style: Laravel Pint
- Analysis: PHPStan
- Tests: PHPUnit
- Unified guard: `composer pre-push`
