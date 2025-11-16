<div class="card">
    <div class="card-header">
        <h4 class="mb-0">{$LANG.config}</h4>
    </div>
    <div class="card-body">
        {if $SAVED == 1}
        <div class="alert alert-success">{$LANG.save_success}</div>
        {/if}

        {if $ERROR}
        <div class="alert alert-danger">{$ERROR}</div>
        {/if}

        <form action="{$FORM_ACTION}" method="post">
            <input type="hidden" name="save" value="{$NV_CHECK_SESSION}">

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{$LANG.commission_rate}</label>
                        <div class="input-group">
                            <input type="number" name="commission_rate" value="{$ROW.commission_rate}" class="form-control" step="0.01" min="0" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{$LANG.tax_rate}</label>
                        <div class="input-group">
                            <input type="number" name="tax_rate" value="{$ROW.tax_rate}" class="form-control" step="0.01" min="0" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{$LANG.currency_symbol}</label>
                        <input type="text" name="currency_symbol" value="{$ROW.currency_symbol}" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="allow_discount" value="1" id="allow_discount"{if $ROW.allow_discount} checked{/if}>
                            <label class="form-check-label" for="allow_discount">{$LANG.allow_discount}</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> {$LANG.save}
                </button>
            </div>
        </form>
    </div>
</div>
