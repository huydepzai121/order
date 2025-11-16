<!-- BEGIN: main -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{LANG.staff_list}</h5>
        <a href="{URL_ADD}" class="btn btn-primary">
            <i class="fa fa-plus"></i> {LANG.staff_add}
        </a>
    </div>
    <div class="card-body">
        <!-- Filter form -->
        <form method="get" action="" class="row g-3 mb-3">
            <input type="hidden" name="{NV_LANG_VARIABLE}" value="{NV_LANG_DATA}">
            <input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}">
            <input type="hidden" name="{NV_OP_VARIABLE}" value="staff">

            <div class="col-md-5">
                <input type="text" name="search" value="{SEARCH}" class="form-control" placeholder="{LANG.search}...">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <!-- BEGIN: status_all -->
                    <option value="-1"{STATUS_SELECTED}>{LANG.all}</option>
                    <!-- END: status_all -->
                    <!-- BEGIN: status_active -->
                    <option value="1"{STATUS_SELECTED}>{LANG.status_active}</option>
                    <!-- END: status_active -->
                    <!-- BEGIN: status_inactive -->
                    <option value="0"{STATUS_SELECTED}>{LANG.status_inactive}</option>
                    <!-- END: status_inactive -->
                </select>
            </div>
            <div class="col-md-3">
                <select name="department" class="form-select">
                    <!-- BEGIN: dept_all -->
                    <option value=""{DEPT_SELECTED}>{LANG.all}</option>
                    <!-- END: dept_all -->
                    <!-- BEGIN: dept_loop -->
                    <option value="{DEPT_VALUE}"{DEPT_SELECTED}>{DEPT_VALUE}</option>
                    <!-- END: dept_loop -->
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
                        <th width="50">STT</th>
                        <th>{LANG.staff_user}</th>
                        <th>{LANG.staff_position}</th>
                        <th>{LANG.staff_department}</th>
                        <th>{LANG.staff_salary}</th>
                        <th>{LANG.staff_status}</th>
                        <th>{LANG.staff_start_date}</th>
                        <th>{LANG.actions}</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- BEGIN: loop -->
                    <tr>
                        <td>{STAFF.stt}</td>
                        <td>
                            <strong>{STAFF.full_name}</strong><br>
                            <small class="text-muted">{STAFF.username}</small>
                        </td>
                        <td>{STAFF.position}</td>
                        <td>{STAFF.department}</td>
                        <td>{STAFF.salary_format}</td>
                        <td><span class="badge bg-{STAFF.status_class}">{STAFF.status_text}</span></td>
                        <td>{STAFF.start_date_format}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{STAFF.url_edit}" class="btn btn-primary" title="{LANG.edit}">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a href="javascript:void(0);" onclick="nv_del_staff({STAFF.id});" class="btn btn-danger" title="{LANG.delete}">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <!-- END: loop -->
                    <!-- BEGIN: empty -->
                    <tr>
                        <td colspan="8" class="text-center">Không có dữ liệu</td>
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
function nv_del_staff(id) {
    if (confirm('{LANG.confirm_delete}')) {
        $.ajax({
            url: script_name + '?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '={MODULE_NAME}&' + nv_fc_variable + '=staff-del&id=' + id,
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
