<?php 
namespace Concrete\Package\VividStore\Src\VividStore\Report;

use Database;

use \Concrete\Package\VividStore\Src\VividStore\Product\Product;
use \Concrete\Package\VividStore\Src\VividStore\Orders\OrderList;
use \Concrete\Package\VividStore\Src\VividStore\Orders\OrderItemList;

defined('C5_EXECUTE') or die(_("Access Denied."));

class ProductReport 
{
	private $orderItems;
	private $products;
	
	public function __construct()
	{
		$this->setOrderItemsByRange();
		$this->setProducts();
	}
	
	public function setOrderItemsByRange($from=null,$to=null)
	{
		if(!$from){
			$from = OrderList::getDateOfFirstOrder();
		}		
		if(!$to){
			$to = date('Y-m-d');
		}
		$orders = new OrderList();
		$orders->setFromDate($from);
		$orders->setToDate($to);
		$this->orderItems = $orders->getOrderItems();
	}
	
	public function setProducts()
	{
		$products = array();
		foreach($this->orderItems as $oi){
			if(array_key_exists($oi->getProductID(),$products)){
				$products[$oi->getProductID()]['pricePaid'] = intval($products[$oi->getProductID()]['pricePaid']) + intval($oi->getPricePaid());
				$products[$oi->getProductID()]['quantity'] = intval($products[$oi->getProductID()]['quantity']) + intval($oi->getQty());
			} else {
				//first figure out what the current product name is.
				//if the product no longer exist, the OI name is fine.
				$product = Product::getByID($oi->getProductID());
				if(is_object($product)){
					$name = $product->getProductName();
				} else { $name = $oi->getProductName(); }
				$products[$oi->getProductID()] = array(
					'name' => $name,
					'pricePaid' => intval($oi->getPricePaid()) * intval($oi->getQty()),
					'quantity' => intval($oi->getQty())
				);
			}
		}
		$this->products = $products;
		
	}
	public function sortByPopularity($direction = 'desc')
	{
		$products = $this->products;	
		usort($products, create_function('$a, $b', '
	        $a = $a["quantity"];
	        $b = $b["quantity"];
	
	        if ($a == $b)
	        {
	            return 0;
	        }
	
	        return ($a ' . ($direction == 'desc' ? '>' : '<') .' $b) ? -1 : 1;
	    '));
		$this->products = $products;
	}
	public function sortByTotal($direction = 'desc')
	{
		$products = $this->products;
		usort($products, create_function('$a, $b', '
	        $a = $a["pricePaid"];
	        $b = $b["pricePaid"];
	
	        if ($a == $b)
	        {
	            return 0;
	        }
	
	        return ($a ' . ($direction == 'desc' ? '>' : '<') .' $b) ? -1 : 1;
	    '));
		$this->products = $products;
	}
	
	public function getOrderItems(){ return $this->orderItems; }
	public function getProducts(){ return $this->products; }
	
}
