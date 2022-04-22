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

### console/config/main-local.php

```php
'components' => [
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'scriptUrl' => '',
            'baseUrl' => 'http://yii2.local',
        ],
    ],
```


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
		'sitemaps' => [
			[
				'query' => ArticlesRubrics::find()->asArray(),
				'postfix' => 'types',
				'url' => function($model) {
					return \yii\helpers\Url::toRoute("/{$model['type']}");
				}
			],
			[
				'query' => Pages::find()->active()->orderBy(['id' => SORT_DESC])->limit(300),
				'postfix' => 'pages',
				'url' => function($model) {
					return \yii\helpers\Url::toRoute("/{$model['link']}");
				}
            ],
        ]
    ],
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
