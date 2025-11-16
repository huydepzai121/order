<!-- BEGIN: main -->
<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0">{LANG.reports}</h5>
    </div>
    <div class="card-body">
        <form method="get" action="" class="row g-3">
            <input type="hidden" name="{NV_LANG_VARIABLE}" value="{NV_LANG_DATA}">
            <input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}">
            <input type="hidden" name="{NV_OP_VARIABLE}" value="reports">

            <div class="col-md-4">
                <label class="form-label">{LANG.report_date_from}</label>
                <input type="date" name="date_from" value="{DATE_FROM}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">{LANG.report_date_to}</label>
                <input type="date" name="date_to" value="{DATE_TO}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fa fa-search"></i> {LANG.report_view}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Thống kê doanh thu -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body text-center">
                <h6 class="text-primary">{LANG.report_total_orders}</h6>
                <h2 class="mb-0">{TOTAL_ORDERS}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body text-center">
                <h6 class="text-success">Đơn hoàn thành</h6>
                <h2 class="mb-0">{COMPLETED_ORDERS}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-danger">
            <div class="card-body text-center">
                <h6 class="text-danger">Đơn đã hủy</h6>
                <h2 class="mb-0">{CANCELLED_ORDERS}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-info">
            <div class="card-body text-center">
                <h6 class="text-info">{LANG.report_total_revenue}</h6>
                <h3 class="mb-0">{TOTAL_REVENUE}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h6>Đã thanh toán</h6>
                <h4>{PAID_REVENUE}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h6>Chưa thanh toán</h6>
                <h4>{UNPAID_REVENUE}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h6>{LANG.report_avg_order}</h6>
                <h4>{AVG_ORDER_VALUE}</h4>
            </div>
        </div>
    </div>
</div>

<!-- Top món ăn bán chạy -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Top 10 món ăn bán chạy</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th width="50">STT</th>
                        <th>{LANG.dish_name}</th>
                        <th>Số lượng</th>
                        <th>Doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- BEGIN: top_dish -->
                    <tr>
                        <td>{DISH.stt}</td>
                        <td><strong>{DISH.dish_name}</strong></td>
                        <td>{DISH.total_qty}</td>
                        <td><strong>{DISH.total_amount_format}</strong></td>
                    </tr>
                    <!-- END: top_dish -->
                    <!-- BEGIN: no_dishes -->
                    <tr>
                        <td colspan="4" class="text-center">Không có dữ liệu</td>
                    </tr>
                    <!-- END: no_dishes -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Báo cáo nhân viên -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">{LANG.report_staff}</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>{LANG.staff_user}</th>
                        <th>Tổng đơn</th>
                        <th>Tổng doanh thu</th>
                        <th>Đã thu</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- BEGIN: staff_report -->
                    <tr>
                        <td><strong>{STAFF.full_name}</strong></td>
                        <td>{STAFF.total_orders}</td>
                        <td><strong>{STAFF.total_amount_format}</strong></td>
                        <td>{STAFF.paid_amount_format}</td>
                    </tr>
                    <!-- END: staff_report -->
                    <!-- BEGIN: no_staff -->
                    <tr>
                        <td colspan="4" class="text-center">Không có dữ liệu</td>
                    </tr>
                    <!-- END: no_staff -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Biểu đồ doanh thu theo ngày -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Biểu đồ doanh thu theo ngày</h5>
    </div>
    <div class="card-body">
        <canvas id="revenueChart" height="80"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
$(document).ready(function() {
    var ctx = document.getElementById('revenueChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {CHART_LABELS},
            datasets: [{
                label: 'Doanh thu',
                data: {CHART_DATA},
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>

<!-- END: main -->
