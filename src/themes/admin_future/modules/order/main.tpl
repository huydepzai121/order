<!-- BEGIN: main -->
<div class="card">
    <div class="card-header text-bg-primary">
        <h5 class="mb-0"><i class="bi bi-receipt"></i> {LANG.order_list}</h5>
    </div>
    <div class="card-body">
        <!-- Bộ lọc -->
        <form action="{NV_BASE_ADMINURL}index.php" method="get" class="mb-4">
            <input type="hidden" name="{NV_LANG_VARIABLE}" value="{NV_LANG_DATA}">
            <input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}">
            <input type="hidden" name="{NV_OP_VARIABLE}" value="{OP}">

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">{LANG.search}</label>
                    <input type="text" name="search" value="{SEARCH}" class="form-control" placeholder="{LANG.search}...">
                </div>

                <div class="col-md-2">
                    <label class="form-label">{LANG.status}</label>
                    <select name="status" class="form-select">
                        <option value="-1">{LANG.all}</option>
                        <!-- BEGIN: status -->
                        <option value="{STATUS.key}" {STATUS.selected}>{STATUS.value}</option>
                        <!-- END: status -->
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">{LANG.payment_status}</label>
                    <select name="payment_status" class="form-select">
                        <option value="-1">{LANG.all}</option>
                        <!-- BEGIN: payment_status -->
                        <option value="{PAYMENT_STATUS_ITEM.key}" {PAYMENT_STATUS_ITEM.selected}>{PAYMENT_STATUS_ITEM.value}</option>
                        <!-- END: payment_status -->
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">{LANG.staff}</label>
                    <select name="staff_id" class="form-select">
                        <option value="0">{LANG.all}</option>
                        <!-- BEGIN: staff -->
                        <option value="{STAFF.userid}" {STAFF.selected}>{STAFF.full_name}</option>
                        <!-- END: staff -->
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">{LANG.from_date}</label>
                    <input type="date" name="from_date" value="{FROM_DATE}" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label">{LANG.to_date}</label>
                    <input type="date" name="to_date" value="{TO_DATE}" class="form-control">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> {LANG.filter}</button>
                </div>
            </div>
        </form>

        <!-- Nút thêm mới -->
        <div class="mb-3">
            <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}=content" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> {LANG.order_add}
            </a>
        </div>

        <!-- BEGIN: orders -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>{LANG.order_code}</th>
                        <th>{LANG.customer_name}</th>
                        <th>{LANG.customer_phone}</th>
                        <th>{LANG.order_date}</th>
                        <th>{LANG.total_amount}</th>
                        <th>{LANG.status}</th>
                        <th>{LANG.payment_status}</th>
                        <th>{LANG.staff}</th>
                        <th class="text-center">{LANG.action}</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- BEGIN: loop -->
                    <tr>
                        <td><strong>{ORDER.order_code}</strong></td>
                        <td>{ORDER.customer_name}</td>
                        <td>{ORDER.customer_phone}</td>
                        <td>{ORDER.order_date}</td>
                        <td><strong>{ORDER.total_amount}</strong></td>
                        <td><span class="badge bg-{ORDER.status_class}">{ORDER.status}</span></td>
                        <td><span class="badge bg-{ORDER.payment_status_class}">{ORDER.payment_status}</span></td>
                        <td>{ORDER.staff_name}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{ORDER.edit_url}" class="btn btn-primary" title="{LANG.edit}">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-danger" onclick="confirmDelete({ORDER.order_id}, '{ORDER.order_code}');" title="{LANG.delete}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <!-- END: loop -->
                </tbody>
            </table>
        </div>
        <!-- END: orders -->

        <!-- BEGIN: no_data -->
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> {LANG.no_data}
        </div>
        <!-- END: no_data -->

        <!-- BEGIN: generate_page -->
        <div class="mt-3">
            {GENERATE_PAGE}
        </div>
        <!-- END: generate_page -->
    </div>
</div>

<script>
function confirmDelete(orderId, orderCode) {
    if (confirm('{LANG.confirm_delete} ' + orderCode + '?')) {
        $.ajax({
            url: '{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=del',
            type: 'POST',
            data: { order_id: orderId },
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
<!-- END: main -->
