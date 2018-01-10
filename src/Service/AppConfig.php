<?php

namespace Miaoxing\AppConfig\Service;

use Miaoxing\Config\Service\Config;
use Wei\Wei;

/**
 * 应用配置
 *
 * @property Wei|\MiaoxingDoc\AppConfig\AutoComplete $wei
 */
class AppConfig extends Config
{
    /**
     * {@inheritdoc}
     */
    protected $configFile = 'data/app-configs/%s.php';

    /**
     * {@inheritdoc}
     */
    protected $versionKey = 'appConfig.version';

    /**
     * {@inheritdoc}
     */
    public function getConfigFile()
    {
        return sprintf($this->configFile, $this->app->getNamespace());
    }

    /**
     * {@inheritdoc}
     * @return AppConfigModel|AppConfigModel[]
     */
    protected function initModel()
    {
        return $this->wei->appConfigModel();
    }
}
