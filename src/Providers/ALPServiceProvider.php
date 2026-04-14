<?php

declare(strict_types=1);

namespace App\Providers;

use App\Console\InstallAlpCommand;
use App\Contracts\ApryseClientInterface;
use App\Contracts\DocumentRepositoryInterface;
use App\Contracts\DocumentStorageInterface;
use App\Normalizers\DocxNormalizer;
use App\Normalizers\PdfNormalizer;
use App\Pipelines\PipelineManager;
use App\Repositories\DocumentRepository;
use App\Services\AprysePhpClient;
use App\Services\DocumentIngestionService;
use App\Services\DocumentManager;
use App\Services\DocumentNormalizerService;
use App\Services\DocumentService;
use App\Services\DocumentStorageService;
use App\Services\AI\AiManager;
use App\Services\AI\DocumentQaService;
use App\Services\AI\DocumentSummarizationService;
use App\Services\AI\EntityExtractionService;
use App\Services\AI\LocalAiProvider;
use App\Services\Layout\DefaultLayoutParser;
use App\Services\LayoutParsingService;
use App\Services\MetadataExtractionService;
use App\Services\PipelineService;
use App\Services\StructuredDocumentService;
use App\Services\TableDetectionService;
use App\Services\TextExtractionService;
use App\Contracts\AiProviderInterface;
use App\Contracts\LayoutParserInterface;
use App\Contracts\StructuredDocumentRepositoryInterface;
use App\Repositories\StructuredDocumentRepository;
use Illuminate\Support\ServiceProvider;

final class ALPServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../../config/alp.php', 'alp');

        $this->app->singleton(ApryseClientInterface::class, AprysePhpClient::class);
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
        $this->app->scoped(StructuredDocumentRepositoryInterface::class, StructuredDocumentRepository::class);
        $this->app->scoped(StructuredDocumentService::class);

        $this->app->singleton(AiProviderInterface::class, LocalAiProvider::class);
        $this->app->singleton(AiManager::class, function ($app): AiManager {
            /** @var AiProviderInterface $provider */
            $provider = $app->make(AiProviderInterface::class);
            $defaultProvider = (string) $app['config']->get('alp.ai.default', 'local');

            return new AiManager(['local' => $provider], $defaultProvider);
        });
        $this->app->scoped(DocumentSummarizationService::class);
        $this->app->scoped(EntityExtractionService::class);
        $this->app->scoped(DocumentQaService::class);

        $this->app->singleton(LayoutParserInterface::class, DefaultLayoutParser::class);
        $this->app->singleton(PipelineManager::class, function ($app): PipelineManager {
            /** @var array<string, list<class-string<\App\Pipelines\Contracts\PipelineStepInterface>>> $pipelines */
            $pipelines = (array) $app['config']->get('alp.pipelines', []);

            return new PipelineManager($pipelines);
        });
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
