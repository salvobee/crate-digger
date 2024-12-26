<?php

namespace Tests\Feature\Services;

use App\Exceptions\WordpressCrawlerException;
use App\Models\Article;
use App\Services\WordpressCrawlerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\Traits\HasDecaDanceFixtures;

class WordpressCrawlerServiceTest extends TestCase
{
    use HasDecaDanceFixtures;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new WordpressCrawlerService();
    }

    use RefreshDatabase;

    protected $service;

    public function test_it_will_fetch_a_list_of_articles_pages()
    {
        Http::fake([
            'https://example.com' => Http::response($this->getHomePageMockedHtml(), 200),
        ]);
        $list = $this->service->listArticlesPages('https://example.com');
        $this->assertCount(96, $list);
        $this->assertEquals('https://decadancebook.wordpress.com/2024/06/', $list->first());
        $this->assertEquals('https://decadancebook.wordpress.com/2015/09/', $list->last());
    }

    public function test_it_fetches_and_saves_an_article()
    {
        Http::fake([
            'https://example.com/test-article' => Http::response($this->getSingleMockedArticleHtml(), 200),
        ]);

        $articleUrl = 'https://example.com/test-article';

        $article = $this->service->fetchSingleArticlePage($articleUrl);

        $this->assertInstanceOf(Article::class, $article);
        $this->assertTrue($article->exists);
        $this->assertEquals('Razor Boy & Mirror Man – Cutter Mix / Beyond Control (Rabbit City Records)', $article->title);
        $this->assertStringContainsString($this->getExpectedParagraphForSingleArticle(), $article->content);
        $this->assertEquals(Carbon::parse('2015-09-09 14:49:11.000000'), $article->published_at);
        $this->assertEquals('Decadance', $article->author);
        $this->assertEquals('example.com', $article->source);
        $this->assertTrue($article->tags->contains(fn($tag) => $tag->name === 'Aphex Twin'));
        $this->assertTrue($article->tags->contains(fn($tag) => $tag->name === 'Colin Faver'));
        $this->assertTrue($article->categories->contains(fn($category) => $category->name === 'Dischi Raccontati'));
    }

    public function test_it_fetches_and_saves_articles_from_a_list_page()
    {
        Http::fake([
            'https://example.com/articles' => Http::response(
                $this->getMockedArticleListHtml()
            ),
        ]);

        $this->service->fetchArticlesListPage('https://example.com/articles');

        $this->assertEquals(8, Article::query()->count());
        $article = Article::first();
        $this->assertNotNull($article);
        $this->assertEquals('Sentimento Espresso, il libro per il trentennale della Time Records', $article->title);
        $this->assertTrue(Str::contains($article->content, $this->getExpectedParagraphForListPage()));
        $this->assertEquals(Carbon::parse('2015-09-25T13:00:36.000000+0000'), $article->published_at);
        $this->assertEquals('Decadance', $article->author);
        $this->assertEquals('example.com', $article->source);
        $this->assertCount(18, $article->tags);
        $this->assertTrue($article->tags->contains(fn($tag) => $tag->name === 'Albertino'));
        $this->assertTrue($article->tags->contains(fn($tag) => $tag->name === 'Bobby Orlando'));
        $this->assertTrue($article->categories->contains(fn($category) => $category->name === 'Libri Raccontati'));
    }

    public function test_it_will_update_existing_single_article()
    {
        Http::fake([
            'https://example.com/test-article' => Http::response($this->getSingleMockedArticleHtml(), 200),
        ]);
        $articleUrl = 'https://example.com/test-article';
        // existing article
        Article::factory()->create([
            'source' => 'example.com',
            'title' => 'Razor Boy & Mirror Man – Cutter Mix / Beyond Control (Rabbit City Records)',
        ]);

        $article = $this->service->fetchSingleArticlePage($articleUrl);

        $this->assertInstanceOf(Article::class, $article);
        $this->assertTrue($article->exists);
        $this->assertEquals('Razor Boy & Mirror Man – Cutter Mix / Beyond Control (Rabbit City Records)', $article->title);
        $this->assertStringContainsString($this->getExpectedParagraphForSingleArticle(), $article->content);
        $this->assertEquals(Carbon::parse('2015-09-09 14:49:11.000000'), $article->published_at);
        $this->assertEquals('Decadance', $article->author);
        $this->assertEquals('example.com', $article->source);
    }

    public function test_it_will_throw_exception_when_error_occurs()
    {
        $this->expectException(WordpressCrawlerException::class);
        Http::fake([
            'https://example.com/test-article' => Http::response('', 404),
        ]);
        $this->service->fetchSingleArticlePage('https://example.com/test-article');
    }

}
