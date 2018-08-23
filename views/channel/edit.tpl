<div class="container-fluid m-t-md">
    <div class="row wulaui">
        <div class="col-sm-12">
            <form id="core-admin-form" name="SettingForm" action="{'weixin/channel/save'|app}" data-validate="{$rules|escape}"
                  data-ajax method="post" role="form" class="form-horizontal {if $script}hidden{/if}"  data-loading
                  style="padding-top: 10px;">
                {$form|render}
            </form>
        </div>

    </div>

</div>