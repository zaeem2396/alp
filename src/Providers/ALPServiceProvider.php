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
use App\Services\MetadataExtractionService;
use App\Services\TextExtractionService;
use Illuminate\Support\ServiceProvider;

final class ALPServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../../config/alp.php', 'alp');

        $this->app->singleton(ApryseClientInterface::class, AprysePhpClient::class);
        $this->app->singleton(DocumentRepositoryInterface::class, DocumentRepository::class);
        $this->app->singleton(DocumentStorageInterface::class, DocumentStorageService::class);
        $this->app->singleton(PdfNormalizer::class);
        $this->app->singleton(DocxNormalizer::class);
        $this->app->singleton(DocumentNormalizerService::class, function ($app): DocumentNormalizerService {
            return new DocumentNormalizerService([
                $app->make(PdfNormalizer::class),
                $app->make(DocxNormalizer::class),
            ]);
        });

        $this->app->singleton(TextExtractionService::class);
        $this->app->singleton(MetadataExtractionService::class);
        $this->app->singleton(DocumentIngestionService::class);
        $this->app->singleton(DocumentService::class);
        $this->app->singleton(DocumentManager::class);
        $this->app->singleton(PipelineManager::class);
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
