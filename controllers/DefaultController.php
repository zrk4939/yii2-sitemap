<?php
/**
 * Created by PhpStorm.
 * User: Илья
 * Date: 21.03.2017
 * Time: 10:07
 */

namespace zrk4939\modules\sitemap\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DefaultController extends Controller
{

    public function actionSitemap($name)
    {
        $module = Yii::$app->getModule('sitemap');
        $savePath   = $module->storePath;
        if(file_exists($savePath . '/'. $name)){
            Yii::$app->response->format = \yii\web\Response::FORMAT_XML;
            ob_start("ob_gzhandler");
            echo file_get_contents($savePath . '/'. $name);
            ob_end_flush();
            Yii::$app->end();
        }

        throw new NotFoundHttpException('File not found!');
    }
}
