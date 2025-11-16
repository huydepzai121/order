{* BEGIN: main *}
<div class="card">
    <div class="card-header text-bg-primary">
        <h5 class="mb-0"><i class="bi bi-list"></i> {$LANG->getModule('item_list')}</h5>
    </div>
    <div class="card-body">
        {* Search form *}
        <form action="{$smarty.const.NV_BASE_ADMINURL}index.php" method="get" class="mb-4">
            <input type="hidden" name="{$smarty.const.NV_LANG_VARIABLE}" value="{$smarty.const.NV_LANG_DATA}">
            <input type="hidden" name="{$smarty.const.NV_NAME_VARIABLE}" value="{$MODULE_NAME}">
            <input type="hidden" name="{$smarty.const.NV_OP_VARIABLE}" value="{$OP}">

            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" value="{$SEARCH}" class="form-control" placeholder="{$LANG->getModule('search')}...">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> {$LANG->getModule('search')}
                    </button>
                </div>
            </div>
        </form>

        {* Add button *}
        <div class="mb-3">
            <a href="{$smarty.const.NV_BASE_ADMINURL}index.php?{$smarty.const.NV_LANG_VARIABLE}={$smarty.const.NV_LANG_DATA}&amp;{$smarty.const.NV_NAME_VARIABLE}={$MODULE_NAME}&amp;{$smarty.const.NV_OP_VARIABLE}=content" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> {$LANG->getModule('item_add')}
            </a>
        </div>

        {if not empty($ITEMS)}
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>{$LANG->getModule('title')}</th>
                        <th>{$LANG->getModule('status')}</th>
                        <th>{$LANG->getModule('weight')}</th>
                        <th class="text-center">{$LANG->getModule('actions')}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$ITEMS item=item}
                    <tr>
                        <td>{$item.item_id}</td>
                        <td><strong>{$item.title}</strong></td>
                        <td>
                            <span class="badge bg-{if $item.status eq 1}success{else}secondary{/if}">
                                {if $item.status eq 1}{$LANG->getModule('status_active')}{else}{$LANG->getModule('status_inactive')}{/if}
                            </span>
                        </td>
                        <td>{$item.weight}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{$smarty.const.NV_BASE_ADMINURL}index.php?{$smarty.const.NV_LANG_VARIABLE}={$smarty.const.NV_LANG_DATA}&amp;{$smarty.const.NV_NAME_VARIABLE}={$MODULE_NAME}&amp;{$smarty.const.NV_OP_VARIABLE}=content&amp;item_id={$item.item_id}" class="btn btn-primary" title="{$LANG->getModule('edit')}">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-danger" onclick="confirmDelete({$item.item_id}, '{$item.title|escape:'javascript'}');" title="{$LANG->getModule('delete')}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
        {else}
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> {$LANG->getModule('no_data')}
        </div>
        {/if}

        {if not empty($GENERATE_PAGE)}
        <div class="mt-3">
            {$GENERATE_PAGE}
        </div>
        {/if}
    </div>
</div>

<script>
function confirmDelete(id, title) {
    if (confirm('{$LANG->getModule("confirm_delete")} "' + title + '"?')) {
        $.ajax({
            url: '{$smarty.const.NV_BASE_ADMINURL}index.php?{$smarty.const.NV_LANG_VARIABLE}={$smarty.const.NV_LANG_DATA}&{$smarty.const.NV_NAME_VARIABLE}={$MODULE_NAME}&{$smarty.const.NV_OP_VARIABLE}=del',
            type: 'POST',
            data: {
                item_id: id,
                checkss: '{$NV_CHECK}'
            },
            success: function(response) {
                if (response.status == 'OK') {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message);
                }
            }
        });
    }
}
</script>
{* END: main *}
