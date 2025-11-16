<!-- BEGIN: main -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{DATA.id > 0 ? '{LANG.dish_edit}' : '{LANG.dish_add}'}</h5>
    </div>
    <div class="card-body">
        <!-- BEGIN: error -->
        <div class="alert alert-danger">{ERROR}</div>
        <!-- END: error -->

        <form method="post" action="">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">{LANG.dish_name} <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{DATA.name}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{LANG.dish_alias}</label>
                    <input type="text" name="alias" value="{DATA.alias}" class="form-control">
                    <small class="text-muted">Để trống để tự động tạo</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{LANG.dish_price} <span class="text-danger">*</span></label>
                    <input type="number" name="price" value="{DATA.price}" class="form-control" step="1000" min="0" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{LANG.dish_weight}</label>
                    <input type="number" name="weight" value="{DATA.weight}" class="form-control" min="0">
                </div>
                <div class="col-12">
                    <label class="form-label">{LANG.dish_description}</label>
                    <textarea name="description" class="form-control" rows="3">{DATA.description}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{LANG.dish_image}</label>
                    <input type="text" name="image" value="{DATA.image}" class="form-control">
                    <small class="text-muted">Đường dẫn hình ảnh</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{LANG.dish_status}</label>
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
