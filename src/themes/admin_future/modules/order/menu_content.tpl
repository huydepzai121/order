{* BEGIN: main *}
<div class="card">
    <div class="card-header text-bg-primary">
        <h5 class="mb-0"><i class="bi bi-menu-button-wide-fill"></i> {$LANG->getModule('menu_add')}</h5>
    </div>
    <div class="card-body">
        {if not empty($ERROR)}
        <div class="alert alert-danger">
            {$ERROR|@join:"<br />"}
        </div>
        {/if}

        <form action="{$smarty.const.NV_BASE_ADMINURL}index.php?{$smarty.const.NV_LANG_VARIABLE}={$smarty.const.NV_LANG_DATA}&amp;{$smarty.const.NV_NAME_VARIABLE}={$MODULE_NAME}&amp;{$smarty.const.NV_OP_VARIABLE}={$OP}&amp;menu_id={$MENU_ID}" method="post">
            <input type="hidden" name="checkss" value="{$NV_CHECK}" />

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('menu_name')} <span class="text-danger">*</span></label>
                        <input type="text" name="menu_name" value="{$MENU_NAME}" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('menu_code')} <span class="text-danger">*</span></label>
                        <input type="text" name="menu_code" value="{$MENU_CODE}" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('category')} <span class="text-danger">*</span></label>
                        <input type="text" name="category" value="{$CATEGORY}" class="form-control" list="categoryList" required>
                        <datalist id="categoryList">
                            {foreach from=$CATEGORIES item=cat}
                            <option value="{$cat}">
                            {/foreach}
                        </datalist>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('price')}</label>
                        <input type="text" name="price" value="{$PRICE}" class="form-control">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">{$LANG->getModule('description')}</label>
                <textarea name="description" rows="3" class="form-control">{$DESCRIPTION}</textarea>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('status')}</label>
                        <select name="status" class="form-select">
                            {foreach from=$STATUS_LIST item=status_item}
                            <option value="{$status_item.key}" {if $status_item.selected}selected{/if}>{$status_item.value}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('weight')}</label>
                        <input type="number" name="weight" value="{$WEIGHT}" class="form-control">
                    </div>
                </div>
            </div>

            <div class="text-end">
                <a href="{$smarty.const.NV_BASE_ADMINURL}index.php?{$smarty.const.NV_LANG_VARIABLE}={$smarty.const.NV_LANG_DATA}&amp;{$smarty.const.NV_NAME_VARIABLE}={$MODULE_NAME}&amp;{$smarty.const.NV_OP_VARIABLE}=menu" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> {$LANG->getModule('back')}
                </a>
                <button type="submit" name="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {$LANG->getModule('save')}
                </button>
            </div>
        </form>
    </div>
</div>
{* END: main *}
