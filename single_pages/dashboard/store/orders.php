<?php 
defined('C5_EXECUTE') or die("Access Denied.");
?>

<?php if ($controller->getTask() == 'order'){ ?>
    
    <h3><?=t("Customer Overview")?></h3>
    <hr>
    <div class="row">
        <div class="col-sm-6">
            <h4><?=t("Billing Information")?></h4>
            <p>
                <?=$order->getAttribute("billing_first_name"). " " . $order->getAttribute("billing_last_name")?><br>
                <?=$order->getAttribute("billing_address")->address1?><br>
                <?php if($order->getAttribute("billing_address")->address2){
                    echo $order->getAttribute("billing_address")->address2 . "<br>";
                } ?>
                <?=$order->getAttribute("billing_address")->city?>, <?=$order->getAttribute("billing_address")->state_province?> <?=$order->getAttribute("billing_address")->postal_code?><br>
                <?=$order->getAttribute("billing_phone")?>
            </p>
        </div>
        <div class="col-sm-6">
            <h4><?=t("Shipping Information")?></h4>
            <p>
                <?=$order->getAttribute("shipping_first_name"). " " . $order->getAttribute("shipping_last_name")?><br>
                <?=$order->getAttribute("shipping_address")->address1?><br>
                <?php if($order->getAttribute("shipping_address")->address2){
                    echo $order->getAttribute("shipping_address")->address2 . "<br>";
                } ?>
                <?=$order->getAttribute("shipping_address")->city?>, <?=$order->getAttribute("shipping_address")->state_province?> <?=$order->getAttribute("shipping_address")->postal_code?><br>
                
            </p>
        </div>
    </div>
    <h3><?=t("Items Orders")?></h3>
    <hr>
    <table class="table table-striped">
        <thead>
            <tr>
                <th><strong><?=t("Product Name")?></strong></th>
                <th><?=t("Product Options")?></th>
                <th><?=t("Price")?></th>
                <th><?=t("Quantity")?></th>
                <th><?=t("Subtotal")?></th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $items = $order->getOrderItems();
                if($items){
                    foreach($items as $item){
              ?>
                <tr>
                    <td><?=$item->getProductName()?></td>
                    <td>
                        <?php
                            $options = $item->getProductOptions();
                            if($options){
                                echo "<ul class='list-unstyled'>";
                                foreach($options as $option){
                                    echo "<li>";    
                                    echo "<strong>".$option['oioKey'].": </strong>";
                                    echo $option['oioValue'];
                                    echo "</li>";
                                }      
                                echo "</ul>";
                            }
                        ?>
                    </td>
                    <td><?=$item->getPricePaid()?></td>
                    <td><?=$item->getQty()?></td>
                    <td><?=$item->getSubTotal()?></td>
                </tr>
              <?php
                    }
                }
            ?>
        </tbody>
    </table>
    
    <p>
        <strong><?=t("Tax")?>:</strong>  <?=$order->getTaxTotal()?><br>
        <strong><?=t("Shipping")?>:</strong>  <?=$order->getShippingTotal()?><br>
        <strong class="text-large"><?=t("Total")?>:</strong>  <?=$order->getTotal()?><br>
        <strong><?=t("Payment Method")?>:</strong> <?=$order->getPaymentMethodName()?>
    </p>

    <h3><?=t("Order Status History")?></h3>
    <hr>
    <div class="row">
        <div class="col-sm-4 col-sm-push-8 col-md-3 col-md-push-9">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title"><?=t("Update Status")?></h4>
                </div>
                <div class="panel-body">

                    <form action="<?=View::url("/dashboard/store/orders/updatestatus",$order->getOrderID())?>" method="post">
                        <div class="form-group">
                            <?php echo $form->select("orderStatus",$orderStatuses,$order->getStatus());?>
                        </div>
                        <input type="submit" class="btn btn-default" value="<?=t("Update")?>">
                    </form>

                </div>
            </div>
        </div>
        <div class="col-sm-8 col-sm-pull-4 col-md-9 col-md-pull-3">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th><strong><?=t("Status")?></strong></th>
                    <th><?=t("Date")?></th>
                    <th><?=t("User")?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $history = $order->getStatusHistory();
                if($history){
                    foreach($history as $status){
                        ?>
                        <tr>
                            <td><?=$status->getOrderStatusName()?></td>
                            <td><?=$status->getDate()?></td>
                            <td><?=$status->getUserName()?></td>
                        </tr>
                    <?php
                    }
                }
                ?>
                </tbody>
            </table>
        </div>

    </div>

    <h3><?=t("Manage Order")?></h3>
    <hr>
    <div class="row">
        <div class="col-sm-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title"><?=t("Order Options")?></h4>
                </div>
                <div class="panel-body">
                    
                    <a id="btn-delete-order" href="<?=View::url("/dashboard/store/orders/remove", $order->getOrderID())?>" class="btn btn-danger"><?=t("Delete Order")?></a>
                    
                </div>
            </div>
        </div>
    </div>
    
    
<?php } else { ?>
<table class="table table-striped">
    <thead>
        <th><?=t("Order %s","#")?></th>
        <th><?=t("Customer Name")?></th>
        <th><?=t("Order Date")?></th>
        <th><?=t("Total")?></th>
        <th><?=t("Status")?></th>
        <th><?=t("View")?></th>
    </thead>
    <tbody>
        <?php
            foreach($orderList as $order){
        ?>
            <tr>
                <td><a href="<?=View::url('/dashboard/store/orders/order/',$order->getOrderID())?>"><?=$order->getOrderID()?></a></td>
                <td><?=$order->getAttribute('billing_last_name').", ".$order->getAttribute('billing_first_name')?></td>
                <td><?=$order->getOrderDate()?></td>
                <td><?=$order->getTotal()?></td>
                <td><?=ucwords($order->getStatus())?></td>
                <td><a class="btn btn-primary" href="<?=View::url('/dashboard/store/orders/order/',$order->getOrderID())?>"><?=t("View")?></a></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<?php if ($paginator->getTotalPages() > 1) { ?>
    <?= $pagination ?>
<?php } ?>

<?php } ?>