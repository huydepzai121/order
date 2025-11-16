{* BEGIN: main *}
<div class="card">
    <div class="card-header text-bg-primary">
        <h5 class="mb-0"><i class="bi bi-menu-button-wide"></i> {$LANG->getModule('menu_list')}</h5>
    </div>
    <div class="card-body">
        {* Bộ lọc *}
        <form action="{$smarty.const.NV_BASE_ADMINURL}index.php" method="get" class="mb-4">
            <input type="hidden" name="{$smarty.const.NV_LANG_VARIABLE}" value="{$smarty.const.NV_LANG_DATA}">
            <input type="hidden" name="{$smarty.const.NV_NAME_VARIABLE}" value="{$MODULE_NAME}">
            <input type="hidden" name="{$smarty.const.NV_OP_VARIABLE}" value="{$OP}">

            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" value="{$SEARCH}" class="form-control" placeholder="{$LANG->getModule('search')}...">
                </div>

                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">{$LANG->getModule('all')}</option>
                        {foreach from=$CATEGORIES item=cat}
                        <option value="{$cat}" {if $cat eq $CATEGORY_SELECTED}selected{/if}>{$cat}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="-1">{$LANG->getModule('all')}</option>
                        {foreach from=$STATUS_OPTIONS key=key item=value}
                        <option value="{$key}" {if $key eq $STATUS_SELECTED}selected{/if}>{$value}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> {$LANG->getModule('filter')}</button>
                </div>
            </div>
        </form>

        {* Nút thêm mới *}
        <div class="mb-3">
            <a href="{$smarty.const.NV_BASE_ADMINURL}index.php?{$smarty.const.NV_LANG_VARIABLE}={$smarty.const.NV_LANG_DATA}&amp;{$smarty.const.NV_NAME_VARIABLE}={$MODULE_NAME}&amp;{$smarty.const.NV_OP_VARIABLE}=menu-content" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> {$LANG->getModule('menu_add')}
            </a>
        </div>

        {if not empty($MENU_ITEMS)}
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>{$LANG->getModule('menu_code')}</th>
                        <th>{$LANG->getModule('menu_name')}</th>
                        <th>{$LANG->getModule('category')}</th>
                        <th>{$LANG->getModule('price')}</th>
                        <th>{$LANG->getModule('status')}</th>
                        <th>{$LANG->getModule('weight')}</th>
                        <th class="text-center">{$LANG->getModule('action')}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$MENU_ITEMS item=item}
                    <tr>
                        <td><code>{$item.menu_code}</code></td>
                        <td><strong>{$item.menu_name}</strong></td>
                        <td><span class="badge bg-info">{$item.category}</span></td>
                        <td><strong>{$item.price}</strong></td>
                        <td><span class="badge bg-{$item.status_class}">{$item.status_text}</span></td>
                        <td>{$item.weight}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{$smarty.const.NV_BASE_ADMINURL}index.php?{$smarty.const.NV_LANG_VARIABLE}={$smarty.const.NV_LANG_DATA}&amp;{$smarty.const.NV_NAME_VARIABLE}={$MODULE_NAME}&amp;{$smarty.const.NV_OP_VARIABLE}=menu-content&amp;menu_id={$item.menu_id}" class="btn btn-primary" title="{$LANG->getModule('edit')}">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-danger" onclick="confirmDelete({$item.menu_id}, '{$item.menu_name|escape:'javascript'}');" title="{$LANG->getModule('delete')}">
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
function confirmDelete(menuId, menuName) {
    if (confirm('{$LANG->getModule("confirm_delete")} ' + menuName + '?')) {
        $.ajax({
            url: '{$smarty.const.NV_BASE_ADMINURL}index.php?{$smarty.const.NV_LANG_VARIABLE}={$smarty.const.NV_LANG_DATA}&{$smarty.const.NV_NAME_VARIABLE}={$MODULE_NAME}&{$smarty.const.NV_OP_VARIABLE}=menu-del',
            type: 'POST',
            data: {
                menu_id: menuId,
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
