{* BEGIN: main *}
<div class="card">
    <div class="card-header text-bg-primary">
        <h5 class="mb-0"><i class="bi bi-pencil"></i> {$LANG->getModule('item_add')}</h5>
    </div>
    <div class="card-body">
        {if not empty($ERROR)}
        <div class="alert alert-danger">
            {$ERROR|@join:"<br />"}
        </div>
        {/if}

        <form action="{$smarty.const.NV_BASE_ADMINURL}index.php?{$smarty.const.NV_LANG_VARIABLE}={$smarty.const.NV_LANG_DATA}&amp;{$smarty.const.NV_NAME_VARIABLE}={$MODULE_NAME}&amp;{$smarty.const.NV_OP_VARIABLE}={$OP}&amp;item_id={$DATA.item_id}" method="post" novalidate>
            <input type="hidden" name="checkss" value="{$NV_CHECK}" />

            <div class="mb-3">
                <label class="form-label">{$LANG->getModule('title')} <span class="text-danger">*</span></label>
                <div class="position-relative">
                    <input type="text" name="title" value="{$DATA.title}" class="form-control required" maxlength="250" required>
                    <div class="invalid-tooltip">{$LANG->getModule('error_required_title')}</div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">{$LANG->getModule('alias')}</label>
                <div class="input-group">
                    <input type="text" name="alias" value="{$DATA.alias}" class="form-control" maxlength="250">
                    <button class="btn btn-secondary" type="button" aria-label="{$LANG->getModule('alias_hint')}" data-bs-toggle="tooltip" data-bs-title="{$LANG->getModule('alias_hint')}">
                        <i class="bi bi-arrow-repeat"></i>
                    </button>
                </div>
                <small class="form-text text-muted">{$LANG->getModule('alias_hint')}</small>
            </div>

            <div class="mb-3">
                <label class="form-label">{$LANG->getModule('content')}</label>
                <textarea name="content" class="form-control" rows="10">{$DATA.content}</textarea>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('status')}</label>
                        <select name="status" class="form-select">
                            <option value="1" {if $DATA.status eq 1}selected{/if}>{$LANG->getModule('status_active')}</option>
                            <option value="0" {if $DATA.status eq 0}selected{/if}>{$LANG->getModule('status_inactive')}</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('weight')}</label>
                        <input type="number" name="weight" value="{$DATA.weight}" class="form-control" min="0">
                    </div>
                </div>
            </div>

            <div class="text-end">
                <a href="{$smarty.const.NV_BASE_ADMINURL}index.php?{$smarty.const.NV_LANG_VARIABLE}={$smarty.const.NV_LANG_DATA}&amp;{$smarty.const.NV_NAME_VARIABLE}={$MODULE_NAME}&amp;{$smarty.const.NV_OP_VARIABLE}=main" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> {$LANG->getGlobal('back')}
                </a>
                <button type="submit" name="submit" value="1" class="btn btn-primary">
                    <i class="bi bi-save"></i> {$LANG->getGlobal('save')}
                </button>
            </div>
        </form>
    </div>
</div>
{* END: main *}
