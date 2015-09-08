<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
use \Concrete\Package\VividStore\Src\VividStore\Product\Product as VividProduct;
use \Concrete\Package\VividStore\Src\VividStore\Utilities\Price as Price;
?>
<div class="cart-page-cart">

    <h1><?=t("Shopping Cart")?></h1>

    <?php if (isset($actiondata) and !empty($actiondata)) { ?>
        <?php if( $actiondata['action'] =='update') { ?>
            <p class="alert alert-success"><?= t('Your cart has been updated');?></p>
        <?php } ?>

        <?php if($actiondata['action'] == 'clear') { ?>
            <p class="alert alert-warning"><?= t('Your cart has been cleared');?></p>
        <?php } ?>

        <?php if($actiondata['action'] == 'remove') { ?>
            <p class="alert alert-warning"><?= t('Item removed');?></p>
        <?php } ?>

        <?php if($actiondata['quantity'] != $actiondata['added']) { ?>
            <p class="alert alert-warning"><?= t('Due to stock levels your quantity has been limited');?></p>
        <?php } ?>
    <?php } ?>

    <input id='cartURL' type='hidden' data-cart-url='<?=View::url("/cart/")?>'>
    
    <ul class="cart-page-cart-list">
    <?php 
    if($cart){
        $i=1;
        foreach ($cart as $k=>$cartItem){
            $pID = $cartItem['product']['pID'];
            $qty = $cartItem['product']['qty'];
            $product = VividProduct::getByID($pID);
            if($i%2==0){$classes=" striped"; }else{ $classes=""; }
            if(is_object($product)){
    ?>
        
        <li class="cart-page-cart-list-item clearfix<?=$classes?>" data-instance-id="<?=$k?>" data-product-id="<?=$pID?>">
            <div class="cart-list-thumb">
                <a href="<?=URL::page(Page::getByID($product->getProductPageID()))?>">
                    <?=$product->getProductImageThumb()?>
                </a>
            </div>
            <div class="cart-list-product-name">
                <a href="<?=URL::page(Page::getByID($product->getProductPageID()))?>">
                    <?=$product->getProductName()?>
                </a>
            </div>
            
            <div class="cart-list-item-price">
                <?php 
                    $salePrice = $product->getProductSalePrice();
                    if(isset($salePrice) && $salePrice != ""){
                        echo '<span class="original-price">'.Price::format($product->getProductPrice()).'</span>';
                        echo '<span class="sale-price">'.Price::format($salePrice).'</span>';
                    } else {
                        echo Price::format($product->getProductPrice());
                    }
                ?>
            </div>
            <div class="cart-list-product-qty">
                <?php if ($product->allowQuantity()) { ?>
                    <form method="post">
                        <input type="hidden" name="instance" value="<?=$k?>" />
                        <span class="cart-item-label"><?=t("Quantity:")?></span>
                        <input type="number" name="pQty" min="1" <?=($product->allowBackOrders() ? '' :'max="' . $product->getProductQty() . '"' );?> value="<?=$qty?>" style="width: 50px;">
                        <button name="action" value="update" class="btn-cart-list-update" type="submit"><?=t("Update")?></button>
                    </form>
                <?php } ?>
            </div>
            <div class="cart-list-item-links">
                <form method="post">
                    <input type="hidden" name="instance" value="<?=$k?>" />
                     <button name="action" value="remove" class="btn-cart-list-remove" type="submit"><?=t("Remove")?></button>
                </form>
            </div>

            <?php if($cartItem['productAttributes']){?>
            <div class="cart-list-item-attributes">
                <?php foreach($cartItem['productAttributes'] as $groupID => $valID){
                    $groupID = str_replace("pog","",$groupID);
                    ?>
                    <div class="cart-list-item-attribute">
                        <span class="cart-list-item-attribute-label"><?=VividProduct::getProductOptionGroupNameByID($groupID)?>:</span>
                        <span class="cart-list-item-attribute-value"><?=VividProduct::getProductOptionValueByID($valID)?></span>
                    </div>
                <?php }  ?>
            </div>    
            <?php } ?>
        </li>
    
    <?php 
            }//if is_object
        $i++;
        }//foreach 
    }//if cart
    ?>
    </ul>

    <?php if ($discountsWithCodesExist) { ?>
    <h3><?= t('Enter Discount Code');?></h3>
        <form method="post" action="<?= View::url('/cart/');?>">
        <input type="text" name="code" />
            <input type="hidden" name="action" value="code" />
            <button type="submit" class=""><?= t('Apply');?></button>
        </form>
    <?php } ?>

    <?php if ($codesuccess) { ?>
        <p><?= t('Discount has been applied');?></p>
    <?php } ?>

    <?php if ($codeerror) { ?>
        <p><?= t('Invalid code');?></p>
    <?php } ?>

    <?php if(!empty($discounts)) { ?>
    <h3><?= t('Discounts');?></h3>
    <div class="cart-page-discounts">
        <ul>
        <?php foreach($discounts as $discount) { ?>
            <li><?php echo h($discount->getDisplay()); ?></li>
        <?php } ?>
        </ul>
    </div>

    <?php }?>

    <?php if ($cart  && !empty($cart)) { ?>
    <div class="cart-page-cart-total">        
        <span class="cart-grand-total-label"><?=t("Sub Total")?>:</span>
        <span class="cart-grand-total-value"><?=Price::format($total)?></span>
    </div>
        
    <div class="cart-page-cart-links">
        <form method="post">
            <button name="action" value="clear" class="btn-cart-list-clear" type="submit"><?=t("Clear Cart")?></button>
        </form>
        <a class="btn-cart-page-checkout" href="<?=View::url('/checkout')?>"><?=t('Checkout')?></a>
    </div>
    <?php } else { ?>
    <p class="alert alert-info"><?= t('Your cart is empty');?></p>
    <?php } ?>
    
</div>
