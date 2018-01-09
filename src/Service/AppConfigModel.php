<?php

namespace Miaoxing\AppConfig\Service;

use Miaoxing\AppConfig\Metadata\AppConfigTrait;
use Miaoxing\Config\Service\ConfigModel;
use Miaoxing\Plugin\BaseModelV2;
use Miaoxing\Plugin\Model\HasAppIdTrait;
use Miaoxing\Plugin\Model\SoftDeleteTrait;

/**
 * 应用配置
 */
class AppConfigModel extends ConfigModel
{
    use HasAppIdTrait;
    use SoftDeleteTrait;
    use AppConfigTrait;
}
