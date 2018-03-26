<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 26.03.2018
 * Time: 12:11
 */

namespace zrk4939\modules\sitemap\interfaces;


interface SiteMapInterface
{
    /**
     * @param array $urlManagerConfig
     * @return string
     */
    public function getSiteMapUrl($urlManagerConfig = []);
}
