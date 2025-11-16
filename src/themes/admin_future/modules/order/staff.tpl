{* BEGIN: main *}
<div class="card">
    <div class="card-header text-bg-primary">
        <h5 class="mb-0"><i class="bi bi-people"></i> {$LANG->getModule('staff_list')}</h5>
    </div>
    <div class="card-body">
        {* Bộ lọc *}
        <form action="{$smarty.const.NV_BASE_ADMINURL}index.php" method="get" class="mb-4">
            <input type="hidden" name="{$smarty.const.NV_LANG_VARIABLE}" value="{$smarty.const.NV_LANG_DATA}">
            <input type="hidden" name="{$smarty.const.NV_NAME_VARIABLE}" value="{$MODULE_NAME}">
            <input type="hidden" name="{$smarty.const.NV_OP_VARIABLE}" value="{$OP}">

            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" value="{$SEARCH}" class="form-control" placeholder="{$LANG->getModule('search')}...">
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> {$LANG->getModule('search')}</button>
                </div>
            </div>
        </form>

        {if not empty($STAFF_LIST)}
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>{$LANG->getModule('staff_name')}</th>
                        <th>{$LANG->getModule('staff_email')}</th>
                        <th>{$LANG->getModule('regdate')}</th>
                        <th>{$LANG->getModule('order_count')}</th>
                        <th>{$LANG->getModule('total_revenue')}</th>
                        <th class="text-center">{$LANG->getModule('action')}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$STAFF_LIST item=staff}
                    <tr>
                        <td>{$staff.userid}</td>
                        <td><strong>{$staff.full_name}</strong><br><small class="text-muted">@{$staff.username}</small></td>
                        <td>{$staff.email}</td>
                        <td>{$staff.regdate}</td>
                        <td><span class="badge bg-primary">{$staff.order_count}</span></td>
                        <td><strong>{$staff.total_revenue}</strong></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{$staff.view_orders_url}" class="btn btn-info" title="{$LANG->getModule('view_orders')}">
                                    <i class="bi bi-receipt"></i>
                                </a>
                                <a href="{$staff.view_work_url}" class="btn btn-success" title="{$LANG->getModule('view_work')}">
                                    <i class="bi bi-calendar-check"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
        {else}
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> {$LANG->getModule('no_data')}
        </div>
        {/if}

        {if not empty($GENERATE_PAGE)}
        <div class="mt-3">
            {$GENERATE_PAGE}
        </div>
        {/if}
    </div>
</div>
{* END: main *}
