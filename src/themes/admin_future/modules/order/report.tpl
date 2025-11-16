<!-- BEGIN: main -->
<div class="card">
    <div class="card-header text-bg-primary">
        <h5 class="mb-0"><i class="bi bi-graph-up"></i> {LANG.report}</h5>
    </div>
    <div class="card-body">
        <!-- Bộ lọc -->
        <form action="{NV_BASE_ADMINURL}index.php" method="get" class="mb-4">
            <input type="hidden" name="{NV_LANG_VARIABLE}" value="{NV_LANG_DATA}">
            <input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}">
            <input type="hidden" name="{NV_OP_VARIABLE}" value="{OP}">

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Loại báo cáo</label>
                    <select name="report_type" class="form-select">
                        <!-- BEGIN: report_type -->
                        <option value="{REPORT_TYPE.key}" {REPORT_TYPE.selected}>{REPORT_TYPE.value}</option>
                        <!-- END: report_type -->
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
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> {LANG.view_report}</button>
                </div>
            </div>
        </form>

        <!-- Báo cáo doanh thu -->
        <!-- BEGIN: revenue_report -->
        <!-- BEGIN: summary -->
        <h5 class="mb-3">Tổng quan doanh thu</h5>
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-bg-info">
                    <div class="card-body">
                        <h6 class="card-title">Tổng đơn hàng</h6>
                        <h3 class="mb-0">{REVENUE.total_orders}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-success">
                    <div class="card-body">
                        <h6 class="card-title">Đơn hoàn thành</h6>
                        <h3 class="mb-0">{REVENUE.completed_orders}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-danger">
                    <div class="card-body">
                        <h6 class="card-title">Đơn hủy</h6>
                        <h3 class="mb-0">{REVENUE.cancelled_orders}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-bg-primary">
                    <div class="card-body">
                        <h6 class="card-title">Tổng doanh thu</h6>
                        <h3 class="mb-0">{REVENUE.total_revenue}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-success">
                    <div class="card-body">
                        <h6 class="card-title">Đã thanh toán</h6>
                        <h3 class="mb-0">{REVENUE.paid_revenue}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-warning">
                    <div class="card-body">
                        <h6 class="card-title">Chưa thanh toán</h6>
                        <h3 class="mb-0">{REVENUE.unpaid_revenue}</h3>
                    </div>
                </div>
            </div>
        </div>
        <!-- END: summary -->

        <!-- BEGIN: by_date -->
        <h5 class="mb-3">Doanh thu theo ngày</h5>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Ngày</th>
                        <th>Số đơn</th>
                        <th>Doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- BEGIN: loop -->
                    <tr>
                        <td>{REVENUE_DATE.date}</td>
                        <td>{REVENUE_DATE.order_count}</td>
                        <td><strong>{REVENUE_DATE.revenue}</strong></td>
                    </tr>
                    <!-- END: loop -->
                </tbody>
            </table>
        </div>
        <!-- END: by_date -->
        <!-- END: revenue_report -->

        <!-- Báo cáo thực đơn -->
        <!-- BEGIN: menu_report -->
        <h5 class="mb-3">Báo cáo thực đơn</h5>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{LANG.menu_name}</th>
                        <th>{LANG.category}</th>
                        <th>{LANG.price}</th>
                        <th>Số lần gọi</th>
                        <th>{LANG.quantity}</th>
                        <th>{LANG.total_revenue}</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- BEGIN: loop -->
                    <tr>
                        <td><strong>{MENU.menu_name}</strong></td>
                        <td><span class="badge bg-info">{MENU.category}</span></td>
                        <td>{MENU.price}</td>
                        <td>{MENU.order_count}</td>
                        <td>{MENU.total_quantity}</td>
                        <td><strong>{MENU.total_revenue}</strong></td>
                    </tr>
                    <!-- END: loop -->
                </tbody>
                <tfoot>
                    <tr class="table-info">
                        <td colspan="4"><strong>Tổng cộng</strong></td>
                        <td><strong>{MENU_TOTAL_QUANTITY}</strong></td>
                        <td><strong>{MENU_TOTAL_REVENUE}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- END: menu_report -->

        <!-- Báo cáo nhân viên -->
        <!-- BEGIN: staff_report -->
        <h5 class="mb-3">Báo cáo nhân viên</h5>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{LANG.staff_name}</th>
                        <th>{LANG.order_count}</th>
                        <th>{LANG.work_hours}</th>
                        <th>{LANG.total_revenue}</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- BEGIN: loop -->
                    <tr>
                        <td><strong>{STAFF_ITEM.staff_name}</strong></td>
                        <td>{STAFF_ITEM.order_count}</td>
                        <td>{STAFF_ITEM.total_work_hours}</td>
                        <td><strong>{STAFF_ITEM.total_revenue}</strong></td>
                    </tr>
                    <!-- END: loop -->
                </tbody>
                <tfoot>
                    <tr class="table-info">
                        <td><strong>Tổng cộng</strong></td>
                        <td><strong>{STAFF_TOTAL_ORDERS}</strong></td>
                        <td><strong>{STAFF_TOTAL_HOURS}</strong></td>
                        <td><strong>{STAFF_TOTAL_REVENUE}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- END: staff_report -->
    </div>
</div>
<!-- END: main -->
