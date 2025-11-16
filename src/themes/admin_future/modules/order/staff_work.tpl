{* BEGIN: main *}
<div class="card">
    <div class="card-header text-bg-primary">
        <h5 class="mb-0"><i class="bi bi-calendar-check"></i> {$LANG->getModule('staff_work_manage')}</h5>
    </div>
    <div class="card-body">
        {* Bộ lọc *}
        <form action="{$smarty.const.NV_BASE_ADMINURL}index.php" method="get" class="mb-4">
            <input type="hidden" name="{$smarty.const.NV_LANG_VARIABLE}" value="{$smarty.const.NV_LANG_DATA}">
            <input type="hidden" name="{$smarty.const.NV_NAME_VARIABLE}" value="{$MODULE_NAME}">
            <input type="hidden" name="{$smarty.const.NV_OP_VARIABLE}" value="{$OP}">

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">{$LANG->getModule('staff')}</label>
                    <select name="staff_id" class="form-select">
                        <option value="0">{$LANG->getModule('all')}</option>
                        {foreach from=$STAFF_LIST item=staff}
                        <option value="{$staff.userid}" {if $staff.selected}selected{/if}>{$staff.full_name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">{$LANG->getModule('from_date')}</label>
                    <input type="date" name="from_date" value="{$FROM_DATE}" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label">{$LANG->getModule('to_date')}</label>
                    <input type="date" name="to_date" value="{$TO_DATE}" class="form-control">
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> {$LANG->getModule('filter')}</button>
                </div>
            </div>
        </form>

        {* Nút thêm mới *}
        <div class="mb-3">
            <a href="{$smarty.const.NV_BASE_ADMINURL}index.php?{$smarty.const.NV_LANG_VARIABLE}={$smarty.const.NV_LANG_DATA}&amp;{$smarty.const.NV_NAME_VARIABLE}={$MODULE_NAME}&amp;{$smarty.const.NV_OP_VARIABLE}=staff-work-content" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> {$LANG->getModule('staff_work_add')}
            </a>
        </div>

        {if not empty($WORK_LIST)}
        {* BEGIN: summary *}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-bg-info">
                    <div class="card-body">
                        <h6 class="card-title">{$LANG->getModule('total_work_hours')}</h6>
                        <h3 class="mb-0">{$TOTAL_HOURS}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-success">
                    <div class="card-body">
                        <h6 class="card-title">{$LANG->getModule('total_orders')}</h6>
                        <h3 class="mb-0">{$TOTAL_ORDERS}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-warning">
                    <div class="card-body">
                        <h6 class="card-title">{$LANG->getModule('total_revenue')}</h6>
                        <h3 class="mb-0">{$TOTAL_REVENUE}</h3>
                    </div>
                </div>
            </div>
        </div>
        {* END: summary *}

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>{$LANG->getModule('staff_name')}</th>
                        <th>{$LANG->getModule('work_date')}</th>
                        <th>{$LANG->getModule('shift')}</th>
                        <th>{$LANG->getModule('start_time')}</th>
                        <th>{$LANG->getModule('end_time')}</th>
                        <th>{$LANG->getModule('work_hours')}</th>
                        <th>{$LANG->getModule('order_count')}</th>
                        <th>{$LANG->getModule('total_revenue')}</th>
                        <th class="text-center">{$LANG->getModule('action')}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$WORK_LIST item=work}
                    <tr>
                        <td><strong>{$work.staff_name}</strong></td>
                        <td>{$work.work_date}</td>
                        <td><span class="badge bg-info">{$work.shift}</span></td>
                        <td>{$work.start_time}</td>
                        <td>{$work.end_time}</td>
                        <td><strong>{$work.work_hours}</strong></td>
                        <td>{$work.order_count}</td>
                        <td><strong>{$work.total_revenue}</strong></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{$work.edit_url}" class="btn btn-primary" title="{$LANG->getModule('edit')}">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-danger" onclick="confirmDelete({$work.work_id});" title="{$LANG->getModule('delete')}">
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
function confirmDelete(workId) {
    if (confirm('{$LANG->getModule("confirm_delete")}?')) {
        $.ajax({
            url: '{$smarty.const.NV_BASE_ADMINURL}index.php?{$smarty.const.NV_LANG_VARIABLE}={$smarty.const.NV_LANG_DATA}&{$smarty.const.NV_NAME_VARIABLE}={$MODULE_NAME}&{$smarty.const.NV_OP_VARIABLE}=staff-work-del',
            type: 'POST',
            data: { work_id: workId },
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
