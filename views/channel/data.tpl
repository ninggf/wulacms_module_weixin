<tbody data-total="{$total}" class="wulaui">
{foreach $rows as $row}
    <tr>
        <td>{$row.channel_name}</td>
        <td>{$row.channel}</td>
        <td>{$row.click_num}</td>
        <td>{$row.auth_num}</td>
        <td>{$row.create_time|date_format:'%Y-%m-%d %H:%M:%S'}</td>
        <td class="text-right">
            <a href="{'weixin/channel/edit'|app}/{$row.channel}" data-ajax="dialog" data-area="600px,auto"
               data-title="编辑『{$row.title|escape}』" class="btn btn-xs btn-primary edit-admin">
                <i class="fa fa-pencil-square-o"></i>
            </a>
            <a href="{'weixin/channel/del'|app}/{$row.channel}" data-confirm="你真的要删除?" data-ajax
               class="btn btn-xs btn-danger">
                <i class="fa fa-trash-o"></i>
            </a>
        </td>
    </tr>
    {foreachelse}
    <tr>
        <td colspan="5" class="text-center">暂无相关数据!</td>
    </tr>
{/foreach}
</tbody>