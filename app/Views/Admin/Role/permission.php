<br><button type="button" class="btn btn-xs btn-primary select-all-btn2" onclick="selectAll(this)" >Select All</button>
<div style="display: flex; column-gap: 15px; flex-wrap: wrap;">
    <?php
    foreach ($main as $k => $v) {
        $isChecked = (isset($permission[$k]) && $permission[$k] == 1) ? 'checked="checked"' : '';
        $mName = str_replace('_', ' ', $k);
        $displayName = str_replace(['update', 'read', 'mod'], ['edit', 'view', 'Module'], $mName);
        ?>

        <div class="checkbox text-capitalize" style="margin-top: 5px;">
            <label>
                <input type="checkbox" <?php echo $isChecked; ?> name="permission[<?php echo $k; ?>]" value="1">
                <?php echo $displayName; ?>
            </label>
        </div>

    <?php } ?>
</div>
<div>
    <button type="submit" class="btn btn-primary btn-sm" >Update</button>
</div>

