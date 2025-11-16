{* BEGIN: main *}
<div class="card">
    <div class="card-header text-bg-primary">
        <h5 class="mb-0"><i class="bi bi-graph-up"></i> {$LANG->getModule('report')}</h5>
    </div>
    <div class="card-body">
        {* Bộ lọc *}
        <form action="{$smarty.const.NV_BASE_ADMINURL}index.php" method="get" class="mb-4">
            <input type="hidden" name="{$smarty.const.NV_LANG_VARIABLE}" value="{$smarty.const.NV_LANG_DATA}">
            <input type="hidden" name="{$smarty.const.NV_NAME_VARIABLE}" value="{$MODULE_NAME}">
            <input type="hidden" name="{$smarty.const.NV_OP_VARIABLE}" value="{$OP}">

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Loại báo cáo</label>
                    <select name="report_type" class="form-select">
                        {foreach from=$REPORT_TYPES item=type}
                        <option value="{$type.key}" {if $type.selected}selected{/if}>{$type.value}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">{$LANG->getModule('from_date')}</label>
                    <input type="date" name="from_date" value="{$FROM_DATE}" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label">{$LANG->getModule('to_date')}</label>
                    <input type="date" name="to_date" value="{$TO_DATE}" class="form-control">
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> {$LANG->getModule('view_report')}</button>
                </div>
            </div>
        </form>

        {* Báo cáo doanh thu *}
        {if $REPORT_TYPE eq 'revenue' and $REVENUE_SUMMARY}
        <h5 class="mb-3">Tổng quan doanh thu</h5>
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-bg-info">
                    <div class="card-body">
                        <h6 class="card-title">Tổng đơn hàng</h6>
                        <h3 class="mb-0">{$REVENUE_SUMMARY.total_orders}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-success">
                    <div class="card-body">
                        <h6 class="card-title">Đơn hoàn thành</h6>
                        <h3 class="mb-0">{$REVENUE_SUMMARY.completed_orders}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-danger">
                    <div class="card-body">
                        <h6 class="card-title">Đơn hủy</h6>
                        <h3 class="mb-0">{$REVENUE_SUMMARY.cancelled_orders}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-bg-primary">
                    <div class="card-body">
                        <h6 class="card-title">Tổng doanh thu</h6>
                        <h3 class="mb-0">{$REVENUE_SUMMARY.total_revenue}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-success">
                    <div class="card-body">
                        <h6 class="card-title">Đã thanh toán</h6>
                        <h3 class="mb-0">{$REVENUE_SUMMARY.paid_revenue}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-warning">
                    <div class="card-body">
                        <h6 class="card-title">Chưa thanh toán</h6>
                        <h3 class="mb-0">{$REVENUE_SUMMARY.unpaid_revenue}</h3>
                    </div>
                </div>
            </div>
        </div>

        {if not empty($REVENUE_BY_DATE)}
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
                    {foreach from=$REVENUE_BY_DATE item=item}
                    <tr>
                        <td>{$item.date}</td>
                        <td>{$item.order_count}</td>
                        <td><strong>{$item.revenue}</strong></td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
        {/if}
        {/if}

        {* Báo cáo thực đơn *}
        {if $REPORT_TYPE eq 'menu' and not empty($MENU_DATA)}
        <h5 class="mb-3">Báo cáo thực đơn</h5>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{$LANG->getModule('menu_name')}</th>
                        <th>{$LANG->getModule('category')}</th>
                        <th>{$LANG->getModule('price')}</th>
                        <th>Số lần gọi</th>
                        <th>{$LANG->getModule('quantity')}</th>
                        <th>{$LANG->getModule('total_revenue')}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$MENU_DATA item=menu}
                    <tr>
                        <td><strong>{$menu.menu_name}</strong></td>
                        <td><span class="badge bg-info">{$menu.category}</span></td>
                        <td>{$menu.price}</td>
                        <td>{$menu.order_count}</td>
                        <td>{$menu.total_quantity}</td>
                        <td><strong>{$menu.total_revenue}</strong></td>
                    </tr>
                    {/foreach}
                </tbody>
                <tfoot>
                    <tr class="table-info">
                        <td colspan="4"><strong>Tổng cộng</strong></td>
                        <td><strong>{$MENU_TOTAL_QUANTITY}</strong></td>
                        <td><strong>{$MENU_TOTAL_REVENUE}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        {/if}

        {* Báo cáo nhân viên *}
        {if $REPORT_TYPE eq 'staff' and not empty($STAFF_DATA)}
        <h5 class="mb-3">Báo cáo nhân viên</h5>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{$LANG->getModule('staff_name')}</th>
                        <th>{$LANG->getModule('order_count')}</th>
                        <th>{$LANG->getModule('work_hours')}</th>
                        <th>{$LANG->getModule('total_revenue')}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$STAFF_DATA item=staff}
                    <tr>
                        <td><strong>{$staff.staff_name}</strong></td>
                        <td>{$staff.order_count}</td>
                        <td>{$staff.total_work_hours}</td>
                        <td><strong>{$staff.total_revenue}</strong></td>
                    </tr>
                    {/foreach}
                </tbody>
                <tfoot>
                    <tr class="table-info">
                        <td><strong>Tổng cộng</strong></td>
                        <td><strong>{$STAFF_TOTAL_ORDERS}</strong></td>
                        <td><strong>{$STAFF_TOTAL_HOURS}</strong></td>
                        <td><strong>{$STAFF_TOTAL_REVENUE}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        {/if}
    </div>
</div>
{* END: main *}
