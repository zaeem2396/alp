# ALP Extensibility Guide

## Contracts

ALP exposes these host-overridable contracts:

- `App\Contracts\TextExtractorInterface`
- `App\Contracts\EntityDetectorInterface`
- `App\Contracts\SummarizerInterface`

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
