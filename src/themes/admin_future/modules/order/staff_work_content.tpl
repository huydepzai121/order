{* BEGIN: main *}
<div class="card">
    <div class="card-header text-bg-primary">
        <h5 class="mb-0"><i class="bi bi-calendar-plus"></i> {if $WORK_ID > 0}{$LANG->getModule('staff_work_edit')}{else}{$LANG->getModule('staff_work_add')}{/if}</h5>
    </div>
    <div class="card-body">
        {if not empty($ERROR)}
        <div class="alert alert-danger">
            {$ERROR|@join:"<br />"}
        </div>
        {/if}

        <form action="{$smarty.const.NV_BASE_ADMINURL}index.php?{$smarty.const.NV_LANG_VARIABLE}={$smarty.const.NV_LANG_DATA}&amp;{$smarty.const.NV_NAME_VARIABLE}={$MODULE_NAME}&amp;{$smarty.const.NV_OP_VARIABLE}={$OP}&amp;work_id={$WORK_ID}" method="post">
            <input type="hidden" name="checkss" value="{$NV_CHECK}" />

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('staff')} <span class="text-danger">*</span></label>
                        <select name="staff_id" class="form-select" required>
                            <option value="0">{$LANG->getModule('staff_select')}</option>
                            {foreach from=$STAFF_LIST item=staff}
                            <option value="{$staff.userid}" {if $staff.selected}selected{/if}>{$staff.full_name}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('work_date')} <span class="text-danger">*</span></label>
                        <input type="date" name="work_date" value="{$WORK_DATE}" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('shift')} <span class="text-danger">*</span></label>
                        <select name="shift" class="form-select" required>
                            <option value="">{$LANG->getModule('select')}</option>
                            {foreach from=$SHIFTS item=shift_item}
                            <option value="{$shift_item.value}" {if $shift_item.selected}selected{/if}>{$shift_item.value}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('start_time')}</label>
                        <input type="time" name="start_time" value="{$START_TIME}" class="form-control">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('end_time')}</label>
                        <input type="time" name="end_time" value="{$END_TIME}" class="form-control">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">{$LANG->getModule('note')}</label>
                <textarea name="note" rows="3" class="form-control">{$NOTE}</textarea>
            </div>

            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> {$LANG->getModule('staff_work_auto_calc_note')}
            </div>

            <div class="text-end">
                <a href="{$smarty.const.NV_BASE_ADMINURL}index.php?{$smarty.const.NV_LANG_VARIABLE}={$smarty.const.NV_LANG_DATA}&amp;{$smarty.const.NV_NAME_VARIABLE}={$MODULE_NAME}&amp;{$smarty.const.NV_OP_VARIABLE}=staff-work" class="btn btn-secondary">
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
