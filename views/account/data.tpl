<tbody data-total="{$total}" class="wulaui">
{foreach $rows as $row}
    <tr>
        <td><input type="checkbox" value="{$row.id}" class="grp"/></td>
        <td>
            {if $row.avatar}
                <p class="thumb-sm">
                    <img src="{$row.avatra}" alt="" class="img-rounded"/>
                </p>
            {/if}
        </td>
        <td>{$row.id}</td>
        <td>{$row.name}</td>
        <td>{$types[$row.type]}</td>
        <td>{$row.wxid}</td>
        <td>{$row.origin_id}</td>
        <td>{$row.desc|escape}</td>
        <td>{if $row.authed}是{/if}</td>
        <td class="text-right">
            <a href="" class="btn btn-xs btn-default">
                <i class="fa fa-cog"></i>
            </a>
            <a class="btn btn-xs btn-danger" href="{'weixin/account/del'|app}/{$row.id}" data-ajax
               data-confirm="你真的要删除这个公众号吗?">
                <i class="fa fa-trash-o"></i>
            </a>
        </td>
    </tr>
    {foreachelse}
    <tr>
        <td colspan="10" class="text-center">无数据!</td>
    </tr>
{/foreach}
</tbody>