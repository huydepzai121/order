{* BEGIN: main *}
<div class="card">
    <div class="card-header text-bg-primary">
        <h5 class="mb-0"><i class="bi bi-gear"></i> {$LANG->getModule('config')}</h5>
    </div>
    <div class="card-body">
        {if not empty($SUCCESS)}
        <div class="alert alert-success">
            <i class="bi bi-check-circle"></i> {$SUCCESS}
        </div>
        {/if}

        {if not empty($ERROR)}
        <div class="alert alert-danger">
            {foreach from=$ERROR item=err}
            <div>{$err}</div>
            {/foreach}
        </div>
        {/if}

        <form action="{$smarty.const.NV_BASE_ADMINURL}index.php?{$smarty.const.NV_LANG_VARIABLE}={$smarty.const.NV_LANG_DATA}&amp;{$smarty.const.NV_NAME_VARIABLE}={$MODULE_NAME}&amp;{$smarty.const.NV_OP_VARIABLE}={$OP}" method="post">
            <input type="hidden" name="checkss" value="{$NV_CHECK}" />

            <div class="mb-3">
                <label class="form-label">Tiền tố mã đơn hàng</label>
                <input type="text" name="order_prefix" value="{$ORDER_PREFIX}" class="form-control">
                <small class="form-text text-muted">Ví dụ: ORD</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Tự động tạo mã đơn hàng</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="auto_order_code" value="1" id="autoOrderCode" {if $AUTO_ORDER_CODE_CHECKED}checked{/if}>
                    <label class="form-check-label" for="autoOrderCode">
                        Tự động tạo mã đơn hàng khi thêm mới
                    </label>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Phương thức thanh toán mặc định</label>
                <select name="default_payment_method" class="form-select">
                    {foreach from=$PAYMENT_METHODS item=method}
                    <option value="{$method.key}" {if $method.selected}selected{/if}>{$method.value}</option>
                    {/foreach}
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Danh sách ca làm việc</label>
                <input type="text" name="work_shifts" value="{$WORK_SHIFTS}" class="form-control">
                <small class="form-text text-muted">Các ca làm việc cách nhau bởi dấu phẩy. Ví dụ: Sáng,Chiều,Tối</small>
            </div>

            <div class="text-end">
                <button type="submit" name="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {$LANG->getModule('save')}
                </button>
            </div>
        </form>
    </div>
</div>
{* END: main *}
