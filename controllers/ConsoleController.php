<?php
/**
 * Created by PhpStorm.
 * User: Илья
 * Date: 21.03.2017
 * Time: 10:07
 */

namespace zrk4939\modules\sitemap\controllers;


use domain\modules\content\models\Category;
use domain\modules\content\models\Content;
use domain\modules\content\models\ContentCategory;
use domain\modules\content\models\ContentItem;
use domain\modules\content\models\ContentType;
use zrk4939\modules\sitemap\components\SiteMapComponent;
use zrk4939\modules\sitemap\helpers\SitemapHelper;
use Yii;
use yii\console\Controller;

class ConsoleController extends Controller
{
    public function actionCreate()
    {
        $cache = Yii::$app->cache;
        $key = 'iterationDate';
        $iterationDate = $cache->get($key);

        if ($iterationDate === false || $iterationDate < date('H')) {

            echo "start creating sitemaps...\n";

            /* @var $module \zrk4939\modules\sitemap\Module */
            $module = Yii::$app->getModule('sitemap');

            $module->createSiteMap();

            echo "Done!!\n";

            $iterationDate = date('H');
            $cache->set($key, $iterationDate);

            exit;
        }
        echo "Данные обновлялись не меньше часа назад ...\n";
        exit;
    }

    public function actionCleaning()
    {
        Yii::$app->cache->flush();

        echo "Вы очистили кэш \n";

        exit;
    }
}
