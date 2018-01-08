<?php

namespace MiaoxingDoc\AppConfig {

    /**
     * @property    \Miaoxing\AppConfig\Service\AppConfig $appConfig 应用配置
     *
     * @property    \Miaoxing\AppConfig\Service\AppConfigModel $appConfigModel 应用配置
     * @method      \Miaoxing\AppConfig\Service\AppConfigModel|\Miaoxing\AppConfig\Service\AppConfigModel[] appConfigModel()
     */
    class AutoComplete
    {
    }
}

namespace {

    /**
     * @return MiaoxingDoc\AppConfig\AutoComplete
     */
    function wei()
    {
    }

    /** @var Miaoxing\AppConfig\Service\AppConfig $appConfig */
    $appConfig = wei()->appConfig;

    /** @var Miaoxing\AppConfig\Service\AppConfigModel $appConfigModel */
    $appConfig = wei()->appConfigModel();

    /** @var Miaoxing\AppConfig\Service\AppConfigModel|Miaoxing\AppConfig\Service\AppConfigModel[] $appConfigModels */
    $appConfigs = wei()->appConfigModel();
}
