<?php

namespace Miaoxing\AppConfig;

use Miaoxing\Plugin\BasePlugin;

class Plugin extends BasePlugin
{
    /**
     * {@inheritdoc}
     */
    protected $name = '应用配置';

    /**
     * @var string
     */
    protected $description = '让每个应用支持独立的服务配置';

    public function onAppInit()
    {
        wei()->appConfig->load();
    }
}
