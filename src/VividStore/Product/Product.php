<?php
namespace Concrete\Package\VividStore\Src\VividStore\Product;

use Package;
use Page;
use PageType;
use PageTemplate;
use Database;
use File;
use Core;
use User;
use Config;

use \Concrete\Package\VividStore\Src\VividStore\Product\ProductImage as StoreProductImage;
use \Concrete\Package\VividStore\Src\VividStore\Product\ProductGroup as StoreProductGroup;
use \Concrete\Package\VividStore\Src\VividStore\Product\ProductUserGroup as StoreProductUserGroup;
use \Concrete\Package\VividStore\Src\VividStore\Product\ProductFile as StoreProductFile;
use \Concrete\Package\VividStore\Src\VividStore\Product\ProductLocation as StoreProductLocation;
use \Concrete\Package\VividStore\Src\VividStore\Product\ProductOption\ProductOptionGroup as StoreProductOptionGroup;
use \Concrete\Package\VividStore\Src\VividStore\Product\ProductOption\ProductOptionItem as StoreProductOptionItem;
use \Concrete\Package\VividStore\Src\Attribute\Key\StoreProductKey;
use \Concrete\Package\VividStore\Src\VividStore\Tax\TaxClass as StoreTaxClass;
use \Concrete\Package\VividStore\Src\VividStore\Utilities\Price as StorePrice;

/**
 * @Entity
 * @Table(name="VividStoreProducts")
 */
class Product 
{

    /** 
     * @Id @Column(type="integer") 
     * @GeneratedValue 
     */
    protected $pID;
    
    /**
     * @Column(type="integer",nullable=true)
     */
    protected $cID; 
    
    /**
     * @Column(type="string")
     */
    protected $pName; 
    
    /**
     * @Column(type="text",nullable=true)
     */
    protected $pDesc; 
    
    /**
     * @Column(type="text",nullable=true)
     */
    protected $pDetail; 
    
    /**
     * @Column(type="decimal", precision=10, scale=2)
     */
    protected $pPrice; 
    
    /**
     * @Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $pSalePrice; 
    
    /**
     * @Column(type="boolean")
     */
    protected $pFeatured; 
    
    /**
     * @Column(type="integer")
     */
    protected $pQty; 
    
    /**
     * @Column(type="boolean",nullable=true)
     */
    protected $pQtyUnlim;
    
    /**
     * @Column(type="boolean")
     */
    protected $pNoQty;
    
    /**
     * @Column(type="integer")
     */
    protected $pTaxClass;
    
    /**
     * @Column(type="boolean")
     */
    protected $pTaxable;
    
    /**
     * @Column(type="integer")
     */
    protected $pfID;
    
    /**
     * @Column(type="boolean")
     */
    protected $pActive;
    
    /**
     * @Column(type="datetime")
     */
    protected $pDateAdded;
    
    /**
     * @Column(type="boolean")
     */
    protected $pShippable;
    
    /**
     * @Column(type="integer")
     */
    protected $pWidth;
    
    /**
     * @Column(type="integer")
     */
    protected $pHeight;
    
    /**
     * @Column(type="integer")
     */
    protected $pLength;
    
    /**
     * @Column(type="integer")
     */
    protected $pWeight;
    
    /**
     * @Column(type="boolean")
     */
    protected $pCreateUserAccount;
    
    /**
     * @Column(type="boolean")
     */
    protected $pAutoCheckout;
    
    /**
     * @Column(type="integer")
     */
    protected $pExclusive;

