<?php
defined('C5_EXECUTE') or die("Access Denied.");
extract($vars); ?>
<script type="text/x-template" id="discount-reward-type-form" data-complete-function="addNewDiscountRewardType">
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                <label class="form-label" for="discountCalculation"><?=t("Discount Type")?></label>
                <select name="discountCalculation" id="discountCalculation" class="form-control">
                    <option value="percentage"><?=t("Percentage off")?></option>
                    <option value="flatRate"><?=t("Flat rate")?></option>
                </select>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                <label class="form-label" for="discountAmount"><?=t("Discount Amount")?></label>
                <input type="text" name="discountAmount" id="discountAmount" class="form-control">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12" id="discount-subject-selector">
            <div class="form-group">
                <label class="form-label" for="discountSubject"><?=t("Discount applies to")?></label>
                <select name="discountSubject" id="discountSubject" class="form-control" onChange="onDiscountSubjectChange()">
                    <option value="grandTotal"><?=t("Grand Total")?></option>
                    <option value="subTotal"><?=t("Sub Total")?></option>
                    <option value="productGroup"><?=t("Products within a Group")?></option>
                </select>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6 hidden" id="product-selector">
            <div class="form-group">
                <label class="form-label" for="discountTarget"><?=t("Group to discount")?></label>
                <select name="discountTarget" id="discountTarget" class="form-control">
                    <?php foreach ($grouplist as $group) { ?>
                        <option value="<?=$group->getGroupID()?>"><?=$group->getGroupName()?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>
</script>
<script type="text/javascript">
    function onDiscountSubjectChange(){
        if($('.vivid-store-dialog #discountSubject option:selected').val() == 'productGroup'){
            $('.vivid-store-dialog #discount-subject-selector').removeClass('col-sm-12').addClass('col-sm-6');
            $('.vivid-store-dialog #product-selector').removeClass('hidden');
        } else {
            $('.vivid-store-dialog #discount-subject-selector').removeClass('col-sm-6').addClass('col-sm-12');
            $('.vivid-store-dialog #product-selector').addClass('hidden');
        }
    }
    function addNewDiscountRewardType(){
        var fields = {
            discountCalculation: $('.vivid-store-dialog #discountCalculation').val(),
            discountAmount: $('.vivid-store-dialog #discountAmount').val(),
            discountSubject: $('.vivid-store-dialog #discountSubject').val(),
            discountTarget: $('.vivid-store-dialog #discountTarget').val()
        }
        $.ajax({
            url: '<?=URL::to('/dashboard/store/promotions/manage/add_reward/',$rewardType->getID())?>',
            data: {
                discountCalculation: fields.discountCalculation,
                discountAmount: fields.discountAmount,
                discountSubject: fields.discountSubject,
                discountTarget: fields.discountTarget
            },
            method: "post",
            error: function(){
                alert('something went wrong');
            },
            success: function(){
                return onNewDiscountRewardSuccess(fields);
            }

        });
    }
    function onNewDiscountRewardSuccess(fields){
        if(fields.discountCalculation=='percentage'){
            var discountString = fields.discountAmount + "%";
        } else {
            var discountString = "$" + fields.discountAmount;
        }
        if(fields.discountSubject == 'productGroup'){
            var discountTargetString = $('.vivid-store-dialog #discountTarget option:selected').text() + " Product Group";
        } else {
            var discountTargetString = $('.vivid-store-dialog #discountSubject option:selected').text();
        }
        var params = {
            discountAmount: discountString,
            discountTarget: discountTargetString
        }
        var listItemTemplate = _.template($('#discount-list-item-template').html());
        return listItemTemplate(params);
    }
</script>

<script type="text/x-template" id="discount-list-item-template">
    <?=t('%s off of %s','<strong><%=discountAmount%></strong>', '<strong><%=discountTarget%></strong>')?>
</script>
