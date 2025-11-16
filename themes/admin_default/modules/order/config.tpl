<!-- BEGIN: main -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{LANG.config}</h5>
    </div>
    <div class="card-body">
        <!-- BEGIN: error -->
        <div class="alert alert-danger">{ERROR}</div>
        <!-- END: error -->

        <!-- BEGIN: success -->
        <div class="alert alert-success">{SUCCESS}</div>
        <!-- END: success -->

        <form method="post" action="">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Tiền tố mã đơn hàng</label>
                    <input type="text" name="order_prefix" value="{DATA.order_prefix}" class="form-control">
                    <small class="text-muted">VD: ORD, DH, ORDER</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Thuế VAT (%)</label>
                    <input type="number" name="tax_rate" value="{DATA.tax_rate}" class="form-control" step="0.1" min="0" max="100">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Đơn vị tiền tệ</label>
                    <input type="text" name="currency" value="{DATA.currency}" class="form-control">
                    <small class="text-muted">VD: VND, USD, EUR</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tự động tạo mã đơn hàng</label>
                    <div class="mt-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="auto_order_code" value="1"{AUTO_ORDER_CODE_CHECKED}>
                            <label class="form-check-label">
                                Bật tự động tạo mã đơn hàng
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" name="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> {LANG.save}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- END: main -->