    public function setCollectionID($cID){ $this->cID = $cID; }
    public function setProductName($name){ $this->pName = $name; }
    public function setProductDescription($description){ $this->pDesc = $description; }
    public function setProductDetail($detail){ $this->pDetail = $detail; }
    public function setProductPrice($price){ $this->pPrice = $price; }
    public function setProductSalePrice($price){ $this->pSalePrice = ($price != '' ? $price : null); }
    public function setIsFeatured($bool){ $this->pFeatured = $bool; }
    public function setProductQty($qty){ $this->pQty = ($qty ? $qty : 0);  }
    public function setIsUnlimited($bool){ $this->pQtyUnlim = $bool; }
    public function setAllowBackOrder($bool){ $this->pBackOrder = $bool; }
    public function setNoQty($bool){ $this->pNoQty = $bool; }
    public function setProductTaxClass($taxClass){ $this->pTaxClass = $taxClass; }
    public function setIsTaxable($bool){ $this->pTaxable = $bool; }
    public function setProductImageID($fID){ $this->pfID = $fID; }
    public function setIsActive($bool){ $this->pActive = $bool; }
    public function setProductDateAdded($date){ $this->pDateAdded = $date; }
    public function setIsShippable($bool){ $this->pShippable = $bool; }
    public function setProductWidth($width){ $this->pWidth = $width; }
    public function setProductHeight($height){ $this->pHeight = $height; }
    public function setProductLength($length){ $this->pLength = $length; }
    public function setProductWeight($weight){ $this->pWeight = $weight; }
    public function setCreatesUserAccount($bool){ $this->pCreateUserAccount = (!is_null($bool) ? $bool : false); }
    public function setAutoCheckout($bool){ $this->pAutoCheckout = (!is_null($bool) ? $bool : false) ; }
    public function setIsExclusive($bool){ $this->pExclusive = (!is_null($bool) ? $bool : false); }
    public function updateProductQty($qty)
    {
        $this->setProductQty($qty);
        $this->save();
    }

    public static function getByID($pID) {
        $db = Database::connection();
        $em = $db->getEntityManager();
        return $em->find('Concrete\Package\VividStore\Src\VividStore\Product\Product', $pID);
    }

