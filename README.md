# Apryse Laravel Platform (ALP)

ALP is a Laravel-first document intelligence layer that turns unstructured files (PDF, DOCX, etc.) into extraction artifacts, layout/table structures, AI-derived summaries and entities, and structured JSON suitable for storage and downstream pipelines.

## Requirements

- **PHP** 8.2+
- **Laravel** 11+ (this repository targets framework APIs used by Laravel packages)
- **Composer** 2.x

## Current scope

- **v0.1.0**: ingestion, normalization, extraction, events, and service-provider wiring.
- **v0.2.0**: table/layout parsing, multi-provider AI abstraction, structured document persistence, named pipeline execution, and async jobs.

## Repository layout

| Path | Purpose |
|------|---------|
| `src/` | ALP application code (`App\` namespace) |
| `config/alp.php` | Published ALP configuration |
| `database/migrations/` | Schema migrations (e.g. `documents`, `structured_documents`) |
| `routes/web.php` | Example HTTP routes (e.g. document upload) |
| `tests/` | PHPUnit unit tests |
| `docs/` | Local project notes (roadmap/usage) |

### DDD/Clean Architecture layout

```text
src/
  Domain/
    Document/
      Models/
      Contracts/
      ValueObjects/
    Pipeline/
      Contracts/
      Definitions/
  Application/
    Services/
    Jobs/
    Events/
    DTOs/
  Infrastructure/
    Apryse/
      Clients/
      Services/
    AI/
      Providers/
      Prompts/
    Storage/
  Pipelines/
    Contracts/
    Steps/
    Engine/
  Repositories/
  Facades/
  Providers/
```

## Local setup (developer machine)

Clone the repository and install PHP dependencies:

```bash
composer install
```

Run the full quality gate (same checks as `composer pre-push`):

```bash
composer pre-push
```

Individual scripts:

| Command | What it runs |
|---------|----------------|
| `composer format:test` | Laravel Pint in check-only mode |
| `composer analyse` | PHPStan |
| `composer test` | PHPUnit |

## Installing ALP in a Laravel application

ALP is packaged as Laravel-discoverable: `composer.json` registers `App\Providers\ALPServiceProvider` under `extra.laravel.providers`. After you add this package to your app (path repository, VCS, or packagist), run:

```bash
composer require zaeem2396/alp
```

Then publish configuration (optional but recommended):

```bash
php artisan alp:install
```

That publishes `config/alp.php` into your app’s `config/` directory.

Register routes if you use ALP’s example HTTP surface: either copy the contents of `routes/web.php` from this repo into your app’s `routes/web.php`, or include a route file that defines `POST /documents` as in this project.

Run migrations from your Laravel app root (after publishing or merging migrations):

```bash
php artisan migrate
```

## Configuration

After publishing, edit `config/alp.php` in your application. Key groups:

- **`default_pipeline`**: default pipeline name (see `pipelines` keys).
- **`queue`**: queue connection/name hint (`ALP_QUEUE` env).
- **`ai`**: AI provider selection (`ALP_AI_PROVIDER`, default `local`).
- **`pipelines`**: named pipelines mapping to step class names implementing `PipelineStepInterface`.
- **`storage`**: filesystem-related settings (`ALP_STORAGE_PATH`, disk hints).

### Environment variables (common)

| Variable | Purpose | Default (in config) |
|----------|---------|----------------------|
| `ALP_QUEUE` | Queue name/connection context for jobs | `default` |
| `ALP_AI_PROVIDER` | Active AI provider key (`local`, `openai`, `anthropic`) | `local` |
| `ALP_STORAGE_PATH` | Base path for raw/processed file storage | `/tmp/alp` |
| `ALP_RAW_DISK` | Laravel disk name for raw blobs (future use) | `local` |
| `ALP_PROCESSED_DISK` | Laravel disk name for processed blobs (future use) | `local` |

## Usage

### HTTP: upload document (example route)

The package ships with an example `POST /documents` handler that accepts JSON:

```json
{
  "name": "invoice-001",
  "content": "<binary or text content as string for the demo>",
  "extension": "pdf"
}
```

Allowed `extension` values in the demo request: `pdf`, `docx`.

Response (201): document `id`, `name`, and `status`.

### Programmatic: facades (in a Laravel app)

With default Laravel facade aliases, you can resolve:

- **`Document`** facade → `DocumentManager` (ingestion, extraction, tables, layout, AI helpers).
- **`Pipeline`** facade → `PipelineManager` (runs configured pipelines).

Example (conceptual — adjust imports to your app’s facade wiring):

```php
use App\Facades\Document;
use App\Facades\Pipeline;

$doc = Document::ingest('My doc', $fileContents, 'pdf');
$tables = Document::detectTables($someText);
$layout = Document::parseLayout($someText);
$summary = Document::summarize($doc->id, $someText);

Pipeline::run('extract-basic', ['document_id' => $doc->id]);
```

Named pipelines are defined under `config/alp.php` → `pipelines` (e.g. `extract-basic`).

### Queue jobs

- `ExtractTablesJob` — async table detection from text.
- `GenerateSummaryJob` — async summarization.

Dispatch these from your application when wiring workers; ensure `queue` workers are running (`php artisan queue:work`) and `ALP_QUEUE` / Laravel queue settings match your deployment.

### Structured documents

`StructuredDocumentService` persists summarized/entity payloads through `StructuredDocumentRepositoryInterface`. The default repository writes versioned payloads to a local JSON store under `ALP_STORAGE_PATH` (for easy local execution) and is aligned with the `structured_documents` migration contract.

## Documentation

- `docs/roadmap.md` — full engineering roadmap (local-only file by default in this repo).
- `docs/usage.md` — local detailed runbook and package usage notes (local-only file by default in this repo).

## License

MIT (see `composer.json`).
