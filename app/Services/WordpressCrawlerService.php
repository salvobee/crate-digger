<?php

namespace App\Services;

use App\Exceptions\WordpressCrawlerException;
use App\Models\Article;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class WordpressCrawlerService
{
    public function listArticlesPages(string $blogUrl): Collection
    {
        $content_dom = $this->fetchUrlContentDom($blogUrl);
        return collect($content_dom->filter('aside.widget_archive ul li a')->each(fn (Crawler $node) => $node->attr('href')));
    }

    /**
     * @throws WordpressCrawlerException
     */
    public function fetchArticlesListPage(string $listPageUrl): Collection
    {
        $content_dom = $this->fetchUrlContentDom($listPageUrl);
        $source = $this->getSourceFromUrl($listPageUrl);
        $articles = collect();
        $content_dom->filter('article')->each(
            fn (Crawler $node) => $articles->push($this->storeArticleFromNode($node, $source))
        );
        return $articles;
    }

    /**
     * @throws WordpressCrawlerException
     */
    public function fetchSingleArticlePage(string $articleUrl): Article
    {
        $content_dom = $this->fetchUrlContentDom($articleUrl);
        $source = $this->getSourceFromUrl($articleUrl);
        $node = $content_dom->filter('article')->first();
        return $this->storeArticleFromNode($node, $source);
    }

    /**
     * @throws WordpressCrawlerException
     */
    private function fetchUrlContentDom(string $url): Crawler
    {
        $response = Http::get($url);

        if ($response->getStatusCode() !== 200) {
            throw new WordpressCrawlerException("Unable to fetch article list from URL: $url");
        }

        $html = $response->getBody()->getContents();
        return new Crawler($html);
    }

    private function getSourceFromUrl(string $url): string
    {
        $url_parse = parse_url($url);
        return $url_parse['host'];
    }

    private function storeArticleFromNode(Crawler $node, string $source): Article
    {
        $title = $node->filter('h1.entry-title')->text();

        $content = $node->filter('div.entry-content')->html();

        $publishedAt = $node->filter('time.entry-date.published')->attr('datetime');

        $author = $node->filter('span.author.vcard > a')->text();

        $tags = $node->filter('span.tags-links a')
            ->each(fn ($tagNode) =>  $tagNode->text());

        return Article::updateOrCreate(
            ['source' => $source, 'title' =>  $title],
            [
                'content' => $content,
                'published_at' => $publishedAt,
                'author' => $author,
                'tags' => $tags
            ]
        );
    }
}