    public static function getByCollectionID($cID)
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        return $em->getRepository('Concrete\Package\VividStore\Src\VividStore\Product\Product')->findOneBy(array('cID' => $cID));
    }

    public function saveProduct($data)
    {
        if($data['pID']){
            //if we know the pID, we're updating.
            $product = self::getByID($data['pID']);
            $product->setProductPageDescription($data['pDesc']);
        } else {
            //else, we don't know it and we're adding a new product
            $product = new self();
            $dt = Core::make('helper/date');
            $product->setProductDateAdded(new \Datetime());
        }
        $product->setProductName($data['pName']);
        $product->setProductDescription($data['pDesc']);
        $product->setProductDetail($data['pDetail']);
        $product->setProductPrice($data['pPrice']);
        $product->setProductSalePrice($data['pSalePrice']);
        $product->setIsFeatured($data['pFeatured']);
        $product->setProductQty($data['pQty']);
        $product->setIsUnlimited($data['pQtyUnlim']);
        $product->setAllowBackOrder($data['pBackOrder']);
        $product->setNoQty($data['pNoQty']);
        $product->setProductTaxClass($data['pTaxClass']);
        $product->setIsTaxable($data['pTaxable']);
        $product->setProductImageID($data['pfID']);
        $product->setIsActive($data['pActive']);
        $product->setCreatesUserAccount($data['pCreateUserAccount']);
        $product->setIsShippable($data['pShippable']);
        $product->setProductWidth($data['pWidth']);
        $product->setProductHeight($data['pHeight']);
        $product->setProductLength($data['pLength']);
        $product->setProductWeight($data['pWeight']);
        $product->setAutoCheckout($data['pAutoCheckout']);
        $product->setIsExclusive($data['pExclusive']);
        $product->save();
        if(!$data['pID']){
            $product->generatePage($data['selectPageTemplate']);
        }
        return $product;
    }

    public function getProductID(){ return $this->pID; }
    public function getProductName(){ return $this->pName; }
    public function getProductPageID() { return $this->cID; }
    public function getProductDesc(){ return $this->pDesc; }
    public function getProductDetail() { return $this->pDetail; }
    public function getProductPrice(){ return $this->pPrice; }
    public function getFormattedPrice(){ return StorePrice::format($this->pPrice); }
    public function getProductSalePrice() { return $this->pSalePrice; }
    public function getFormattedSalePrice(){ return StorePrice::format($this->pSalePrice); }
    public function getActivePrice(){
        $salePrice = $this->getProductSalePrice();
        if($salePrice != ""){
            return $salePrice;
        } else {
            return $this->getProductPrice();
        }
    }
    public function getFormattedActivePrice(){ return StorePrice::format($this->getActivePrice()); }
    public function getTaxClassID(){ return $this->pTaxClass; }
    public function getTaxClass(){ return StoreTaxClass::getByID($this->pTaxClass); }
    
    public function isTaxable(){
        if($this->pTaxable == "1"){
            return true;
        } else {
            return false;
        }

    }
    public function isFeatured(){ return $this->pFeatured; }
    public function isActive(){ return $this->pActive; }
    public function isShippable() { return $this->pShippable; }
    public function getDimensions($whl=null){
        switch($whl){
            case "w":
                return $this->pWidth;
                break;
            case "h":
                return $this->pHeight;
                break;
            case "l":
                return $this->pLength;
                break;
            default:
                return $this->pLength."x".$this->pWidth."x".$this->pHeight;
                break;
        }
    }
    public function getProductWeight(){ return $this->pWeight; }
    public function getProductImageID() { return $this->pfID; }
    public function getProductImageObj(){
        if($this->pfID){
            $fileObj = File::getByID($this->pfID);
            return $fileObj;
        }
    }
    public function hasDigitalDownload() { return count($this->getProductDownloadFiles()) > 0 ? true : false; }
    public function getProductDownloadFiles(){ return StoreProductFile::getFilesForProduct($this); }
    public function getProductDownloadFileObjects(){ return StoreProductFile::getFileObjectsForProduct($this); }
    public function createsLogin(){ return (bool)$this->pCreateUserAccount; }
    public function allowQuantity() { return !(bool)$this->pNoQty; }
    public function isExclusive() { return (bool)$this->pExclusive; }
    public function isUnlimited() { return (bool)$this->pQtyUnlim; }
    public function autoCheckout() { return (bool)$this->pAutoCheckout; }
    public function allowBackOrders() { return (bool)$this->pBackOrder; }
    public function hasUserGroups(){ return count($this->getProductUserGroups()) > 0 ? true : false; }
    public function getProductUserGroups(){ return StoreProductUserGroup::getUserGroupsForProduct($this); }
    public function getProductUserGroupIDs(){ return StoreProductUserGroup::getUserGroupIDsForProduct($this); }
    public function getProductImage(){
        $fileObj = $this->getProductImageObj();
        if(is_object($fileObj)){
            return "<img src='".$fileObj->getRelativePath()."'>";
        }
    }
    public function getProductImageThumb(){
        $fileObj = $this->getProductImageObj();
        if(is_object($fileObj)){
            return "<img src='".$fileObj->getThumbnailURL('file_manager_listing')."'>";
        }
    }
    public function getProductQty(){ return $this->pQty; }
    
    public function isSellable()
    {
        if($this->getProductQty() > 0 || $this->isUnlimited()){
            return true;
        } else {
            if($this->allowBackOrders()){
                return true;
            } else {
                return false;
            }
        }
    }
    
    public function getProductImages() { return StoreProductImage::getImagesForProduct($this); }
    public function getproductimagesobjects() { return StoreProductImage::getImageObjectsForProduct($this); }
    public function getProductLocationPages() { return StoreProductLocation::getLocationsForProduct($this); }

    public function getProductOptionGroups() { return StoreProductOptionGroup::getOptionGroupsForProduct($this); }
    public function getProductOptionItems() { return StoreProductOptionItem::getOptionItemsForProduct($this); }

    public function getProductGroupIDs() { return StoreProductGroup::getGroupIDsForProduct($this); }
    public function getProductGroups() { return StoreProductGroup::getGroupsForProduct($this); }

    public function save()
    {
        $em = Database::get()->getEntityManager();
        $em->persist($this);
        $em->flush();
    }
    
    public function remove()
    {
        StoreProductImage::removeImagesForProduct($this);
        StoreProductOptionGroup::removeOptionGroupsForProduct($this);
        StoreProductOptionItem::removeOptionItemsForProduct($this);
        StoreProductFile::removeFilesForProduct($this);
        StoreProductGroup::removeGroupsForProduct($this);
        StoreProductLocation::removeLocationsForProduct($this);
        StoreProductUserGroup::removeUserGroupsForProduct($this);
        $em = Database::get()->getEntityManager();
        $em->remove($this);
        $em->flush();
        $page = Page::getByID($this->cID);
        if(is_object($page)){
            $page->delete();
        }
    }
    
    public function generatePage($templateID=null){
        $pkg = Package::getByHandle('vivid_store');
        $targetCID = Config::get('vividstore.productPublishTarget');
        $parentPage = Page::getByID($targetCID);
        $pageType = PageType::getByHandle('store_product');
        $pageTemplate = $pageType->getPageTypeDefaultPageTemplateObject();
        if($templateID){
            $pt = PageTemplate::getByID($templateID);
            if(is_object($pt)){
                $pageTemplate = $pt;
            }
        }
        $productParentPage = $parentPage->add(
            $pageType,
            array(
                'cName' => $this->getProductName(),
                'pkgID' => $pkg->pkgID
            ),
            $pageTemplate
        );
        $productParentPage->setAttribute('exclude_nav', 1);

        $this->setProductPageID($productParentPage->getCollectionID());
        $this->setProductPageDescription($this->getProductDesc());
    }
    public function setProductPageDescription($newDescription)
    {
        $productDescription = strip_tags(trim($this->getProductDesc()));
        $pageID = $this->getProductPageID();
        if ($pageID) {
            $productPage = Page::getByID($pageID);
            if (is_object($productPage)) {
                $pageDescription = trim($productPage->getAttribute('meta_description'));
                // if it's the same as the current product description, it hasn't been updated independently of the product
                if ($pageDescription == '' || $productDescription == $pageDescription) {
                    $productPage->setAttribute('meta_description', strip_tags($newDescription));
                }
            }
        }
    }
    public function setProductPageID($cID)
    {
        $this->setCollectionID($cID);
        $this->save();
    }
    
    

    /* TO-DO
     * This isn't completely accurate as an order status may be incomplete and never change,
     * or an order may be canceled. So at somepoint, circle back to this to check for certain status's
     */
    public function getTotalSold()
    {
        $db = Database::get();
        $results = $db->GetAll("SELECT * FROM VividStoreOrderItems WHERE pID = ?",$this->pID);
        return count($results);
    }

    public function setAttribute($ak, $value)
    {
        if (!is_object($ak)) {
            $ak = StoreProductKey::getByHandle($ak);
        }
        $ak->setAttribute($this, $value);
    }
    public function getAttribute($ak, $displayMode = false) {
        if (!is_object($ak)) {
            $ak = StoreProductKey::getByHandle($ak);
        }
        if (is_object($ak)) {
            $av = $this->getAttributeValueObject($ak);
            if (is_object($av)) {
                return $av->getValue($displayMode);
            }
        }
    }
    public function getAttributeValueObject($ak, $createIfNotFound = false) {
        $db = Database::get();
        $av = false;
        $v = array($this->getProductID(), $ak->getAttributeKeyID());
        $avID = $db->GetOne("SELECT avID FROM VividStoreProductAttributeValues WHERE pID=? AND akID=?", $v);
        if ($avID > 0) {
            $av = StoreProductValue::getByID($avID);
            if (is_object($av)) {
                $av->setProduct($this);
                $av->setAttributeKey($ak);
            }
        }

        if ($createIfNotFound) {
            $cnt = 0;

            // Is this avID in use ?
            if (is_object($av)) {
                $cnt = $db->GetOne("SELECT COUNT(avID) FROM VividStoreProductAttributeValues WHERE avID=?", $av->getAttributeValueID());
            }

            if ((!is_object($av)) || ($cnt > 1)) {
                $av = $ak->addAttributeValue();
            }
        }

        return $av;
    }

}
