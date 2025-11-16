{* BEGIN: main *}
<div class="card">
    <div class="card-header text-bg-primary">
        <h5 class="mb-0"><i class="bi bi-receipt-cutoff"></i> {$LANG->getModule('order_add')}</h5>
    </div>
    <div class="card-body">
        {if not empty($ERROR)}
        <div class="alert alert-danger">
            {$ERROR|@join:"<br />"}
        </div>
        {/if}

        <form action="{$smarty.const.NV_BASE_ADMINURL}index.php?{$smarty.const.NV_LANG_VARIABLE}={$smarty.const.NV_LANG_DATA}&amp;{$smarty.const.NV_NAME_VARIABLE}={$MODULE_NAME}&amp;{$smarty.const.NV_OP_VARIABLE}={$OP}&amp;order_id={$ORDER_ID}" method="post">
            <input type="hidden" name="checkss" value="{$NV_CHECK}" />

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('order_code')}</label>
                        <input type="text" name="order_code" value="{$ORDER_CODE}" class="form-control" placeholder="{$LANG->getModule('order_code')}">
                        <small class="form-text text-muted">Để trống để tự động tạo mã</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('staff')} <span class="text-danger">*</span></label>
                        <select name="staff_id" class="form-select" required>
                            <option value="0">{$LANG->getModule('staff_select')}</option>
                            {foreach from=$STAFF_LIST item=staff}
                            <option value="{$staff.userid}" {if $staff.selected}selected{/if}>{$staff.full_name}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('customer_name')} <span class="text-danger">*</span></label>
                        <input type="text" name="customer_name" value="{$CUSTOMER_NAME}" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('customer_phone')} <span class="text-danger">*</span></label>
                        <input type="text" name="customer_phone" value="{$CUSTOMER_PHONE}" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">{$LANG->getModule('customer_address')}</label>
                <input type="text" name="customer_address" value="{$CUSTOMER_ADDRESS}" class="form-control">
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('order_date')} <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="order_date" value="{$ORDER_DATE}" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('delivery_date')}</label>
                        <input type="datetime-local" name="delivery_date" value="{$DELIVERY_DATE}" class="form-control">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('status')}</label>
                        <select name="status" class="form-select">
                            {foreach from=$STATUS_LIST item=status_item}
                            <option value="{$status_item.key}" {if $status_item.selected}selected{/if}>{$status_item.value}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('payment_status')}</label>
                        <select name="payment_status" class="form-select">
                            {foreach from=$PAYMENT_STATUS_LIST item=payment_status_item}
                            <option value="{$payment_status_item.key}" {if $payment_status_item.selected}selected{/if}>{$payment_status_item.value}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('payment_method')}</label>
                        <select name="payment_method" class="form-select">
                            {foreach from=$PAYMENT_METHOD_LIST item=payment_method_item}
                            <option value="{$payment_method_item.key}" {if $payment_method_item.selected}selected{/if}>{$payment_method_item.value}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">{$LANG->getModule('note')}</label>
                <textarea name="note" rows="3" class="form-control">{$NOTE}</textarea>
            </div>

            {* Chi tiết đơn hàng *}
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">{$LANG->getModule('order_items')}</h6>
                </div>
                <div class="card-body">
                    <div id="orderItems">
                        {if not empty($ORDER_ITEMS)}
                        {foreach from=$ORDER_ITEMS item=item}
                        <div class="row mb-2 order-item">
                            <div class="col-md-4">
                                <select name="items_menu_id[]" class="form-select menu-select" required>
                                    <option value="">{$LANG->getModule('menu_item')}</option>
                                    {$MENU_OPTIONS nofilter}
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items_quantity[]" value="{$item.quantity}" class="form-control item-quantity" placeholder="{$LANG->getModule('quantity')}" min="1" required>
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="items_price[]" value="{$item.price}" class="form-control item-price" placeholder="{$LANG->getModule('price')}" required>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="items_note[]" value="{$item.note}" class="form-control" placeholder="{$LANG->getModule('note')}">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger btn-remove-item"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                        {/foreach}
                        {/if}
                    </div>

                    <button type="button" class="btn btn-success btn-sm" id="btnAddItem">
                        <i class="bi bi-plus-circle"></i> {$LANG->getModule('add_item')}
                    </button>
                </div>
            </div>

            <div class="text-end">
                <a href="{$smarty.const.NV_BASE_ADMINURL}index.php?{$smarty.const.NV_LANG_VARIABLE}={$smarty.const.NV_LANG_DATA}&amp;{$smarty.const.NV_NAME_VARIABLE}={$MODULE_NAME}&amp;{$smarty.const.NV_OP_VARIABLE}=main" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> {$LANG->getModule('back')}
                </a>
                <button type="submit" name="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {$LANG->getModule('save')}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
var menuOptions = '{$MENU_OPTIONS|escape:"javascript"}';

$(document).ready(function() {
    // Thêm món mới
    $('#btnAddItem').click(function() {
        var html = '<div class="row mb-2 order-item">' +
            '<div class="col-md-4">' +
            '<select name="items_menu_id[]" class="form-select menu-select" required>' +
            '<option value="">{$LANG->getModule("menu_item")|escape:"javascript"}</option>' +
            menuOptions +
            '</select>' +
            '</div>' +
            '<div class="col-md-2">' +
            '<input type="number" name="items_quantity[]" value="1" class="form-control item-quantity" placeholder="{$LANG->getModule("quantity")|escape:"javascript"}" min="1" required>' +
            '</div>' +
            '<div class="col-md-2">' +
            '<input type="text" name="items_price[]" class="form-control item-price" placeholder="{$LANG->getModule("price")|escape:"javascript"}" required>' +
            '</div>' +
            '<div class="col-md-3">' +
            '<input type="text" name="items_note[]" class="form-control" placeholder="{$LANG->getModule("note")|escape:"javascript"}">' +
            '</div>' +
            '<div class="col-md-1">' +
            '<button type="button" class="btn btn-danger btn-remove-item"><i class="bi bi-trash"></i></button>' +
            '</div>' +
            '</div>';
        $('#orderItems').append(html);
    });

    // Xóa món
    $(document).on('click', '.btn-remove-item', function() {
        $(this).closest('.order-item').remove();
    });

    // Tự động điền giá khi chọn món
    $(document).on('change', '.menu-select', function() {
        var price = $(this).find('option:selected').data('price');
        if (price) {
            $(this).closest('.order-item').find('.item-price').val(price);
        }
    });
});
</script>
{* END: main *}
