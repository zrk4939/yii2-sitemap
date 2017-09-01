<?php
/**
 * Created by PhpStorm.
 * User: Илья
 * Date: 01.09.2017
 * Time: 8:55
 */

namespace zrk4939\modules\sitemap\components;


use DOMDocument;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Object;
use yii\db\ActiveQuery;
use yii\helpers\Inflector;

class SiteMapComponent extends Object
{
    public $baseUrl;
    public $sitemaps = [];

    private $_models = [];
    private $_sitemaps = [];

    const FREQ_ALWAYS = 'always';
    const FREQ_HOURLY = 'hourly';
    const FREQ_DAILY = 'daily';
    const FREQ_WEEKLY = 'weekly';
    const FREQ_MONTHLY = 'monthly';
    const FREQ_YEARLY = 'yearly';
    const FREQ_NEVER = 'never';

    public function init()
    {
        parent::init();

        if (empty($this->baseUrl)) {
            throw new InvalidConfigException(Yii::t('yii', 'SiteMapComponent::baseUrl can not be empty'));
        }
    }

    public function createSiteMap(string $savePath)
    {
        foreach ($this->sitemaps as $sitemap) {
            $models = $this->getModels($sitemap['query']);
            $pages = $this->addModels($models);
            $mapUrl = $this->createPagesSiteMap($pages, $savePath, $sitemap['postfix']);

            if (key_exists('childsQuery', $sitemap)) {
                foreach ($models as $model) {
                    $childQuery = clone $sitemap['childsQuery'];
                    $linkChild = current(array_keys($sitemap['childLink']));
                    $linkModel = current($sitemap['childLink']);
                    $childQuery->andWhere([$linkChild => $model->getAttribute($linkModel)]);

                    $childModels = $this->getModels($childQuery);
                    $childPages = $this->addModels($childModels);
                    $mapUrl = $this->createPagesSiteMap($childPages, $savePath, Inflector::slug($model->getAttribute('name')));

                    $this->populateSitemapLoc($mapUrl);
                }
            }

            $this->populateSitemapLoc($mapUrl);
        }

        $this->createSitemapIndex($savePath);
    }

    public function dateToW3C($date)
    {
        if (is_int($date)) {
            return date(DATE_W3C, $date);
        } else {
            return date(DATE_W3C, strtotime($date));
        }
    }

    private function addModels(array $models, string $changeFreq = self::FREQ_HOURLY, int $priority = 1)
    {
        $pages = [];

        foreach ($models as $model) {
            $page = [
                'loc' => $model->getUrl(),
                'changefreq' => $changeFreq,
                'priority' => $priority
            ];

            if ($model->hasAttribute('updated_at')) {
                $page['lastmod'] = $this->dateToW3C($model->updated_at);
            }

            $pages[] = $page;
        }

        return $pages;
    }

    private function createPagesSiteMap(array $pages, string $path, string $postfix = '')
    {
        $dom = new DOMDocument('1.0', 'utf-8');
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

    private function createSitemapIndex($path)
    {
        $dom = new DOMDocument('1.0', 'utf-8');
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

    private function populateSitemapLoc($locUrl)
    {
        $this->_sitemaps[] = [
            'loc' => $locUrl,
            'lastmod' => $this->dateToW3C(time())
        ];
    }

    protected function getModels(ActiveQuery $query)
    {
        return $query->all();
    }
}