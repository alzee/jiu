<?php
/**
 * vim:ft=php et ts=4 sts=4
 * @version
 * @todo
 */

namespace App\EventListener;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Retail;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Entity\Voucher;
use App\Entity\Org;
use App\Entity\Choice;

class RetailNew extends AbstractController
{
    public function postPersist(Retail $retail, LifecycleEventArgs $event): void
    {
        $em = $event->getEntityManager();

        $product = $retail->getProduct();
        $quantity = $retail->getQuantity();
        $amount = $quantity * $product->getPrice();
        $voucher = $quantity * $product->getVoucher();

        $retail->setAmount($amount);
        $retail->setVoucher($voucher);

        // store stock - quantity
        $product->setStock($product->getStock() - $quantity);

        // consumer + voucher
        $consumer = $retail->getConsumer();
        $consumer->setVoucher($consumer->getVoucher() + $voucher);

        // store - voucher
        $store = $retail->getStore();
        $store->setVoucher($store->getVoucher() - $voucher);

        // voucher record for store
        $record = new Voucher();
        $record->setOrg($store);
        $record->setVoucher(-$voucher);
        $type = Choice::VOUCHER_TYPES['酒零售'];
        $record->setType($type);
        $em->persist($record);

        // voucher record for consumer
        $record = new Voucher();
        $consumers = $em->getRepository(Org::class)->findOneByType(4);
        $record->setOrg($consumers);
        $record->setConsumer($consumer);
        $record->setVoucher($voucher);
        $record->setType($type - 100);
        $em->persist($record);

        $em->flush();
    }
}
