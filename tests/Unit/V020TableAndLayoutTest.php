<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\Layout\DefaultLayoutParser;
use App\Services\LayoutParsingService;
use App\Services\TableDetectionService;
use PHPUnit\Framework\TestCase;

final class V020TableAndLayoutTest extends TestCase
{
    public function test_detects_table_cells_from_csv_like_text(): void
    {
        $service = new TableDetectionService();
        $result = $service->detect("item,amount\nBook,12.00");

        self::assertCount(1, $result['tables']);
        self::assertGreaterThan(0, count($result['tables'][0]['cells']));
    }

    public function test_parses_text_into_layout_zones(): void
    {
        $service = new LayoutParsingService(new DefaultLayoutParser());
        $layout = $service->parse("Invoice\nLine one\nLine two");

        self::assertSame(1, $layout['pages']);
        self::assertSame('header', $layout['zones'][0]['type']);
    }
}
