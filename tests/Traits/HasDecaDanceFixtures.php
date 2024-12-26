<?php

namespace Tests\Traits;

trait HasDecaDanceFixtures
{

    protected function getSingleMockedArticleHtml(): string
    {
        return file_get_contents(base_path('tests/Fixtures/Decadance/single_article_page.html'));
    }

    protected function getMockedArticleListHtml(): false|string
    {
        return file_get_contents(base_path('tests/Fixtures/Decadance/articles_list_page.html'));
    }

    protected function getHomePageMockedHtml(): false|string
    {
        return file_get_contents(base_path('tests/Fixtures/Decadance/home_page.html'));
    }

    protected function getExpectedParagraphForSingleArticle(): string
    {
        return '<p>Ad inaugurare il catalogo sono loro stessi, nascosti dietro l’alias Razor Boy &amp;';
    }

    protected function getExpectedParagraphForListPage(): string
    {
        return "<p>La Time Records dei primi tempi è, come sottolineato nel libro, ispirata dai dischi che giungono dall’altra parte dell’Atlantico a firma Patrick Cowley e Bobby Orlando";
    }
}
