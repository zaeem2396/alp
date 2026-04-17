# Apryse Laravel Platform (ALP)

ALP is a Laravel-first document processing package that ingests documents, extracts text and metadata, runs configurable pipeline steps, and persists structured outputs for downstream AI and query workflows.

## Requirements

- **PHP** 8.2+
- **Laravel** 11+
- **Composer** 2.x

## Current scope

- **v0.1.0**: ingestion, normalization, extraction, events, and service-provider wiring
- **v0.2.0**: table/layout parsing, structured artifact persistence, named pipelines, async jobs, and interface-driven extension points for extraction and AI summarization/entity detection
- **v0.3.0**: instrumented pipeline execution (`PipelineExecutorService`), run logging tables, `AlpEventBusInterface`, sync/queue/auto dispatch via `PipelineDispatcher`, `RunPipelineJob`, and named queue topology for workloads

## Install In A Laravel App

ALP is Laravel-discoverable through `extra.laravel.providers`, so a host application can install it with:

```bash
composer require zaeem2396/alp
```

Publish the package configuration and package assets:

```bash
php artisan alp:install
```

Run your application migrations after publishing:

```bash
php artisan migrate
```

## Configuration

Edit `config/alp.php` in the host application after publishing it.

| Key | Purpose |
|---|---|
| `default_pipeline` | Default named pipeline key |
| `queue` | Legacy default queue name used when a specific queue mapping is absent |
| `queues.high` / `queues.default` / `queues.ai` / `queues.index` / `queues.pipelines` | Named queue endpoints for prioritised workloads (pipelines default to `queues.pipelines`) |
| `pipeline.execution_mode` | Default execution mode for `PipelineDispatcher`: `sync`, `queue`, or `auto` |
| `ai.default` | Default AI provider key |
| `pipelines` | Named pipeline definitions as step class lists |
| `storage.base_path` | Base path for raw and derived local storage |

### Common environment variables

| Variable | Purpose | Default |
|---|---|---|
| `ALP_QUEUE` | Fallback queue when `ALP_QUEUE_PIPELINES` is unset | `default` |
| `ALP_QUEUE_HIGH` / `ALP_QUEUE_DEFAULT` / `ALP_QUEUE_AI` / `ALP_QUEUE_INDEX` | Named queue bindings for ALP workloads | `alp-high`, `alp-default`, `alp-ai`, `alp-index` |
| `ALP_QUEUE_PIPELINES` | Queue used by `RunPipelineJob` | falls back to `ALP_QUEUE` |
| `ALP_PIPELINE_EXECUTION_MODE` | Default `PipelineDispatcher` mode | `sync` |
| `ALP_AI_PROVIDER` | Active AI provider key | `local` |
| `ALP_STORAGE_PATH` | Local ALP storage path | `/tmp/alp` |
| `ALP_RAW_DISK` | Raw storage disk hint | `local` |
| `ALP_PROCESSED_DISK` | Processed storage disk hint | `local` |

## Extensibility Model

ALP now exposes contract-based extension points so host applications can replace default behavior without forking the package.

Default bindings:

- `TextExtractorInterface` -> `ApryseTextExtractor`
- `EntityDetectorInterface` -> `DefaultEntityDetector`
- `SummarizerInterface` -> `DefaultSummarizer`

Override example in a host app service provider:

```php
use App\Contracts\SummarizerInterface;
use App\Contracts\TextExtractorInterface;
use App\Services\CustomSummarizer;
use App\Services\MockTextExtractor;

public function register(): void
{
    $this->app->bind(TextExtractorInterface::class, MockTextExtractor::class);
    $this->app->bind(SummarizerInterface::class, CustomSummarizer::class);
}
```

Named pipeline steps are also container-resolved, so constructor injection works for custom implementations and custom steps.

## Usage

### Document service

Resolve `DocumentManager` when you want to use ALP directly from application code:

```php
use App\Services\DocumentManager;

$manager = app(DocumentManager::class);

$document = $manager->ingest('invoice-001', $contents, 'pdf');
$text = $manager->extractText('/tmp/invoice-001.pdf');
$summary = $manager->summarize($document->id, $text);
$entities = $manager->extractEntities($document->id, $text, [
    'amount' => '/\d+\.\d+/',
]);
```

### Pipeline service

Run named pipelines through `PipelineService`:

```php
use App\Services\PipelineService;

$result = app(PipelineService::class)->run('extract-basic', [
    'document_id' => 'doc-1',
    'file' => '/tmp/invoice-001.pdf',
]);
```

The built-in `ExtractText` step expects `file` or `file_path` in the context and writes extracted text back to `text`.

Optional context keys for pipeline runs:

- `_correlation_id` (string): forwarded to `PipelineStarted` and run logging for tracing
- `_unique_lock` (string): when dispatching `RunPipelineJob`, preferred key for `ShouldBeUnique` deduplication (falls back to `_correlation_id`, then a hash of the context payload)
- `_async` (bool): when the dispatcher mode is `auto`, a `true` value routes execution through `RunPipelineJob`

### Pipeline dispatcher

Resolve `PipelineDispatcher` when you want sync execution or an explicit queue hand-off:

```php
use App\Application\Enums\PipelineExecutionMode;
use App\Application\Services\PipelineDispatcher;

$dispatcher = app(PipelineDispatcher::class);

$result = $dispatcher->dispatch('extract-basic', [
    'file' => '/tmp/invoice-001.pdf',
], PipelineExecutionMode::Sync);

$queued = $dispatcher->dispatch('extract-basic', [
    'file' => '/tmp/invoice-001.pdf',
    '_async' => true,
], PipelineExecutionMode::Auto);
```

When a job is queued the return value is a small associative array (`queued`, `pipeline`, `queue`) instead of the pipeline context payload.

### Queue jobs

- `ExtractTablesJob`
- `GenerateSummaryJob`
- `RunPipelineJob`

Ensure workers are running in the host app:

```bash
php artisan queue:work
```

## Local Development

Install dependencies:

```bash
composer install
```

Run the full quality gate:

```bash
composer pre-push
```

Individual commands:

| Command | Purpose |
|---|---|
| `composer format:test` | Laravel Pint check |
| `composer analyse` | PHPStan |
| `composer test` | PHPUnit |

## Documentation

- `docs/extensibility.md` for host-app override examples
- `docs/roadmap.md` for the local engineering roadmap
- `docs/usage.md` for local detailed setup notes

## License

MIT.
