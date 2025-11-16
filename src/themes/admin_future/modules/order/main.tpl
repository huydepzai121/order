{* BEGIN: main *}
<div class="card">
    <div class="card-header text-bg-primary">
        <h5 class="mb-0"><i class="bi bi-receipt"></i> {$LANG->getModule('order_list')}</h5>
    </div>
    <div class="card-body">
        {* Bộ lọc *}
        <form action="{$smarty.const.NV_BASE_ADMINURL}index.php" method="get" class="mb-4">
            <input type="hidden" name="{$smarty.const.NV_LANG_VARIABLE}" value="{$smarty.const.NV_LANG_DATA}">
            <input type="hidden" name="{$smarty.const.NV_NAME_VARIABLE}" value="{$MODULE_NAME}">
            <input type="hidden" name="{$smarty.const.NV_OP_VARIABLE}" value="{$OP}">

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">{$LANG->getModule('search')}</label>
                    <input type="text" name="search" value="{$SEARCH}" class="form-control" placeholder="{$LANG->getModule('search')}...">
                </div>

                <div class="col-md-2">
                    <label class="form-label">{$LANG->getModule('status')}</label>
                    <select name="status" class="form-select">
                        <option value="-1">{$LANG->getModule('all')}</option>
                        {foreach from=$STATUS_LIST key=key item=value}
                        <option value="{$key}" {if $key eq $STATUS_SELECTED}selected{/if}>{$value}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">{$LANG->getModule('payment_status')}</label>
                    <select name="payment_status" class="form-select">
                        <option value="-1">{$LANG->getModule('all')}</option>
                        {foreach from=$PAYMENT_STATUS_LIST key=key item=value}
                        <option value="{$key}" {if $key eq $PAYMENT_STATUS_SELECTED}selected{/if}>{$value}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">{$LANG->getModule('staff')}</label>
                    <select name="staff_id" class="form-select">
                        <option value="0">{$LANG->getModule('all')}</option>
                        {foreach from=$STAFF_LIST item=staff}
                        <option value="{$staff.userid}" {if $staff.userid eq $STAFF_ID_SELECTED}selected{/if}>{$staff.full_name}</option>
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

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> {$LANG->getModule('filter')}</button>
                </div>
            </div>
        </form>

        {* Nút thêm mới *}
        <div class="mb-3">
            <a href="{$smarty.const.NV_BASE_ADMINURL}index.php?{$smarty.const.NV_LANG_VARIABLE}={$smarty.const.NV_LANG_DATA}&amp;{$smarty.const.NV_NAME_VARIABLE}={$MODULE_NAME}&amp;{$smarty.const.NV_OP_VARIABLE}=content" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> {$LANG->getModule('order_add')}
            </a>
        </div>

        {if not empty($ORDERS)}
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>{$LANG->getModule('order_code')}</th>
                        <th>{$LANG->getModule('customer_name')}</th>
                        <th>{$LANG->getModule('customer_phone')}</th>
                        <th>{$LANG->getModule('order_date')}</th>
                        <th>{$LANG->getModule('total_amount')}</th>
                        <th>{$LANG->getModule('status')}</th>
                        <th>{$LANG->getModule('payment_status')}</th>
                        <th>{$LANG->getModule('staff')}</th>
                        <th class="text-center">{$LANG->getModule('action')}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$ORDERS item=order}
                    <tr>
                        <td><strong>{$order.order_code}</strong></td>
                        <td>{$order.customer_name}</td>
                        <td>{$order.customer_phone}</td>
                        <td>{$order.order_date}</td>
                        <td><strong>{$order.total_amount}</strong></td>
                        <td><span class="badge bg-{$order.status_class}">{$order.status}</span></td>
                        <td><span class="badge bg-{$order.payment_status_class}">{$order.payment_status}</span></td>
                        <td>{$order.staff_name}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{$smarty.const.NV_BASE_ADMINURL}index.php?{$smarty.const.NV_LANG_VARIABLE}={$smarty.const.NV_LANG_DATA}&amp;{$smarty.const.NV_NAME_VARIABLE}={$MODULE_NAME}&amp;{$smarty.const.NV_OP_VARIABLE}=content&amp;order_id={$order.order_id}" class="btn btn-primary" title="{$LANG->getModule('edit')}">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-danger" onclick="confirmDelete({$order.order_id}, '{$order.order_code|escape:'javascript'}');" title="{$LANG->getModule('delete')}">
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
function confirmDelete(orderId, orderCode) {
    if (confirm('{$LANG->getModule("confirm_delete")} ' + orderCode + '?')) {
        $.ajax({
            url: '{$smarty.const.NV_BASE_ADMINURL}index.php?{$smarty.const.NV_LANG_VARIABLE}={$smarty.const.NV_LANG_DATA}&{$smarty.const.NV_NAME_VARIABLE}={$MODULE_NAME}&{$smarty.const.NV_OP_VARIABLE}=del',
            type: 'POST',
            data: {
                order_id: orderId,
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
