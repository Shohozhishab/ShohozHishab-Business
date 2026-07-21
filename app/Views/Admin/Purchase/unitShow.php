
<?php foreach ($units as $val){ ?>
    <div class="form-group col-xs-3">
        <label for="int" class="text-capitalize"><?= $val->name;?> </label>
        <input type="text" class="form-control" name="<?= strtolower(str_replace(' ', '_', $val->name));?>" placeholder="<?= $val->name;?>" value="" >
    </div>
<?php } ?>

<input type="hidden" name="purchase_units_price" value="<?= $unitSet->purchase_units_price;?>" >
<input type="hidden" name="sell_unit_price" value="<?= $unitSet->sell_unit_price;?>" >

<div class="form-group col-xs-3">
    <label for="int">Purchase Price (<?= showUnitName($unitSet->purchase_units_price); ?>)</label>
    <input type="number" class="form-control purchase_price" oninput="minusValueCheck(this.value,this)" name="price" id="price" placeholder="Purchase Price"/>
</div>
<div class="form-group col-xs-3">
    <label for="int">Selling Price (<?= showUnitName($unitSet->sell_unit_price); ?>)</label>
    <input type="number" class="form-control selling_price" oninput="minusValueCheck(this.value,this)" name="selling_price" id="selling_price" placeholder="Selling Price"/>
</div>
<div class="form-group col-xs-3">
    <label for="int">Unit</label>
    <select class="form-control" name="sale_unit">
        <?php foreach ($unitsArray as $item){?>
        <option value="<?= $item->units_id;?>" <?= ($saleUnit->units_id == $item->units_id)?'selected':'';?> ><?= $item->name;?></option>
        <?php } ?>
    </select>
</div>

