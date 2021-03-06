<?php $view->layout() ?>

<?= $block('header-actions') ?>
<a class="btn btn-secondary float-right" href="<?= $url('admin/app-configs') ?>">返回列表</a>
<?= $block->end() ?>

<div class="row">
  <div class="col-12">
    <form class="form-horizontal js-config-form" role="form" method="post">
      <div class="form-group">
        <label class="col-lg-2 control-label" for="name">
          <span class="text-warning">*</span>
          名称
        </label>

        <div class="col-lg-4">
          <input type="text" name="name" id="name" class="form-control" required>
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-2 control-label" for="type">
          <span class="text-warning">*</span>
          类型
        </label>

        <div class="col-lg-4">
          <select class="js-type form-control" name="type" id="type" required>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-2 control-label" for="value">
          <span class="text-warning">*</span>
          值
        </label>

        <div class="col-lg-4">
          <textarea class="form-control" name="value" id="value" required></textarea>
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-2 control-label" for="comment">
          注释
        </label>

        <div class="col-lg-4">
          <input type="text" class="form-control" name="comment" id="comment">
        </div>
      </div>

      <div class="clearfix form-actions form-group">
        <input type="hidden" name="id" id="id">

        <div class="offset-lg-2">
          <button class="btn btn-primary" type="submit">
            <i class="fa fa-check bigger-110"></i>
            提交
          </button>
          &nbsp; &nbsp; &nbsp;
          <a class="btn btn-secondary" href="<?= $url('admin/app-configs') ?>">
            <i class="fa fa-undo bigger-110"></i>
            返回列表
          </a>
        </div>
      </div>
    </form>
  </div>
</div>

<?= $block->js() ?>
<script>
  require([
    'plugins/admin/js/form',
    'plugins/app/js/validation',
    'plugins/app/libs/jquery.populate/jquery.populate'
  ], function (form) {
    form.toOptions($('.js-type'), <?= json_encode(wei()->configModel->getConsts('type')) ?>, 'id', 'label');

    var config = <?= $config->toJson() ?>;

    $('.js-config-form')
      .populate(config)
      .ajaxForm({
        url: $.url('admin/app-configs/create'),
        dataType: 'json',
        beforeSubmit: function (arr, $form, options) {
          return $form.valid();
        },
        success: function (ret) {
          $.msg(ret, function () {
            if (ret.code === 1) {
              window.location.href = $.url('admin/app-configs');
            }
          })
        }
      })
      .validate();
  });
</script>
<?= $block->end() ?>

