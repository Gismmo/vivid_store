<?php 
namespace Concrete\Package\VividStore\Src\VividStore\Product;

use Database;

/**
 * @Entity
 * @Table(name="VividStoreProductImages")
 */
class Image
{
    
    /** 
     * @Id @Column(type="integer") 
     * @GeneratedValue 
     */
    protected $piID;
    
    /**
     * @Column(type="integer")
     */
    protected $pID; 
    
    /**
     * @Column(type="integer")
     */
    protected $pifID; 
    
    /**
     * @Column(type="integer")
     */
    protected $piSort; 
    
    private function setProductID($pID){ $this->pID = $pID; }
    private function setFileID($pifID){ $this->pifID = $pifID; }
    private function setSort($piSort){ $this->piSort = $piSort; }
    
    public function getID(){ return $this->piID; }
    public function getProductID() { return $this->pID; }
    public function getFileID() { return $this->pifID; }
    public function getSort() { return $this->piSort; }
    
    public static function getByID($piID) {
        $db = Database::connection();
        $em = $db->getEntityManager();
        return $em->find('Concrete\Package\VividStore\Src\VividStore\Product\Image', $piID);
    }
    
    public static function getImagesForProduct(\Concrete\Package\VividStore\Src\VividStore\Product\Product $product)
    {
        $db = Database::connection();
        $em = $db->getEntityManager();
        return $em->getRepository('Concrete\Package\VividStore\Src\VividStore\Product\Image')->findBy(array('pID' => $product->getProductID()));
    }
    
    public static function addImagesForProduct(array $images, \Concrete\Package\VividStore\Src\VividStore\Product\Product $product)
    {
        //clear out existing images
        $existingImages = self::getImagesForProduct($product);
        foreach($existingImages as $img){
            $img->delete();
        }
        
        //add new ones.
        for($i=0;$i<count($images['pifID']);$i++){
            self::add($product->getProductID(),$images['pifID'][$i],$images['piSort'][$i]);
            $vals = array($pID,$data['pifID'][$i],$data['piSort'][$i]);
        }
    }
    
    public static function add($pID,$pifID,$piSort)
    {
        $productImage = new self();
        $productImage->setProductID($pID);
        $productImage->setFileID($pifID);
        $productImage->setSort($piSort);
        $productImage->save();
        return $productImage;
    }
    
    public function save()
    {
        $em = Database::connection()->getEntityManager();
        $em->persist($this);
        $em->flush();
    }
    
    public function delete()
    {
        $em = Database::connection()->getEntityManager();
        $em->remove($this);
        $em->flush();
    }
    
}
