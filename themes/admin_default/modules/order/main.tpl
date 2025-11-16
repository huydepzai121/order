<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">{$LANG.order_list}</h4>
        <a href="{$BASE_URL}&{$smarty.const.NV_OP_VARIABLE}=order-add" class="btn btn-primary">
            <i class="fa fa-plus"></i> {$LANG.order_add}
        </a>
    </div>
    <div class="card-body">
        <!-- Bộ lọc tìm kiếm -->
        <form action="{$BASE_URL}" method="get" class="mb-3">
            <input type="hidden" name="{$smarty.const.NV_LANG_VARIABLE}" value="{$smarty.const.NV_LANG_DATA}">
            <input type="hidden" name="{$smarty.const.NV_NAME_VARIABLE}" value="{$MODULE_NAME}">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="q" value="{$SEARCH.keyword}" class="form-control" placeholder="{$LANG.search}...">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="-1">{$LANG.all} {$LANG.order_status}</option>
                        {foreach from=$STATUS_OPTIONS item=status}
                        <option value="{$status.key}"{if $status.selected} selected{/if}>{$status.value}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="payment" class="form-select">
                        <option value="-1">{$LANG.all} {$LANG.payment_status}</option>
                        {foreach from=$PAYMENT_OPTIONS item=payment}
                        <option value="{$payment.key}"{if $payment.selected} selected{/if}>{$payment.value}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="employee" class="form-select">
                        {foreach from=$EMPLOYEE_OPTIONS item=emp}
                        <option value="{$emp.key}"{if $emp.selected} selected{/if}>{$emp.value}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa fa-search"></i> {$LANG.search}
                    </button>
                </div>
            </div>
        </form>

        <!-- Bảng danh sách -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50">STT</th>
                        <th>{$LANG.order_code}</th>
                        <th>{$LANG.customer_name}</th>
                        <th>{$LANG.customer_phone}</th>
                        <th>{$LANG.employee}</th>
                        <th>{$LANG.final_amount}</th>
                        <th>{$LANG.order_status}</th>
                        <th>{$LANG.payment_status}</th>
                        <th>{$LANG.order_date}</th>
                        <th width="100" class="text-center">{$LANG.action}</th>
                    </tr>
                </thead>
                <tbody>
                    {if $ORDERS|@count > 0}
                        {foreach from=$ORDERS item=row}
                        <tr>
                            <td>{$row.stt}</td>
                            <td><strong>{$row.order_code}</strong></td>
                            <td>{$row.customer_name}</td>
                            <td>{$row.customer_phone}</td>
                            <td>{$row.employee_name}</td>
                            <td class="text-end"><strong>{$row.final_amount_format}</strong></td>
                            <td>
                                <span class="badge bg-{$row.status_class}">{$row.order_status_text}</span>
                            </td>
                            <td>
                                <span class="badge bg-{$row.payment_class}">{$row.payment_status_text}</span>
                            </td>
                            <td>{$row.order_date_format}</td>
                            <td class="text-center">
                                <a href="{$row.edit_url}" class="btn btn-sm btn-info" title="{$LANG.edit}">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteOrder({$row.id})" title="{$LANG.delete}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        {/foreach}
                    {else}
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="fa fa-inbox fa-3x mb-3"></i>
                                <p>{$LANG.empty}</p>
                            </td>
                        </tr>
                    {/if}
                </tbody>
            </table>
        </div>

        {if $GENERATE_PAGE}
        <div class="mt-3">{$GENERATE_PAGE}</div>
        {/if}
    </div>
</div>

<script>
function deleteOrder(id) {
    if (confirm('{$LANG.delete_confirm}')) {
        $.ajax({
            type: 'POST',
            url: '{$BASE_URL}',
            data: {
                delete: 1,
                id: id
            },
            success: function(data) {
                if (data.status == 'success') {
                    location.reload();
                } else {
                    alert(data.message);
                }
            }
        });
    }
}
</script>
