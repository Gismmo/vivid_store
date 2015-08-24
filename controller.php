<?php
namespace Concrete\Package\VividStore;
use Package;

use \Concrete\Package\VividStore\Src\VividStore\Payment\Method as PaymentMethod;
use \Concrete\Package\VividStore\Src\VividStore\Utilities\Installer;
use Route;

defined('C5_EXECUTE') or die(_("Access Denied."));

class Controller extends Package
{
    protected $pkgHandle = 'vivid_store';
    protected $appVersionRequired = '5.7.3';
    protected $pkgVersion = '2.3.3';
    protected $pkgAutoloaderRegistries = array(
        'src/AuthorizeNet' => '\AuthorizeNet',
        'src/Omnipay' => '\Omnipay'
    );
    public function getPackageDescription()
    {
        return t("Add a Store to your Site");
    }

    public function getPackageName()
    {
        return t("Vivid Store");
    }
	
	public function installStore()
	{
		$pkg = Package::getByHandle('vivid_store');
		if (version_compare($pkg->getPackageVersion(), '2.1', '<')) {
            Installer::renameDatabaseTables($pkg);
        }
		if (version_compare(APP_VERSION, '5.7.4', '<')) {
			Installer::refreshDatabase($pkg);
		}
		Installer::installSinglePages($pkg);
		Installer::installProductParentPage($pkg);
        Installer::installStoreProductPageType($pkg);
		Installer::updateConfigStorage($pkg);
		Installer::setDefaultConfigValues($pkg);
		Installer::installPaymentMethods($pkg);
        Installer::installBlocks($pkg);
		Installer::setPageTypeDefaults($pkg);
		Installer::installCustomerGroups($pkg);
		Installer::installUserAttributes($pkg);
		Installer::installOrderAttributes($pkg);
		Installer::installProductAttributes($pkg);
		Installer::createDDFileset($pkg);
		Installer::installOrderStatuses($pkg);
	}

    public function install()
    {
        $pkg = parent::install();
		$this->installStore();
    }

    public function upgrade()
    {
        parent::upgrade();
		$this->installStore();		
    }

    

    public function registerRoutes()
    {
        Route::register('/cart/getSubTotal', '\Concrete\Package\VividStore\Src\VividStore\Cart\CartTotal::getSubTotal');
        Route::register('/cart/getTaxTotal', '\Concrete\Package\VividStore\Src\VividStore\Cart\CartTotal::getTaxTotal');
        Route::register('/cart/getTotal', '\Concrete\Package\VividStore\Src\VividStore\Cart\CartTotal::getTotal');
        Route::register('/cart/getTotalItems', '\Concrete\Package\VividStore\Src\VividStore\Cart\CartTotal::getTotalItems');
        Route::register('/cart/getmodal', '\Concrete\Package\VividStore\Src\VividStore\Cart\CartModal::getCartModal');
        Route::register('/productmodal', '\Concrete\Package\VividStore\Src\VividStore\Product\ProductModal::getProductModal');
        Route::register('/checkout/getstates', '\Concrete\Package\VividStore\Src\VividStore\Utilities\States::getStateList');
        Route::register('/checkout/updater','\Concrete\Package\VividStore\Src\VividStore\Utilities\Checkout::updater');
        Route::register('/productfinder','\Concrete\Package\VividStore\Src\VividStore\Utilities\ProductFinder::getProductMatch');
        Route::register('/checkout/paypalresponse','\Concrete\Package\VividStore\Src\VividStore\Payment\Methods\PaypalStandard\PaypalStandardPaymentMethod::validateCompletion');
    }
    public function on_start()
    {
        $this->registerRoutes();
    }
    public function uninstall()
    {
        $authnetpm = PaymentMethod::getByHandle('auth_net');
        if(is_object($authnetpm)){
            $authnetpm->delete();
        }
        $invoicepm = PaymentMethod::getByHandle('invoice');
        if(is_object($invoicepm)){
            $invoicepm->delete();
        }
		$paypalpm = PaymentMethod::getByHandle('paypal_standard');
        if(is_object($paypalpm)){
            $paypalpm->delete();
        }
        parent::uninstall();
    }


}
?>
