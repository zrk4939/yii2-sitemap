<?php
/**
 * Created by PhpStorm.
 * User: Илья
 * Date: 21.03.2017
 * Time: 10:07
 */

namespace zrk4939\modules\sitemap\controllers;

use DOMDocument;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class DefaultController extends Controller
{

    public function actionSitemap($name)
    {
        /** @var \zrk4939\modules\sitemap\Module $module */
        $module = Yii::$app->getModule('sitemap');
        $savePath = $module->storePath;
        if (file_exists($savePath . '/' . $name)) {
            Yii::$app->response->format = Response::FORMAT_RAW;
            $headers = Yii::$app->response->headers;
            $headers->add('Content-Type', 'text/xml');

            $doc = new DOMDocument();
            $doc->load($savePath . '/' . $name);
            return $doc->saveXML();
        }

        throw new NotFoundHttpException('File not found!');
    }
}
