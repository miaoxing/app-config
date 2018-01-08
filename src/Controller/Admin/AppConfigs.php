<?php

namespace Miaoxing\AppConfig\Controller\Admin;

use Miaoxing\Config\Service\Config;
use Miaoxing\Plugin\BaseController;
use Wei\Request;

class AppConfigs extends BaseController
{
    protected $controllerName = '配置管理';

    protected $actionPermissions = [
        'index' => '列表',
        'new,create' => '添加',
        'edit,update' => '编辑',
        'destroy' => '删除',
        'editBatch,updateBatch' => '批量更新',
        'publish' => '发布',
    ];

    protected $displayPageHeader = true;

    public function indexAction($req)
    {
        switch ($req['_format']) {
            case 'json':
                $configs = wei()->appConfigModel()
                    ->desc('id')
                    ->limit($req['rows'])
                    ->page($req['page']);

                $configs->findAll();

                return $this->suc([
                    'data' => $configs,
                    'page' => (int) $req['page'],
                    'rows' => (int) $req['rows'],
                    'records' => $configs->count(),
                ]);

            default:
                return get_defined_vars();
        }
    }

    public function newAction($req)
    {
        return $this->editAction($req);
    }

    public function editBatchAction($req)
    {
        return get_defined_vars();
    }

    public function editAction($req)
    {
        $config = wei()->appConfigModel()->findId($req['id']);

        return get_defined_vars();
    }

    public function createAction($req)
    {
        return $this->updateAction($req);
    }

    public function updateAction(Request $req)
    {
        if (strpos($req['name'], Config::DELIMITER) === false) {
            return $this->err('名称需包含分隔符(' . Config::DELIMITER . ')');
        }

        $config = wei()->appConfigModel()->findId($req['id']);
        $config->save($req);

        return $this->suc([
            'data' => $config,
        ]);
    }

    public function updateBatchAction($req)
    {
        $reqConfigs = json_decode($req['configs'], true);
        if (json_last_error()) {
            return $this->err('解析JSON失败:' . json_last_error_msg());
        }
        if (!is_array($reqConfigs)) {
            return $this->err('值必需是JSON数组');
        }

        $configs = wei()->appConfigModel();
        foreach ($reqConfigs as $name => $value) {
            $configs[] = wei()->appConfigModel()
                ->findOrInit(['name' => $req['name'] . Config::DELIMITER . $name])
                ->fromArray([
                    // 优先设置type才能正确转换value的值
                    'type' => wei()->appConfig->detectType($value),
                    'value' => $value,
                ]);
        }

        $configs->db->transactional(function () use ($configs) {
            $configs->save();
        });

        return $this->suc();
    }

    public function destroyAction($req)
    {
        $config = wei()->appConfigModel()->findOneById($req['id']);

        $config->destroy();

        return $this->suc();
    }

    public function publishAction()
    {
        $ret = wei()->appConfig->publish();

        return $ret;
    }
}
