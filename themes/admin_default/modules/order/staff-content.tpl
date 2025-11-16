<!-- BEGIN: main -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{DATA.id > 0 ? '{LANG.staff_edit}' : '{LANG.staff_add}'}</h5>
    </div>
    <div class="card-body">
        <!-- BEGIN: error -->
        <div class="alert alert-danger">{ERROR}</div>
        <!-- END: error -->

        <form method="post" action="">
            <div class="row g-3">
                <!-- BEGIN: user_add -->
                <div class="col-md-6">
                    <label class="form-label">{LANG.staff_user} <span class="text-danger">*</span></label>
                    <select name="userid" class="form-select" required>
                        <option value="0">{LANG.select_user}</option>
                        <!-- BEGIN: user_loop -->
                        <option value="{USER_ID}"{USER_SELECTED}>{USER_NAME}</option>
                        <!-- END: user_loop -->
                    </select>
                </div>
                <!-- END: user_add -->

                <!-- BEGIN: user_edit -->
                <div class="col-md-6">
                    <label class="form-label">{LANG.staff_user}</label>
                    <input type="text" value="{USER_NAME_DISPLAY}" class="form-control" disabled>
                </div>
                <!-- END: user_edit -->

                <div class="col-md-6">
                    <label class="form-label">{LANG.staff_position}</label>
                    <input type="text" name="position" value="{DATA.position}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{LANG.staff_department}</label>
                    <input type="text" name="department" value="{DATA.department}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{LANG.staff_salary}</label>
                    <input type="number" name="salary" value="{DATA.salary}" class="form-control" step="100000" min="0">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{LANG.staff_commission} (%)</label>
                    <input type="number" name="commission_rate" value="{DATA.commission_rate}" class="form-control" step="0.1" min="0" max="100">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{LANG.staff_start_date}</label>
                    <input type="date" name="start_date" value="{DATA.start_date_format}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{LANG.staff_end_date}</label>
                    <input type="date" name="end_date" value="{DATA.end_date_format}" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">{LANG.staff_notes}</label>
                    <textarea name="notes" class="form-control" rows="3">{DATA.notes}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">{LANG.staff_status}</label>
                    <div class="mt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" value="1"{STATUS_1_CHECKED}>
                            <label class="form-check-label">{LANG.status_active}</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" value="0"{STATUS_0_CHECKED}>
                            <label class="form-check-label">{LANG.status_inactive}</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" name="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> {LANG.save}
                </button>
                <a href="{URL_BACK}" class="btn btn-secondary">
                    <i class="fa fa-times"></i> {LANG.cancel}
                </a>
            </div>
        </form>
    </div>
</div>

<!-- END: main -->
