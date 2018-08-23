<div class="hbox stretch wulaui layui-hide" id="channel-list">
    <section class="vbox">
        <header class="header bg-light clearfix b-b">
            <div class="row m-t-sm">
                <div class="col-sm-6 m-b-xs">
                    <a href="{'weixin/channel/edit'|app}" class="btn btn-sm btn-success edit-admin" data-ajax="dialog"
                       data-area="700px,auto" data-title="新的渠道">
                        <i class="fa fa-plus"></i> 添加渠道
                    </a>
                </div>
                <div class="col-xs-6 text-right m-b-xs">
                    <form data-table-form="#table" id="search-form" class="form-inline">
                        <input type="hidden" name="deleted" id="deleted"/>
                        <div class="input-group input-group-sm">
                            <input id="search" data-expend="300" type="text" name="q" class="input-sm form-control"
                                   placeholder="{'Search'|t}" autocomplete="off"/>
                            <span class="input-group-btn">
                                <button class="btn btn-sm btn-info" id="btn-do-search" type="submit">Go!</button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </header>
        <section class="w-f">
            <div class="table-responsive">
                <table id="table" data-auto data-table="{'weixin/channel/data'|app}" data-sort="channel,d"
                       style="min-width: 800px">
                    <thead>
                    <tr>
                        <th width="100">渠道名</th>
                        <th width="100">标识</th>
                        <th width="120">点击量</th>
                        <th width="120">授权人数</th>
                        <th width="100">创建时间</th>
                        <th width="100"></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </section>
        <footer class="footer b-t">
            <div data-table-pager="#table" data-limit="10"></div>
        </footer>
    </section>
    <aside class="aside aside-xs b-l hidden-xs">
        <div class="vbox">
            <header class="bg-light dk header b-b">
                <p>状态</p>
            </header>
            <section class="hidden-xs scrollable m-t-xs">
                <ul class="nav nav-pills nav-stacked no-radius" id="task-status">
                    <li class="active">
                        <a href="javascript:;"> 全部 </a>
                    </li>
                    {foreach $deleted as $id=>$name}
                        <li>
                            <a href="javascript:;" rel="{$id}" title="{$name}"> {$name}</a>
                        </li>
                    {/foreach}
                </ul>
            </section>
        </div>
    </aside>
    <a class="hidden edit-task" id="for-edit-task"></a>
</div>
<script>
	layui.use(['jquery', 'bootstrap', 'wulaui'], function ($, b, wui) {
		var group = $('#task-status'), table = $('#table');
		group.find('a').click(function () {
			var me = $(this), mp = me.closest('li');
			if (mp.hasClass('active')) {
				return;
			}
			group.find('li').not(mp).removeClass('active');
			mp.addClass('active');
			$('#deleted').val(me.attr('rel'));
			$('#search-form').submit();
			return false;
		});

		$('#channel-list').on('before.dialog', '.edit-admin', function (e) {
			e.options.btn = ['保存', '取消'];
			e.options.yes = function () {
				$('#core-admin-form').data('dialogId', layer.index).submit();
				return false;
			};
		}).removeClass('layui-hide');

		$('body').on('ajax.success', '#core-admin-form', function () {
			layer.closeAll();
			table.reload();
		});
		$('#btn-reload').click(function () {
			table.reload();
		});
	})
</script>