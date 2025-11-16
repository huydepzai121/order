<!-- BEGIN: main -->
<div class="card">
    <div class="card-header text-bg-primary">
        <h5 class="mb-0"><i class="bi bi-calendar-check"></i> {LANG.staff_work_manage}</h5>
    </div>
    <div class="card-body">
        <!-- Bộ lọc -->
        <form action="{NV_BASE_ADMINURL}index.php" method="get" class="mb-4">
            <input type="hidden" name="{NV_LANG_VARIABLE}" value="{NV_LANG_DATA}">
            <input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}">
            <input type="hidden" name="{NV_OP_VARIABLE}" value="{OP}">

            <div class="row g-3">
                <div class="col-md-3">
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

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> {LANG.filter}</button>
                </div>
            </div>
        </form>

        <!-- Nút thêm mới -->
        <div class="mb-3">
            <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}=staff-work-content" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Thêm công nhân viên
            </a>
        </div>

        <!-- BEGIN: work -->
        <!-- BEGIN: summary -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-bg-info">
                    <div class="card-body">
                        <h6 class="card-title">Tổng giờ làm</h6>
                        <h3 class="mb-0">{TOTAL_HOURS}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-success">
                    <div class="card-body">
                        <h6 class="card-title">Tổng đơn hàng</h6>
                        <h3 class="mb-0">{TOTAL_ORDERS}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-warning">
                    <div class="card-body">
                        <h6 class="card-title">Tổng doanh thu</h6>
                        <h3 class="mb-0">{TOTAL_REVENUE}</h3>
                    </div>
                </div>
            </div>
        </div>
        <!-- END: summary -->

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>{LANG.staff_name}</th>
                        <th>{LANG.work_date}</th>
                        <th>{LANG.shift}</th>
                        <th>{LANG.start_time}</th>
                        <th>{LANG.end_time}</th>
                        <th>{LANG.work_hours}</th>
                        <th>{LANG.order_count}</th>
                        <th>{LANG.total_revenue}</th>
                        <th class="text-center">{LANG.action}</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- BEGIN: loop -->
                    <tr>
                        <td><strong>{WORK.staff_name}</strong></td>
                        <td>{WORK.work_date}</td>
                        <td><span class="badge bg-info">{WORK.shift}</span></td>
                        <td>{WORK.start_time}</td>
                        <td>{WORK.end_time}</td>
                        <td><strong>{WORK.work_hours}</strong></td>
                        <td>{WORK.order_count}</td>
                        <td><strong>{WORK.total_revenue}</strong></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{WORK.edit_url}" class="btn btn-primary" title="{LANG.edit}">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-danger" onclick="confirmDelete({WORK.work_id});" title="{LANG.delete}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <!-- END: loop -->
                </tbody>
            </table>
        </div>
        <!-- END: work -->

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
function confirmDelete(workId) {
    if (confirm('{LANG.confirm_delete}?')) {
        $.ajax({
            url: '{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=staff-work-del',
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
<!-- END: main -->
