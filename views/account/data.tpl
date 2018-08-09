<tbody data-total="{$total}" class="wulaui">
{foreach $rows as $row}
    <tr rel="{$row.id}">
        <td></td>
        <td><input type="checkbox" value="{$row.id}" class="grp"/></td>
        <td>
            {if $row.avatar}
                <p class="thumb-sm">
                    <img src="{$row.avatra}" alt="" class="img-rounded"/>
                </p>
            {/if}
        </td>
        <td>{$row.id}</td>
        <td>
            {if $canEdit}
                <a href="{'weixin/account/edit'|app}/{$row.id}" class="new-item" data-ajax="dialog"
                   data-area="800px,550px" data-title="编辑公众号">{$row.name}</a>
            {else}
                {$row.name}
            {/if}
        </td>
        <td>{$types[$row.type]}</td>
        <td>{$row.wxid}</td>
        <td>{$row.origin_id}</td>
        <td>{if $row.authed}是{/if}</td>
        <td class="text-right">
            <a class="btn btn-xs btn-danger" href="{'weixin/account/del'|app}/{$row.id}" data-ajax
               data-confirm="你真的要删除这个公众号吗?">
                <i class="fa fa-trash-o"></i>
            </a>
        </td>
    </tr>
    <tr parent="{$row.id}">
        <td colspan="2"></td>
        <td colspan="8">
            <h4>接入设置</h4>
            <p>
                <strong>开发者ID(AppID):</strong>{$row.app_id} &nbsp;&nbsp; </p>
            <p>
                <strong>服务器地址(URL):</strong>{$row.base_url|default:$base_url|rtrim:'/'}{'weixin'|app}/{$row.id}
                <a href="javascript:" title="点击复制" class="copy"
                   data-clipboard-text="{$row.base_url|default:$base_url|rtrim:'/'}{'weixin'|app}/{$row.id}"> <i
                            class="fa fa-copy"></i> </a>
            </p>
            <p>
                <strong>令牌(Token):</strong>{$row.token}
                <a href="javascript:" title="点击复制" class="copy" data-clipboard-text="{$row.token}">
                    <i class="fa fa-copy"></i> </a>
            </p>
            <p>
                <strong>消息加解密密钥:</strong>{$row.aeskey}
                <a href="javascript:" title="点击复制" class="copy" data-clipboard-text="{$row.aeskey}"> <i
                            class="fa fa-copy"></i> </a> &nbsp;&nbsp; <strong>消息加解密方式:</strong>{$modes[$row.mode]}
            </p>
        </td>
    </tr>
    {foreachelse}
    <tr>
        <td colspan="10" class="text-center">无数据!</td>
    </tr>
{/foreach}
</tbody>