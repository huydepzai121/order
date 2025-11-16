<!-- BEGIN: main -->
<div class="card">
    <div class="card-header text-bg-primary">
        <h5 class="mb-0"><i class="bi bi-menu-button-wide-fill"></i> {LANG.menu_add}</h5>
    </div>
    <div class="card-body">
        <!-- BEGIN: error -->
        <div class="alert alert-danger">
            <!-- BEGIN: loop -->
            <div>{ERROR}</div>
            <!-- END: loop -->
        </div>
        <!-- END: error -->

        <form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}&amp;menu_id={MENU_ID}" method="post">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{LANG.menu_name} <span class="text-danger">*</span></label>
                        <input type="text" name="menu_name" value="{MENU_NAME}" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{LANG.menu_code} <span class="text-danger">*</span></label>
                        <input type="text" name="menu_code" value="{MENU_CODE}" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{LANG.category} <span class="text-danger">*</span></label>
                        <input type="text" name="category" value="{CATEGORY}" class="form-control" list="categoryList" required>
                        <datalist id="categoryList">
                            <!-- BEGIN: category -->
                            <option value="{CATEGORY_ITEM.value}">
                            <!-- END: category -->
                        </datalist>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{LANG.price}</label>
                        <input type="text" name="price" value="{PRICE}" class="form-control">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">{LANG.description}</label>
                <textarea name="description" rows="3" class="form-control">{DESCRIPTION}</textarea>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{LANG.status}</label>
                        <select name="status" class="form-select">
                            <option value="1" {STATUS.selected|if:STATUS==1}>{ LANG.active}</option>
                            <option value="0" {STATUS.selected|if:STATUS==0}>{LANG.inactive}</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{LANG.weight}</label>
                        <input type="number" name="weight" value="{WEIGHT}" class="form-control">
                    </div>
                </div>
            </div>

            <div class="text-end">
                <a href="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}=menu" class="btn btn-secondary">
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
