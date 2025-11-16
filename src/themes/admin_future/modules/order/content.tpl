<!-- BEGIN: main -->
<div class="card">
    <div class="card-header text-bg-primary">
        <h5 class="mb-0"><i class="bi bi-receipt-cutoff"></i> {LANG.order_add}</h5>
    </div>
    <div class="card-body">
        <!-- BEGIN: error -->
        <div class="alert alert-danger">
            <!-- BEGIN: loop -->
            <div>{ERROR}</div>
            <!-- END: loop -->
        </div>
        <!-- END: error -->

        <form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}&amp;order_id={ORDER_ID}" method="post">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{LANG.order_code}</label>
                        <input type="text" name="order_code" value="{ORDER_CODE}" class="form-control" placeholder="{LANG.order_code}">
                        <small class="form-text text-muted">Để trống để tự động tạo mã</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{LANG.staff} <span class="text-danger">*</span></label>
                        <select name="staff_id" class="form-select" required>
                            <option value="0">{LANG.staff_select}</option>
                            <!-- BEGIN: staff -->
                            <option value="{STAFF.userid}" {STAFF.selected}>{STAFF.full_name}</option>
                            <!-- END: staff -->
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{LANG.customer_name} <span class="text-danger">*</span></label>
                        <input type="text" name="customer_name" value="{CUSTOMER_NAME}" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{LANG.customer_phone} <span class="text-danger">*</span></label>
                        <input type="text" name="customer_phone" value="{CUSTOMER_PHONE}" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">{LANG.customer_address}</label>
                <input type="text" name="customer_address" value="{CUSTOMER_ADDRESS}" class="form-control">
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{LANG.order_date} <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="order_date" value="{ORDER_DATE}" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{LANG.delivery_date}</label>
                        <input type="datetime-local" name="delivery_date" value="{DELIVERY_DATE}" class="form-control">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">{LANG.status}</label>
                        <select name="status" class="form-select">
                            <!-- BEGIN: status -->
                            <option value="{STATUS.key}" {STATUS.selected}>{STATUS.value}</option>
                            <!-- END: status -->
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">{LANG.payment_status}</label>
                        <select name="payment_status" class="form-select">
                            <!-- BEGIN: payment_status -->
                            <option value="{PAYMENT_STATUS_ITEM.key}" {PAYMENT_STATUS_ITEM.selected}>{PAYMENT_STATUS_ITEM.value}</option>
                            <!-- END: payment_status -->
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">{LANG.payment_method}</label>
                        <select name="payment_method" class="form-select">
                            <!-- BEGIN: payment_method -->
                            <option value="{PAYMENT_METHOD_ITEM.key}" {PAYMENT_METHOD_ITEM.selected}>{PAYMENT_METHOD_ITEM.value}</option>
                            <!-- END: payment_method -->
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">{LANG.note}</label>
                <textarea name="note" rows="3" class="form-control">{NOTE}</textarea>
            </div>

            <!-- Chi tiết đơn hàng -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">{LANG.order_items}</h6>
                </div>
                <div class="card-body">
                    <div id="orderItems">
                        <!-- BEGIN: items -->
                        <!-- BEGIN: loop -->
                        <div class="row mb-2 order-item">
                            <div class="col-md-4">
                                <select name="items_menu_id[]" class="form-select menu-select" required>
                                    <option value="">{LANG.menu_item}</option>
                                    {MENU_OPTIONS}
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items_quantity[]" value="{ITEM.quantity}" class="form-control item-quantity" placeholder="{LANG.quantity}" min="1" required>
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="items_price[]" value="{ITEM.price}" class="form-control item-price" placeholder="{LANG.price}" required>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="items_note[]" value="{ITEM.note}" class="form-control" placeholder="{LANG.note}">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger btn-remove-item"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                        <!-- END: loop -->
                        <!-- END: items -->
                    </div>

                    <button type="button" class="btn btn-success btn-sm" id="btnAddItem">
                        <i class="bi bi-plus-circle"></i> {LANG.add_item}
                    </button>
                </div>
            </div>

            <div class="text-end">
                <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}=main" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> {LANG.back}
                </a>
                <button type="submit" name="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {LANG.save}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
var menuOptions = '{MENU_OPTIONS}';

$(document).ready(function() {
    // Thêm món mới
    $('#btnAddItem').click(function() {
        var html = '<div class="row mb-2 order-item">' +
            '<div class="col-md-4">' +
            '<select name="items_menu_id[]" class="form-select menu-select" required>' +
            '<option value="">{LANG.menu_item}</option>' +
            menuOptions +
            '</select>' +
            '</div>' +
            '<div class="col-md-2">' +
            '<input type="number" name="items_quantity[]" value="1" class="form-control item-quantity" placeholder="{LANG.quantity}" min="1" required>' +
            '</div>' +
            '<div class="col-md-2">' +
            '<input type="text" name="items_price[]" class="form-control item-price" placeholder="{LANG.price}" required>' +
            '</div>' +
            '<div class="col-md-3">' +
            '<input type="text" name="items_note[]" class="form-control" placeholder="{LANG.note}">' +
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
<!-- END: main -->
