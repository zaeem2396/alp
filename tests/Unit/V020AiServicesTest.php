<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Repositories\StructuredDocumentRepository;
use App\Services\AI\AiManager;
use App\Services\AI\DocumentQaService;
use App\Services\AI\DocumentSummarizationService;
use App\Services\AI\EntityExtractionService;
use App\Services\AI\LocalAiProvider;
use App\Services\StructuredDocumentService;
use PHPUnit\Framework\TestCase;

final class V020AiServicesTest extends TestCase
{
    public function test_summarization_and_entity_extraction_store_artifacts(): void
    {
        $manager = new AiManager(['local' => new LocalAiProvider], 'local');
        $store = new StructuredDocumentService(new StructuredDocumentRepository);
        $summaryService = new DocumentSummarizationService($manager, $store);
        $entityService = new EntityExtractionService($manager, $store);

        $summary = $summaryService->summarize('doc-202', 'A very long paragraph for summarization tests.');
        $entities = $entityService->extract('doc-202', 'Invoice total is 12.99', [
            'amount' => '/\d+\.\d+/',
        ]);

        self::assertNotSame('', $summary);
        self::assertArrayHasKey('amount', $entities);
        self::assertNotNull($store->retrieve('doc-202'));
    }

    public function test_document_qa_returns_citations(): void
    {
        $manager = new AiManager(['local' => new LocalAiProvider], 'local');
        $qaService = new DocumentQaService($manager);

        $result = $qaService->ask('What is total?', ['Total: 45.00']);

        self::assertNotSame('', $result['answer']);
        self::assertSame([0], $result['citations']);
    }
}
