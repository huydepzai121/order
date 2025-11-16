<!-- BEGIN: main -->
<div class="card">
    <div class="card-header text-bg-primary">
        <h5 class="mb-0"><i class="bi bi-calendar-plus"></i> Thêm công nhân viên</h5>
    </div>
    <div class="card-body">
        <!-- BEGIN: error -->
        <div class="alert alert-danger">
            <!-- BEGIN: loop -->
            <div>{ERROR}</div>
            <!-- END: loop -->
        </div>
        <!-- END: error -->

        <form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}&amp;work_id={WORK_ID}" method="post">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{LANG.staff} <span class="text-danger">*</span></label>
                        <select name="staff_id" class="form-select" required>
                            <option value="0">{LANG.staff_select}</option>
                            <!-- BEGIN: staff -->
                            <option value="{STAFF.userid}" {STAFF.selected}>{STAFF.full_name}</option>
                            <!-- END: staff -->
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{LANG.work_date} <span class="text-danger">*</span></label>
                        <input type="date" name="work_date" value="{WORK_DATE}" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">{LANG.shift} <span class="text-danger">*</span></label>
                        <select name="shift" class="form-select" required>
                            <option value="">{LANG.select}</option>
                            <!-- BEGIN: shift -->
                            <option value="{SHIFT.value}" {SHIFT.selected}>{SHIFT.value}</option>
                            <!-- END: shift -->
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">{LANG.start_time}</label>
                        <input type="time" name="start_time" value="{START_TIME}" class="form-control">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">{LANG.end_time}</label>
                        <input type="time" name="end_time" value="{END_TIME}" class="form-control">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">{LANG.note}</label>
                <textarea name="note" rows="3" class="form-control">{NOTE}</textarea>
            </div>

            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Số đơn hàng và doanh thu sẽ được tự động tính toán dựa trên ngày làm việc.
            </div>

            <div class="text-end">
                <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}=staff-work" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> {LANG.back}
                </a>
                <button type="submit" name="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {LANG.save}
                </button>
            </div>
        </form>
    </div>
</div>
<!-- END: main -->
