# ALP Extensibility Guide

## Contracts

ALP exposes these host-overridable contracts:

- `App\Contracts\TextExtractorInterface`
- `App\Contracts\EntityDetectorInterface`
- `App\Contracts\SummarizerInterface`
- `App\Contracts\AlpEventBusInterface` (Laravel event dispatcher bridge by default)
- `App\Application\Contracts\PipelineRunStoreInterface` (database-backed run log by default; swap for custom telemetry)

The package binds them by default in `ALPServiceProvider`, but a host Laravel application can replace those bindings in its own service provider.

## Override A Default Binding

```php
use App\Contracts\EntityDetectorInterface;
use App\Contracts\SummarizerInterface;
use App\Contracts\TextExtractorInterface;
use App\Services\MyEntityDetector;
use App\Services\MySummarizer;
use App\Services\MyTextExtractor;

public function register(): void
{
    $this->app->bind(TextExtractorInterface::class, MyTextExtractor::class);
    $this->app->bind(EntityDetectorInterface::class, MyEntityDetector::class);
    $this->app->bind(SummarizerInterface::class, MySummarizer::class);
}
```

## Default Implementations

| Contract | Default binding |
|---|---|
| `TextExtractorInterface` | `App\Infrastructure\Apryse\ApryseTextExtractor` |
| `EntityDetectorInterface` | `App\Infrastructure\AI\DefaultEntityDetector` |
| `SummarizerInterface` | `App\Infrastructure\AI\DefaultSummarizer` |
| `AlpEventBusInterface` | `App\Infrastructure\Events\LaravelAlpEventBus` |
| `PipelineRunStoreInterface` | `App\Infrastructure\Persistence\DatabasePipelineRunStore` |

For tests or single-tenant utilities you can rebind `PipelineRunStoreInterface` to `App\Infrastructure\Persistence\NullPipelineRunStore` to skip database writes while still exercising the executor.

## Custom Pipeline Step Injection

Named pipelines are resolved through the Laravel container, so steps can use constructor injection.

```php
use App\Contracts\SummarizerInterface;
use App\Pipelines\Contracts\PipelineStepInterface;

final class SummarizeDocument implements PipelineStepInterface
{
    public function __construct(private readonly SummarizerInterface $summarizer) {}

    public function handle(array $context): array
    {
        $context['summary'] = $this->summarizer->summarize($context['text']);

        return $context;
    }
}
```

Register the step class in `config/alp.php`:

```php
'pipelines' => [
    'custom-extract' => [
        \App\Pipelines\Steps\ExtractText::class,
        \App\Pipelines\Steps\DetectEntities::class,
        \App\Pipelines\Steps\SummarizeDocument::class,
    ],
],
```

## Step Context Expectations

- `ExtractText` expects `file` or `file_path`
- `DetectEntities` expects `text`
- `AISummarize` expects `text`

Each step writes its output back into the shared context array and avoids hardcoded demo data.

## Pipeline execution and dispatch

- `App\Application\Contracts\PipelineExecutorInterface` is the supported entrypoint for synchronous, instrumented runs (timing, lifecycle events, and run persistence).
- `App\Application\Services\PipelineDispatcher` chooses between immediate execution and `App\Jobs\RunPipelineJob` using `App\Application\Enums\PipelineExecutionMode` (`sync`, `queue`, `auto`).
- Domain helpers such as `App\Domain\Pipeline\Definitions\PipelineDefinitionRegistry` expose the same pipeline map that backs `config/alp.php`, which is useful when generating documentation or validating registrations in host apps.

Lifecycle events published through `AlpEventBusInterface` include `PipelineStarted`, `PipelineStepCompleted`, `PipelineCompleted`, and `PipelineFailed`.
