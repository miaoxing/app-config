<?php

namespace Miaoxing\AppConfig\Migration;

use Miaoxing\Plugin\BaseMigration;

class V20180108162511CreateAppConfigsTable extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->schema->table('app_configs')
            ->id()
            ->int('app_id')
            ->string('name')
            ->text('value')
            ->tinyInt('type')->comment('值的类型,默认0为字符串')
            ->string('comment')
            ->timestamps()
            ->userstamps()
            ->softDeletable()
            ->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->dropIfExists('app_configs');
    }
}
