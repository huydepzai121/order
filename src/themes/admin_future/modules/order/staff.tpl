<!-- BEGIN: main -->
<div class="card">
    <div class="card-header text-bg-primary">
        <h5 class="mb-0"><i class="bi bi-people"></i> {LANG.staff_list}</h5>
    </div>
    <div class="card-body">
        <!-- Bộ lọc -->
        <form action="{NV_BASE_ADMINURL}index.php" method="get" class="mb-4">
            <input type="hidden" name="{NV_LANG_VARIABLE}" value="{NV_LANG_DATA}">
            <input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}">
            <input type="hidden" name="{NV_OP_VARIABLE}" value="{OP}">

            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" value="{SEARCH}" class="form-control" placeholder="{LANG.search}...">
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> {LANG.search}</button>
                </div>
            </div>
        </form>

        <!-- BEGIN: staff -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>{LANG.staff_name}</th>
                        <th>{LANG.staff_email}</th>
                        <th>Ngày đăng ký</th>
                        <th>{LANG.order_count}</th>
                        <th>{LANG.total_revenue}</th>
                        <th class="text-center">{LANG.action}</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- BEGIN: loop -->
                    <tr>
                        <td>{STAFF.userid}</td>
                        <td><strong>{STAFF.full_name}</strong><br><small class="text-muted">@{STAFF.username}</small></td>
                        <td>{STAFF.email}</td>
                        <td>{STAFF.regdate}</td>
                        <td><span class="badge bg-primary">{STAFF.order_count}</span></td>
                        <td><strong>{STAFF.total_revenue}</strong></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{STAFF.view_orders_url}" class="btn btn-info" title="Xem đơn hàng">
                                    <i class="bi bi-receipt"></i>
                                </a>
                                <a href="{STAFF.view_work_url}" class="btn btn-success" title="Xem công việc">
                                    <i class="bi bi-calendar-check"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <!-- END: loop -->
                </tbody>
            </table>
        </div>
        <!-- END: staff -->

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
<!-- END: main -->
