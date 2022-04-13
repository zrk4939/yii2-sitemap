<?php

namespace zrk4939\modules\sitemap;

use Yii;
use yii\db\ActiveQuery;
use yii\helpers\Inflector;

/**
 * SiteMap module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'zrk4939\modules\sitemap\controllers';
    public $storePath = '@runtime/sitemap';
    public $sitemaps = [];
    public $baseUrl;


    public $changefreq = self::FREQ_HOURLY;

    const FREQ_ALWAYS = 'always';
    const FREQ_HOURLY = 'hourly';
    const FREQ_DAILY = 'daily';
    const FREQ_WEEKLY = 'weekly';
    const FREQ_MONTHLY = 'monthly';
    const FREQ_YEARLY = 'yearly';
    const FREQ_NEVER = 'never';

    private $_sitemaps;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->storePath = Yii::getAlias($this->storePath);
        if (!is_dir($this->storePath)) {
            mkdir($this->storePath, 0775);
        }
        // custom initialization code goes here
    }

    /**
     *
     */
    public function createSiteMap()
    {
        foreach ($this->sitemaps as $sitemap) {
            $models = $this->getModels($sitemap['query']);
            $pages = $this->addModels($models, $this->changefreq, 1, $sitemap['url']);
            $mapLink = $this->createPagesSiteMap($pages, $this->storePath, $sitemap['postfix']);

            $this->populateSitemapLoc($mapLink);
        }

        $this->createSitemapIndex($this->storePath);
    }

    private function dateToW3C($date)
    {
        if (is_int($date)) {
            return date(DATE_W3C, $date);
        } else {
            return date(DATE_W3C, strtotime($date));
        }
    }

    /**
     * @param array $models
     * @param string $changeFreq
     * @param int $priority
     * @param callable $callable
     * @return array
     */
    private function addModels(array $models, string $changeFreq = self::FREQ_HOURLY, int $priority = 1, $callable)
    {
        $pages = [];

        foreach ($models as $model) {

            $page = [
                'loc' => call_user_func_array($callable, array($this->baseUrl, $model)),
                'changefreq' => $changeFreq,
                'priority' => $priority
            ];

            if (isset($model['updated_at'])) {
                $page['lastmod'] = $this->dateToW3C($model['updated_at']);
            }

            $pages[] = $page;
        }

        return $pages;
    }

    /**
     * @param array $pages
     * @param string $path
     * @param string $postfix
     * @return string
     */
    private function createPagesSiteMap(array $pages, string $path, string $postfix = '')
    {
        $dom = new \DOMDocument('1.0', 'utf-8');
        $urlset = $dom->createElement('urlset');
        $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        foreach ($pages as $page) {
            $url = $dom->createElement('url');

            foreach ($page as $key => $value) {
                if (is_a($value, 'DOMElement')) {
                    $elem = $dom->importNode($value, true);
                } else {
                    $elem = $dom->createElement($key);
                    $elem->appendChild($dom->createTextNode(htmlspecialchars($value, ENT_XML1, 'UTF-8')));
                }

                $url->appendChild($elem);
            }
            $urlset->appendChild($url);
        }

        $dom->appendChild($urlset);
        $dom->save($path . '/sitemap_' . $postfix . '.xml');
        $mapUrl = $this->baseUrl . '/sitemap_' . $postfix . '.xml';

        echo "$mapUrl was be created!\n";

        return $mapUrl;
    }

    /**
     * @param $path
     */
    private function createSitemapIndex($path)
    {
        $dom = new \DOMDocument('1.0', 'utf-8');
        $sitemapindex = $dom->createElement('sitemapindex');
        $sitemapindex->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        foreach ($this->_sitemaps as $sitemap) {
            $map = $dom->createElement('sitemap');
            foreach ($sitemap as $key => $value) {
                $elem = $dom->createElement($key);
                $elem->appendChild($dom->createTextNode($value));
                $map->appendChild($elem);
            }
            $sitemapindex->appendChild($map);
        }

        $dom->appendChild($sitemapindex);
        $dom->save($path . '/sitemap.xml');
    }

    /**
     * @param ActiveQuery $query
     * @return array|\yii\db\ActiveRecord[]
     */
    protected function getModels(ActiveQuery $query)
    {

        return $query->all();
    }

    /**
     * @param string $locUrl
     */
    private function populateSitemapLoc($locUrl)
    {
        $this->_sitemaps[] = [
            'loc' => $locUrl,
            'lastmod' => $this->dateToW3C(time())
        ];
    }
}
