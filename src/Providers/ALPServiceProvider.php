<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\Services\DocumentIntelligenceService;
use App\Application\Services\PipelineOrchestrator;
use App\Console\InstallAlpCommand;
use App\Contracts\AiProviderInterface;
use App\Contracts\AlpEventBusInterface;
use App\Contracts\ApryseClientInterface;
use App\Contracts\DocumentRepositoryInterface;
use App\Contracts\DocumentStorageInterface;
use App\Contracts\EntityDetectorInterface;
use App\Contracts\LayoutParserInterface;
use App\Contracts\StructuredDocumentRepositoryInterface;
use App\Contracts\SummarizerInterface;
use App\Contracts\TextExtractorInterface;
use App\Infrastructure\AI\DefaultEntityDetector;
use App\Infrastructure\AI\DefaultSummarizer;
use App\Infrastructure\Apryse\ApryseTextExtractor;
use App\Infrastructure\Events\LaravelAlpEventBus;
use App\Infrastructure\Apryse\Clients\ApryseClientAdapter;
use App\Infrastructure\Apryse\Services\ApryseExtractionService;
use App\Infrastructure\Storage\DocumentStorageAdapter;
use App\Normalizers\DocxNormalizer;
use App\Normalizers\PdfNormalizer;
use App\Pipelines\Contracts\PipelineEngineInterface;
use App\Pipelines\Contracts\PipelineStepInterface;
use App\Pipelines\Engine\PipelineEngine;
use App\Pipelines\PipelineManager;
use App\Repositories\DocumentRepository;
use App\Repositories\StructuredDocumentRepository;
use App\Services\AI\AiManager;
use App\Services\AI\AnthropicProvider;
use App\Services\AI\DocumentQaService;
use App\Services\AI\DocumentSummarizationService;
use App\Services\AI\EntityExtractionService;
use App\Services\AI\LocalAiProvider;
use App\Services\AI\OpenAiProvider;
use App\Services\AprysePhpClient;
use App\Services\DocumentIngestionService;
use App\Services\DocumentManager;
use App\Services\DocumentNormalizerService;
use App\Services\DocumentService;
use App\Services\DocumentStorageService;
use App\Services\Layout\DefaultLayoutParser;
use App\Services\LayoutParsingService;
use App\Services\MetadataExtractionService;
use App\Services\PipelineService;
use App\Services\StructuredDocumentService;
use App\Services\TableDetectionService;
use App\Services\TextExtractionService;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;

final class ALPServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../../config/alp.php', 'alp');

        $this->app->singleton(AlpEventBusInterface::class, function ($app): AlpEventBusInterface {
            return new LaravelAlpEventBus($app->make(Dispatcher::class));
        });

        $this->app->singleton(ApryseClientInterface::class, AprysePhpClient::class);
        $this->app->bind(TextExtractorInterface::class, ApryseTextExtractor::class);
        $this->app->bind(EntityDetectorInterface::class, DefaultEntityDetector::class);
        $this->app->bind(SummarizerInterface::class, DefaultSummarizer::class);
        $this->app->scoped(DocumentRepositoryInterface::class, DocumentRepository::class);
        $this->app->singleton(DocumentStorageInterface::class, function ($app): DocumentStorageInterface {
            $basePath = (string) $app['config']->get('alp.storage.base_path', '/tmp/alp');

            return new DocumentStorageService($basePath);
        });
        $this->app->singleton(PdfNormalizer::class);
        $this->app->singleton(DocxNormalizer::class);
        $this->app->singleton(DocumentNormalizerService::class, function ($app): DocumentNormalizerService {
            return new DocumentNormalizerService([
                $app->make(PdfNormalizer::class),
                $app->make(DocxNormalizer::class),
            ]);
        });

        $this->app->scoped(TextExtractionService::class);
        $this->app->scoped(MetadataExtractionService::class);
        $this->app->scoped(TableDetectionService::class);
        $this->app->scoped(LayoutParsingService::class);
        $this->app->scoped(DocumentIngestionService::class);
        $this->app->scoped(DocumentService::class);
        $this->app->scoped(DocumentManager::class);
        $this->app->scoped(PipelineService::class);
        $this->app->scoped(StructuredDocumentRepositoryInterface::class, function ($app): StructuredDocumentRepositoryInterface {
            $basePath = (string) $app['config']->get('alp.storage.base_path', '/tmp/alp');

            return new StructuredDocumentRepository(sprintf('%s/structured_documents.json', rtrim($basePath, '/')));
        });
        $this->app->scoped(StructuredDocumentService::class);

        $this->app->singleton(LocalAiProvider::class);
        $this->app->singleton(OpenAiProvider::class);
        $this->app->singleton(AnthropicProvider::class);
        $this->app->singleton(AiProviderInterface::class, LocalAiProvider::class);
        $this->app->singleton(AiManager::class, function ($app): AiManager {
            /** @var LocalAiProvider $localProvider */
            $localProvider = $app->make(LocalAiProvider::class);
            /** @var OpenAiProvider $openAiProvider */
            $openAiProvider = $app->make(OpenAiProvider::class);
            /** @var AnthropicProvider $anthropicProvider */
            $anthropicProvider = $app->make(AnthropicProvider::class);
            $defaultProvider = (string) $app['config']->get('alp.ai.default', 'local');

            return new AiManager([
                'local' => $localProvider,
                'openai' => $openAiProvider,
                'anthropic' => $anthropicProvider,
            ], $defaultProvider);
        });
        $this->app->scoped(DocumentSummarizationService::class);
        $this->app->scoped(EntityExtractionService::class);
        $this->app->scoped(DocumentQaService::class);
        $this->app->scoped(DocumentIntelligenceService::class);
        $this->app->scoped(PipelineOrchestrator::class);
        $this->app->singleton(ApryseClientAdapter::class);
        $this->app->singleton(ApryseExtractionService::class);
        $this->app->singleton(DocumentStorageAdapter::class);

        $this->app->singleton(LayoutParserInterface::class, DefaultLayoutParser::class);
        $this->app->singleton(PipelineManager::class, function ($app): PipelineManager {
            /** @var array<string, list<class-string<PipelineStepInterface>>> $pipelines */
            $pipelines = (array) $app['config']->get('alp.pipelines', []);

            return new PipelineManager($pipelines, static fn (string $stepClass): object => $app->make($stepClass));
        });
        $this->app->scoped(PipelineEngineInterface::class, PipelineEngine::class);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../../config/alp.php' => $this->app->basePath('config/alp.php'),
        ], 'alp-config');

        if ($this->app->runningInConsole()) {
            $this->commands([InstallAlpCommand::class]);
        }
    }
}
