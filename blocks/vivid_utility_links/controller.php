<?php
namespace Concrete\Package\VividStore\Block\VividUtilityLinks;
use \Concrete\Core\Block\BlockController;
use Package;
use Core;
use View;
use User;
use UserInfo;
use \Concrete\Package\VividStore\Src\VividStore\Cart\Cart as VividCart;


defined('C5_EXECUTE') or die("Access Denied.");
class Controller extends BlockController
{
    protected $btTable = 'btVividUtilityLinks';
    protected $btInterfaceWidth = "450";
    protected $btWrapperClass = 'ccm-ui';
    protected $btInterfaceHeight = "400";
    protected $btDefaultSet = 'vivid_store';

    public function getBlockTypeDescription()
    {
        return t("Add your cart links for Vivid Store");
    }

    public function getBlockTypeName()
    {
        return t("Utility Links");
    }
    public function view()
    {
        $pkg = Package::getByHandle('vivid_store');    
        $packagePath = $pkg->getRelativePath();
        $this->addFooterItem(Core::make('helper/html')->javascript($packagePath.'/js/vivid-store.js','vivid-store'));
        $this->addHeaderItem(Core::make('helper/html')->css($packagePath.'/css/vivid-store.css','vivid-store'));    
        $this->set("itemCount",VividCart::getTotalItemsInCart());
        $this->addHeaderItem("
            <script type=\"text/javascript\">
                var PRODUCTMODAL = '".View::url('/productmodal')."';
                var CARTURL = '".View::url('/cart')."';
                var CHECKOUTURL = '".View::url('/checkout')."';
                var QTYMESSAGE = '".t('Quantity must be greater than zero')."';
            </script>
        ");
    }
    public function save($args)
    {
        $args['showCartItems'] = isset($args['showCartItems']) ? 1 : 0;
        $args['showSignIn'] = isset($args['showSignIn']) ? 1 : 0;
        parent::save($args);
    }
    public function validate($args)
    {
        $e = Core::make("helper/validation/error"); 
        if($args['cartLabel']==""){
            $e->add(t('Cart Label must be set'));
        }
        if(strlen($args['cartLabel']) > 255){
            $e->add(t('Cart Link Label exceeds 255 characters'));
        }
        if(strlen($args['itemsLabel']) > 255){
            $e->add(t('Cart Items Label exceeds 255 characters'));
        }
        return $e;
    }
}