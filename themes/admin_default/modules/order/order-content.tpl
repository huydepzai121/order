<!-- BEGIN: main -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{DATA.id > 0 ? '{LANG.order_edit}' : '{LANG.order_add}'}</h5>
    </div>
    <div class="card-body">
        <!-- BEGIN: error -->
        <div class="alert alert-danger">{ERROR}</div>
        <!-- END: error -->

        <form method="post" action="">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">{LANG.order_code} <span class="text-danger">*</span></label>
                    <input type="text" name="order_code" value="{DATA.order_code}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{LANG.staff_serve}</label>
                    <select name="staff_id" class="form-select">
                        <option value="0">{LANG.select_staff}</option>
                        <!-- BEGIN: staff_loop -->
                        <option value="{STAFF_ID}"{STAFF_SELECTED}>{STAFF_NAME}</option>
                        <!-- END: staff_loop -->
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{LANG.customer_name} <span class="text-danger">*</span></label>
                    <input type="text" name="customer_name" value="{DATA.customer_name}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{LANG.customer_phone}</label>
                    <input type="text" name="customer_phone" value="{DATA.customer_phone}" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">{LANG.customer_address}</label>
                    <textarea name="customer_address" class="form-control" rows="2">{DATA.customer_address}</textarea>
                </div>
            </div>

            <hr class="my-4">

            <h6>Chi tiết đơn hàng</h6>
            <div id="order-details">
                <!-- BEGIN: order_detail -->
                <div class="row g-2 mb-2 order-item">
                    <div class="col-md-6">
                        <select name="dish_id[]" class="form-select dish-select" required>
                            <option value="0">{LANG.select_dish}</option>
                            <!-- BEGIN: dish_option -->
                            <option value="{DISH_ID}"{DISH_SELECTED}>{DISH_NAME}</option>
                            <!-- END: dish_option -->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="quantity[]" value="{DETAIL.quantity}" class="form-control" placeholder="{LANG.quantity}" min="1" required>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-danger w-100 remove-item">{LANG.remove_item}</button>
                    </div>
                </div>
                <!-- END: order_detail -->

                <!-- BEGIN: empty_detail -->
                <div class="row g-2 mb-2 order-item">
                    <div class="col-md-6">
                        <select name="dish_id[]" class="form-select dish-select" required>
                            <option value="0">{LANG.select_dish}</option>
                            <!-- BEGIN: dish_option -->
                            <option value="{DISH_ID}">{DISH_NAME}</option>
                            <!-- END: dish_option -->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="quantity[]" value="1" class="form-control" placeholder="{LANG.quantity}" min="1" required>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-danger w-100 remove-item">{LANG.remove_item}</button>
                    </div>
                </div>
                <!-- END: empty_detail -->
            </div>

            <button type="button" class="btn btn-success mb-3" id="add-item-btn">
                <i class="fa fa-plus"></i> {LANG.add_item}
            </button>

            <hr class="my-4">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">{LANG.discount_amount}</label>
                    <input type="number" name="discount_amount" value="{DATA.discount_amount}" class="form-control" step="0.01" min="0">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{LANG.payment_method}</label>
                    <select name="payment_method" class="form-select">
                        <!-- BEGIN: payment_method_loop -->
                        <option value="{METHOD_ID}"{METHOD_SELECTED}>{METHOD_NAME}</option>
                        <!-- END: payment_method_loop -->
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{LANG.order_status}</label>
                    <select name="order_status" class="form-select">
                        <!-- BEGIN: order_status_loop -->
                        <option value="{STATUS_ID}"{STATUS_SELECTED}>{STATUS_NAME}</option>
                        <!-- END: order_status_loop -->
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{LANG.payment_status}</label>
                    <select name="payment_status" class="form-select">
                        <!-- BEGIN: payment_status_loop -->
                        <option value="{STATUS_ID}"{STATUS_SELECTED}>{STATUS_NAME}</option>
                        <!-- END: payment_status_loop -->
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">{LANG.order_notes}</label>
                    <textarea name="notes" class="form-control" rows="3">{DATA.notes}</textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" name="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> {LANG.save}
                </button>
                <a href="{URL_BACK}" class="btn btn-secondary">
                    <i class="fa fa-times"></i> {LANG.cancel}
                </a>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // Template cho item mới
    var dishOptions = '';
    <!-- BEGIN: dish_option -->
    dishOptions += '<option value="{DISH_ID}">{DISH_NAME}</option>';
    <!-- END: dish_option -->

    // Thêm item
    $('#add-item-btn').click(function() {
        var html = '<div class="row g-2 mb-2 order-item">' +
            '<div class="col-md-6">' +
            '<select name="dish_id[]" class="form-select dish-select" required>' +
            '<option value="0">{LANG.select_dish}</option>' +
            dishOptions +
            '</select>' +
            '</div>' +
            '<div class="col-md-3">' +
            '<input type="number" name="quantity[]" value="1" class="form-control" placeholder="{LANG.quantity}" min="1" required>' +
            '</div>' +
            '<div class="col-md-3">' +
            '<button type="button" class="btn btn-danger w-100 remove-item">{LANG.remove_item}</button>' +
            '</div>' +
            '</div>';
        $('#order-details').append(html);
    });

    // Xóa item
    $(document).on('click', '.remove-item', function() {
        if ($('.order-item').length > 1) {
            $(this).closest('.order-item').remove();
        } else {
            alert('Phải có ít nhất 1 món ăn');
        }
    });
});
</script>

<!-- END: main -->
