<!-- BEGIN: main -->
<div class="card">
    <div class="card-header text-bg-primary">
        <h5 class="mb-0"><i class="bi bi-menu-button-wide"></i> {LANG.menu_list}</h5>
    </div>
    <div class="card-body">
        <!-- Bộ lọc -->
        <form action="{NV_BASE_ADMINURL}index.php" method="get" class="mb-4">
            <input type="hidden" name="{NV_LANG_VARIABLE}" value="{NV_LANG_DATA}">
            <input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}">
            <input type="hidden" name="{NV_OP_VARIABLE}" value="{OP}">

            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" value="{SEARCH}" class="form-control" placeholder="{LANG.search}...">
                </div>

                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">{LANG.all}</option>
                        <!-- BEGIN: category -->
                        <option value="{CATEGORY.value}" {CATEGORY.selected}>{CATEGORY.value}</option>
                        <!-- END: category -->
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="-1">{LANG.all}</option>
                        <!-- BEGIN: status_filter -->
                        <option value="{STATUS.key}" {STATUS.selected}>{STATUS.value}</option>
                        <!-- END: status_filter -->
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> {LANG.filter}</button>
                </div>
            </div>
        </form>

        <!-- Nút thêm mới -->
        <div class="mb-3">
            <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}=menu-content" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> {LANG.menu_add}
            </a>
        </div>

        <!-- BEGIN: items -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>{LANG.menu_code}</th>
                        <th>{LANG.menu_name}</th>
                        <th>{LANG.category}</th>
                        <th>{LANG.price}</th>
                        <th>{LANG.status}</th>
                        <th>{LANG.weight}</th>
                        <th class="text-center">{LANG.action}</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- BEGIN: loop -->
                    <tr>
                        <td><code>{ITEM.menu_code}</code></td>
                        <td><strong>{ITEM.menu_name}</strong></td>
                        <td><span class="badge bg-info">{ITEM.category}</span></td>
                        <td><strong>{ITEM.price}</strong></td>
                        <td><span class="badge bg-{ITEM.status_class}">{ITEM.status_text}</span></td>
                        <td>{ITEM.weight}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{ITEM.edit_url}" class="btn btn-primary" title="{LANG.edit}">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-danger" onclick="confirmDelete({ITEM.menu_id}, '{ITEM.menu_name}');" title="{LANG.delete}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <!-- END: loop -->
                </tbody>
            </table>
        </div>
        <!-- END: items -->

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
function confirmDelete(menuId, menuName) {
    if (confirm('{LANG.confirm_delete} ' + menuName + '?')) {
        $.ajax({
            url: '{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}={MODULE_NAME}&{NV_OP_VARIABLE}=menu-del',
            type: 'POST',
            data: { menu_id: menuId },
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
