<?php

namespace Miaoxing\AppConfig\Service;

use Miaoxing\Config\Service\ConfigModel;
use Miaoxing\Plugin\Model\HasAppIdTrait;
use Miaoxing\Plugin\Model\SoftDeleteTrait;

/**
 * 应用配置
 */
class AppConfigModel extends ConfigModel
{
    use HasAppIdTrait;
    use SoftDeleteTrait;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $this->casts['app_id'] = 'int';
    }
}
