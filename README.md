# yii2-sitemap

Site Map generator module for the [Yii2](http://www.yiiframework.ru/) framework

## Installation

add
```
"zrk4939/yii2-sitemap": "@dev",
```
to the require section of your `composer.json` file.

and
```
{
    "type": "vcs",
    "url": "https://github.com/zrk4939/yii2-sitemap.git"
}
```
to the repositories array of your `composer.json` file.

## Usage

### main.php

```php
'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache', // Используем хранилище yii\caching\FileCache
            'cachePath' => '@common/runtime/cache' // Храним кэш в common/runtime/cache
        ],
    ],

 'sitemap' => [
        'class' => 'zrk4939\modules\sitemap\Module',
        'storePath' => '@frontend/runtime/sitemap',
        'baseUrl' => $params['frontendUrl'],
        'urlManagerConfig' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'baseUrl' => $params['frontendUrl'],
            'rules' => require(dirname(__DIR__, 2) . '/frontend/config/urlRules.php'),
        ],
        'divideCounts' => 1000,
        'sitemaps' => [
            [
                'query' => \domain\modules\content\models\ContentType::find(),
                'postfix' => 'types',
            ],
            [
                'query' => \domain\modules\content\models\ContentCategory::find(),
                'postfix' => 'categories',
            ],
            [
                'query' => \domain\modules\content\models\ContentItem::find(),
                'postfix' => 'content-items',
                'iterationLimit' => 1000,
                'iterationCount' => \domain\modules\content\models\ContentItem::find()->asArray(),
            ],
        ]
    ],
```

### AR MODEL
Your model must be implemented and declared ::getSiteMapUrl method

```php
class ContentItem extends ActiveRecord implements SiteMapInterface{
    ...
    public function getSiteMapUrl($config = [])
    {
        /** @var yii\web\UrlManager $urlManager */
        $urlManager = Yii::createObject($config);

        return urldecode($urlManager->createAbsoluteUrl(['/content/default/index', 'url' => $this->slug], true));
    }
}
```

### UrlManager rule
```
'<name:^sitemap([-0-9a-zA-Z_]+)?.xml>' => 'sitemap/default/sitemap',
```

### console
```
$ php yii sitemap/console/create //создание sitemap.xml
$ php yii sitemap/console/cleaning //очиска кеш
```
