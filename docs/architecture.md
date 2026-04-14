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

## v0.2.0 Additions

1. Layout and table extraction services generate structured page zones and tabular cells.
2. AI layer introduces provider abstraction via `AiManager` and `AiProviderInterface`.
3. AI workflows now include summarization, entity extraction, and document Q&A services.
4. Structured JSON outputs are persisted through `StructuredDocumentService`.
5. Named pipeline execution is available through `PipelineService::run(...)`.

## Quality Gates

- Style: Laravel Pint
- Analysis: PHPStan
- Tests: PHPUnit
- Unified guard: `composer pre-push`
