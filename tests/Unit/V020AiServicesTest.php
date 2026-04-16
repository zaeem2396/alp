<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Contracts\EntityDetectorInterface;
use App\Contracts\SummarizerInterface;
use App\Repositories\StructuredDocumentRepository;
use App\Services\AI\AiManager;
use App\Services\AI\AnthropicProvider;
use App\Services\AI\DocumentQaService;
use App\Services\AI\DocumentSummarizationService;
use App\Services\AI\EntityExtractionService;
use App\Services\AI\LocalAiProvider;
use App\Services\AI\OpenAiProvider;
use App\Services\StructuredDocumentService;
use PHPUnit\Framework\TestCase;

final class V020AiServicesTest extends TestCase
{
    public function test_summarization_and_entity_extraction_store_artifacts(): void
    {
        $store = new StructuredDocumentService(new StructuredDocumentRepository);
        $summaryService = new DocumentSummarizationService(new class implements SummarizerInterface
        {
            public function summarize(string $text): string
            {
                return mb_strimwidth($text, 0, 20, '...');
            }
        }, $store);
        $entityService = new EntityExtractionService(new class implements EntityDetectorInterface
        {
            public function detect(string $text): array
            {
                preg_match('/\d+\.\d+/', $text, $amountMatches);

                return [
                    'amount' => [
                        'value' => $amountMatches[0] ?? null,
                        'confidence' => 0.9,
                    ],
                ];
            }
        }, $store);

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
        $manager = new AiManager(['local' => new LocalAiProvider, 'openai' => new OpenAiProvider], 'local');
        $qaService = new DocumentQaService($manager);

        $result = $qaService->ask('What is total?', ['Total: 45.00']);

        self::assertNotSame('', $result['answer']);
        self::assertSame([0], $result['citations']);
    }

    public function test_ai_manager_supports_multiple_providers(): void
    {
        $manager = new AiManager([
            'local' => new LocalAiProvider,
            'openai' => new OpenAiProvider,
            'anthropic' => new AnthropicProvider,
        ], 'local');

        self::assertStringStartsWith('[openai]', $manager->provider('openai')->summarize('hello world'));
        self::assertStringStartsWith('[anthropic]', $manager->provider('anthropic')->summarize('hello world'));
    }

    public function test_document_qa_whitespace_only_chunk_has_no_citations(): void
    {
        $provider = new LocalAiProvider;
        $result = $provider->answerQuestion('What?', ['   ']);

        self::assertStringContainsString('No context available', $result['answer']);
        self::assertSame([], $result['citations']);
    }
}
