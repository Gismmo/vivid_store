<?php
namespace Concrete\Package\VividStore\Src\VividStore\Order;

use Database;

/**
 * @Entity
 * @Table(name="VividStoreOrderItemDiscounts")
 */
class OrderItemDiscount
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $oidID;

    /**
     * @ManyToOne(targetEntity="Concrete\Package\VividStore\Src\VividStore\Order\OrderItem")
     * @JoinColumn(name="oiID", referencedColumnName="oiID")
     */
    protected $orderItem;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $oidName;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $oidDisplay;

    /**
     * @Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $oidValue;

    /**
     * @Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $oidPercentage;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $oidDeductFrom;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $oidCode;

    public function save()
    {
        $em = \Database::connection()->getEntityManager();
        $em->persist($this);
        $em->flush();
    }

    public function delete()
    {
        $em = \Database::connection()->getEntityManager();
        $em->remove($this);
        $em->flush();
    }
}