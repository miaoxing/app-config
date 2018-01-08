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
class AppConfigModel extends BaseModelV2
{
    use HasAppIdTrait;
    use SoftDeleteTrait;
    use AppConfigTrait;

    protected $defaultCasts = [
        'value' => 'mixed'
    ];

    protected $decoder = 'unserialize';

    protected $encoder = 'serialize';

    protected $virtual = [
        'type_label'
    ];

    protected function getTypeLabelAttribute()
    {
        return wei()->configModel->getConstantLabel('type', $this['type']);
    }

    protected function getValueAttribute()
    {
        return $this->data['value'] ? call_user_func($this->decoder, $this->data['value']) : null;
    }

    protected function setValueAttribute($value)
    {
        $value = $this->covert($value, $this->get('type'));
        $this->data['value'] = call_user_func($this->encoder, $value);
    }

    /**
     * @param string $value
     * @param int $type
     * @return mixed
     */
    protected function covert($value, $type)
    {
        switch ($type) {
            case ConfigModel::TYPE_STRING:
                return (string) $value;

            case ConfigModel::TYPE_INT:
                return (int) $value;

            case ConfigModel::TYPE_FLOAT:
                return (float) $value;

            case ConfigModel::TYPE_BOOL:
                return (bool) $value;

            case ConfigModel::TYPE_ARRAY:
                return json_decode($value, true);

            default:
                return $value;
        }
    }
}
