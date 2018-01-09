<?php

namespace Miaoxing\AppConfig\Service;

use Miaoxing\Config\Service\ConfigModel;
use Miaoxing\Plugin\BaseService;

use Wei\Cache;
use Wei\RetTrait;
use Wei\Wei;

/**
 * 应用配置
 *
 * @property Cache cache
 * @property Wei|\MiaoxingDoc\AppConfig\AutoComplete $wei
 */
class AppConfig extends BaseService
{
    use RetTrait;

    const DELIMITER = '.';

    /**
     * 配置文件的路径
     *
     * @var string
     */
    protected $configFile = 'data/configs/%s.php';

    /**
     * @var array
     */
    protected $typeMap = [
        'string' => ConfigModel::TYPE_STRING,
        'boolean' => ConfigModel::TYPE_BOOL,
        'integer' => ConfigModel::TYPE_INT,
        'double' => ConfigModel::TYPE_FLOAT,
        'array' => ConfigModel::TYPE_ARRAY,
        'object' => ConfigModel::TYPE_ARRAY,
        'resource' => ConfigModel::TYPE_INT,
        'NULL' => ConfigModel::TYPE_NULL,
    ];

    public function load()
    {
        $configs = $this->getLocalConfigs();

        if ($this->needsUpdate($configs)) {
            $configs = $this->writeConfigs();
        }

        $this->wei->setConfig($configs);
    }

    /**
     * 发布配置,可选同时设置一些配置
     *
     * @param array $data 要设置的配置
     * @return array
     */
    public function publish(array $data)
    {
        if ($data) {
            $this->set($data);
        }

        $this->updateVersion();

        $this->writeConfigs();

        return $this->suc();
    }

    public function writeConfigs()
    {
        $configs = $this->wei->appConfigModel()->findAll();
        $configs = $this->generateConfigs($configs);

        $file = $this->getConfigFile();
        $dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        file_put_contents($file, $this->generateContent($configs));

        return $configs;
    }

    public function getConfigFile()
    {
        return sprintf($this->configFile, $this->app->getNamespace());
    }

    /**
     * 设置一项或多项配置的值
     *
     * @param string|array $name
     * @param mixed $value
     * @return $this
     */
    public function set($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $item => $value) {
                $this->set($name, $value);
            }
        } else {
            $config = wei()->appConfigModel()->findOrInit(['name' => $name]);
            $config->save(['value' => $value]);
        }

        return $this;
    }

    protected function getVersion()
    {
        $version = $this->cache->get('appConfig.version', function () {
            return $this->wei->appConfigModel()->findOrInit(['name' => 'appConfig.version'])->value;
        });

        if (!$version) {
            $version = $this->updateVersion();
        }

        return $version;
    }

    protected function updateVersion()
    {
        $versionConfig = $this->wei->appConfigModel()->findOrInit(['name' => 'appConfig.version']);
        $versionConfig->save(['value' => date('Y-m-d H:i:s')]);
        $this->cache->set('appConfig.version', $versionConfig['value']);

        return $versionConfig['value'];
    }

    protected function getLocalConfigs()
    {
        if (is_file($file = $this->getConfigFile())) {
            return require $file;
        } else {
            return [];
        }
    }

    protected function needsUpdate(array $configs)
    {
        // 初始化版本号
        $version = $this->getVersion();

        return !isset($configs['appConfig']['version']) || $configs['appConfig']['version'] < $version;
    }

    protected function generateConfigs($configs)
    {
        $data = [];

        /** @var ConfigModel $config */
        foreach ($configs as $config) {
            // 从右边的点(.)拆分为两部分,兼容a.b.c的等情况
            $pos = strrpos($config['name'], static::DELIMITER);
            $service = substr($config['name'], 0, $pos);
            $option = substr($config['name'], $pos + 1);

            $data[$service][$option] = $config->getPhpValue();
        }

        return $data;
    }

    /**
     * 检测数据的类型
     *
     * @param mixed $value
     * @return int
     */
    public function detectType($value)
    {
        return $this->typeMap[gettype($value)];
    }

    /**
     * @param array $data
     * @return string
     */
    protected function generateContent(array $data)
    {
        return "<?php\n\nreturn " . var_export($data, true) . ";\n";
    }
}

