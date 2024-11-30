<?php

namespace Services;

use App\Models\Listing;
use App\Models\Release;
use App\Services\SortingCriteriaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SortingCriteriaServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_prepare_sorting_criteria_applies_default_sorting()
    {
        $query = Release::query();
        $sortedQuery = SortingCriteriaService::prepareSortingCriteria($query, []);

        $this->assertStringContainsString('order by "listed_at" desc', $sortedQuery->toSql());
    }

    public function test_prepare_sorting_criteria_applies_main_table_sorting()
    {
        $query = Listing::query();
        $sortedQuery = SortingCriteriaService::prepareSortingCriteria($query, ['sort' => 'year-asc']);

        $this->assertStringContainsString('order by (select "year" from "releases"', $sortedQuery->toSql());
    }

    public function test_prepare_sorting_criteria_applies_related_table_sorting()
    {
        $query = Listing::query();
        $sortedQuery = SortingCriteriaService::prepareSortingCriteria($query, ['sort' => 'label-asc']);

        $expectedSubQuery = 'order by (select "label" from "releases"';
        $this->assertStringContainsString($expectedSubQuery, $sortedQuery->toSql());
    }

    public function test_prepare_sorting_criteria_throws_error_for_invalid_sort_key()
    {
        $this->expectException(\ErrorException::class);

        $query = Release::query();
        SortingCriteriaService::prepareSortingCriteria($query, ['sort' => 'invalid-key']);
    }
}
