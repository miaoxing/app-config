<?php

namespace Miaoxing\AppConfig\Controller\Admin;

use Miaoxing\Plugin\BaseController;
use Wei\Request;

class AppConfigs extends BaseController
{
    protected $displayPageHeader = true;

    public function indexAction($req)
    {
        switch ($req['_format']) {
            case 'json':
                $configs = wei()->appConfigModel()
                    ->desc('id')
                    ->limit($req['rows'])
                    ->page($req['page'])
                    ->findAll();

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

    public function batchEditAction($req)
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
        $ret = wei()->appConfigModel()->store($req);

        return $ret;
    }

    public function batchUpdateAction($req)
    {
        $ret = wei()->appConfig->batchUpdate($req);

        return $ret;
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
