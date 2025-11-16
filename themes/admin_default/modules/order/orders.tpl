<!-- BEGIN: main -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{LANG.order_list}</h5>
        <a href="{URL_ADD}" class="btn btn-primary">
            <i class="fa fa-plus"></i> {LANG.order_add}
        </a>
    </div>
    <div class="card-body">
        <!-- Filter form -->
        <form method="get" action="" class="row g-3 mb-3">
            <input type="hidden" name="{NV_LANG_VARIABLE}" value="{NV_LANG_DATA}">
            <input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}">
            <input type="hidden" name="{NV_OP_VARIABLE}" value="orders">

            <div class="col-md-3">
                <input type="text" name="search" value="{SEARCH}" class="form-control" placeholder="{LANG.search}...">
            </div>
            <div class="col-md-2">
                <select name="order_status" class="form-select">
                    <!-- BEGIN: order_status_all -->
                    <option value="-1"{ORDER_STATUS_SELECTED}>{LANG.all}</option>
                    <!-- END: order_status_all -->
                    <!-- BEGIN: order_status_loop -->
                    <option value="{STATUS_KEY}"{STATUS_SELECTED}>{STATUS_VALUE}</option>
                    <!-- END: order_status_loop -->
                </select>
            </div>
            <div class="col-md-2">
                <select name="payment_status" class="form-select">
                    <!-- BEGIN: payment_status_all -->
                    <option value="-1"{PAYMENT_STATUS_SELECTED}>{LANG.all}</option>
                    <!-- END: payment_status_all -->
                    <!-- BEGIN: payment_status_loop -->
                    <option value="{STATUS_KEY}"{STATUS_SELECTED}>{STATUS_VALUE}</option>
                    <!-- END: payment_status_loop -->
                </select>
            </div>
            <div class="col-md-3">
                <select name="staff_id" class="form-select">
                    <!-- BEGIN: staff_all -->
                    <option value="0"{STAFF_SELECTED}>{LANG.all}</option>
                    <!-- END: staff_all -->
                    <!-- BEGIN: staff_loop -->
                    <option value="{STAFF_KEY}"{STAFF_SELECTED}>{STAFF_VALUE}</option>
                    <!-- END: staff_loop -->
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">{LANG.search}</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>{LANG.order_code}</th>
                        <th>{LANG.customer_name}</th>
                        <th>{LANG.customer_phone}</th>
                        <th>{LANG.staff_serve}</th>
                        <th>{LANG.final_amount}</th>
                        <th>{LANG.order_status}</th>
                        <th>{LANG.payment_status}</th>
                        <th>{LANG.order_time}</th>
                        <th>{LANG.actions}</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- BEGIN: loop -->
                    <tr>
                        <td><strong>{ORDER.order_code}</strong></td>
                        <td>{ORDER.customer_name}</td>
                        <td>{ORDER.customer_phone}</td>
                        <td>{ORDER.staff_name}</td>
                        <td><strong>{ORDER.final_amount_format}</strong></td>
                        <td><span class="badge bg-{ORDER.status_class}">{ORDER.order_status_text}</span></td>
                        <td><span class="badge bg-{ORDER.payment_class}">{ORDER.payment_status_text}</span></td>
                        <td>{ORDER.order_time_format}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{ORDER.url_edit}" class="btn btn-primary" title="{LANG.edit}">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a href="javascript:void(0);" onclick="nv_del_order({ORDER.id});" class="btn btn-danger" title="{LANG.delete}">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <!-- END: loop -->
                    <!-- BEGIN: empty -->
                    <tr>
                        <td colspan="9" class="text-center">{LANG.search} không có kết quả</td>
                    </tr>
                    <!-- END: empty -->
                </tbody>
            </table>
        </div>

        <!-- BEGIN: generate_page -->
        <div class="mt-3">
            {GENERATE_PAGE}
        </div>
        <!-- END: generate_page -->
    </div>
</div>

<script>
function nv_del_order(id) {
    if (confirm('{LANG.confirm_delete}')) {
        $.ajax({
            url: script_name + '?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '={MODULE_NAME}&' + nv_fc_variable + '=order-del&id=' + id,
            type: 'POST',
            dataType: 'json',
            data: 'confirm=1&checkss=' + md5(id + '{NV_CHECK_SESSION}'),
            success: function(res) {
                if (res.status == 'OK') {
                    window.location.href = res.redirect;
                } else {
                    alert(res.message);
                }
            }
        });
    }
}
</script>

<!-- END: main -->
