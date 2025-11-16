<!-- BEGIN: main -->
<div class="row">
    <div class="col-12">
        <h3 class="mb-4">Dashboard - Quản lý Order</h3>
    </div>
</div>

<!-- Thống kê tổng quan -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body">
                <h5 class="card-title text-primary">Tổng đơn hàng</h5>
                <h2 class="mb-0">{TOTAL_ORDERS}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body">
                <h5 class="card-title text-success">Hoàn thành</h5>
                <h2 class="mb-0">{COMPLETED_ORDERS}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-warning">
            <div class="card-body">
                <h5 class="card-title text-warning">Chờ xử lý</h5>
                <h2 class="mb-0">{PENDING_ORDERS}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-info">
            <div class="card-body">
                <h5 class="card-title text-info">Doanh thu</h5>
                <h3 class="mb-0">{TOTAL_REVENUE}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Món ăn</h5>
                <p class="display-6">{TOTAL_DISHES}</p>
                <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=dishes" class="btn btn-sm btn-primary">Quản lý</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Nhân viên</h5>
                <p class="display-6">{TOTAL_STAFF}</p>
                <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=staff" class="btn btn-sm btn-primary">Quản lý</a>
            </div>
        </div>
    </div>
</div>

<!-- BEGIN: recent_orders -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Đơn hàng gần đây</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Mã ĐH</th>
                                <th>Khách hàng</th>
                                <th>Nhân viên</th>
                                <th>Thành tiền</th>
                                <th>Trạng thái</th>
                                <th>Thanh toán</th>
                                <th>Thời gian</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- BEGIN: loop -->
                            <tr>
                                <td><strong>{ORDER.order_code}</strong></td>
                                <td>{ORDER.customer_name}</td>
                                <td>{ORDER.staff_name}</td>
                                <td><strong>{ORDER.final_amount_format}</strong></td>
                                <td><span class="badge bg-secondary">{ORDER.order_status_text}</span></td>
                                <td><span class="badge bg-warning">{ORDER.payment_status_text}</span></td>
                                <td>{ORDER.order_time_format}</td>
                                <td>
                                    <a href="{ORDER.url_edit}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            <!-- END: loop -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: recent_orders -->

<!-- END: main -->
