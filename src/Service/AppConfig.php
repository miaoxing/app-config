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
     * @return array
     */
    public function publish()
    {
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

            $data[$service][$option] = $config->value;
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
     * 转换数据为可存储的字符串
     *
     * @param mixed $value
     * @return string
     */
    public function encode($value)
    {
        if (!is_scalar($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return $value;
    }

    protected function generateContent($data)
    {
        return "<?php\n\nreturn " . var_export($data, true) . ";\n";
    }
}

