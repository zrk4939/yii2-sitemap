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
 'sitemap' => [
             'class' => 'zrk4939\modules\sitemap\Module',
             'baseUrl' => 'http://example.com',
             'sitemaps' => [
                 [
                     'query' => \namespace\to\CategoryModel::find(),
                     'postfix' => 'types',
                     'childsQuery' => \namespace\to\ItemModel::find(),
                     'childLink' => ['type_id' => 'id'],
                 ],
                 [
                     'query' => \namespace\to\CategoryModel::find(),
                     'postfix' => 'categories',
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
$ php yii sitemap/console/create
```