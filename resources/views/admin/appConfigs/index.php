<?php $view->layout() ?>

<?= $block('header-actions') ?>
<a class="js-publish btn btn-secondary" href="javascript:">发布配置</a>
<a class="btn btn-success" href="<?= $url('admin/app-configs/new') ?>">添加配置</a>
<a class="btn btn-success" href="<?= $url('admin/app-configs/batch-edit') ?>">批量更新配置</a>
<?= $block->end() ?>

<div class="row">
  <div class="col-12">
    <!-- PAGE CONTENT BEGINS -->
    <div class="table-responsive">

      <table class="js-config-table record-table table table-bordered table-hover">
        <thead>
        <tr>
          <th>名称</th>
          <th>类型</th>
          <th>值</th>
          <th>注释</th>
          <th>最后修改时间</th>
          <th class="t-6">操作</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
    <!-- /.table-responsive -->
    <!-- PAGE CONTENT ENDS -->
  </div>
  <!-- /col -->
</div>
<!-- /row -->

<script id="action-tpl" type="text/html">
  <a href="<%= $.url('admin/app-configs/%s/edit', id) %>">编辑</a>
  <a class="delete-record text-danger" href="javascript:"
    data-href="<%= $.url('admin/app-configs/%s/destroy', id) %>">删除</a>
</script>

<?= $block->js() ?>
<script>
  require(['plugins/admin/js/form', 'plugins/admin/js/data-table', 'plugins/app/libs/artTemplate/template.min'], function (form) {
    $('.js-config-form').loadParams().update(function () {
      $recordTable.reload($(this).serialize(), false);
    });

    var $recordTable = $('.js-config-table').dataTable({
      ajax: {
        url: $.queryUrl('admin/app-configs.json')
      },
      columns: [
        {
          data: 'name'
        },
        {
          data: 'typeLabel'
        },
        {
          data: 'value'
        },
        {
          data: 'comment'
        },
        {
          data: 'updatedAt'
        },
        {
          data: 'id',
          sClass: 'text-center',
          render: function (data, type, full) {
            return template.render('action-tpl', full);
          }
        }
      ]
    });

    $recordTable.deletable();

    $('.js-publish').click(function () {
      $.ajax({
        url: $.url('admin/app-configs/publish.json'),
        loading: true,
        success: function (ret) {
          $.msg(ret);
          $recordTable.reload();
        }
      });
    });
  });
</script>
<?= $block->end() ?>
