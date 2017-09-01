<?php

namespace zrk4939\modules\sitemap;

use Yii;

/**
 * SiteMap module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'zrk4939\modules\sitemap\controllers';
    public $storePath;
    public $sitemaps = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->storePath = Yii::getAlias('@frontend') . '/runtime/sitemap';
        if(!is_dir($this->storePath)){
            mkdir($this->storePath, 0775);
        }
        // custom initialization code goes here
    }
}
