<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Org;
use App\Entity\Product;
use App\Entity\Orders;
use App\Entity\OrderItems;
use App\Entity\Returns;
use App\Entity\ReturnItems;
use App\Entity\Retail;
use App\Entity\OrderRestaurant;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/api')]
class ApiController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/order/new', methods: ['POST'])]
    public function orderNew(Request $request): JsonResponse
    {
        $params  = $request->toArray();
        $seller = $this->doctrine->getRepository(Org::class)->find($params['sellerid']);
        $buyer = $this->doctrine->getRepository(Org::class)->find($params['buyerid']);
        $product = $this->doctrine->getRepository(Product::class)->find($params['product']);
        $quantity = $params['quantity'];
        $em = $this->doctrine->getManager();

        $item = new OrderItems();
        $item->setProduct($product);
        $item->setQuantity($quantity);
        $em->persist($item);
        $em->flush();

        $order = new Orders();
        $order->setSeller($seller);
        $order->setBuyer($buyer);
        $order->setNote($params['note']);
        $order->addOrderItem($item);

        $em->persist($order);
        $em->flush();
        
        $item->setOrd($order);

        $em->flush();

        return $this->json([
            'code' => 0,
        ]);
    }

    #[Route('/return/new', methods: ['POST'])]
    public function returnNew(Request $request): JsonResponse
    {
        $params  = $request->toArray();
        $sender = $this->doctrine->getRepository(Org::class)->find($params['senderid']);
        $recipient = $this->doctrine->getRepository(Org::class)->find($params['recipientid']);
        $product = $this->doctrine->getRepository(Product::class)->find($params['product']);
        $quantity = $params['quantity'];
        $em = $this->doctrine->getManager();

        $item = new ReturnItems();
        $item->setProduct($product);
        $item->setQuantity($quantity);
        $em->persist($item);
        $em->flush();

        $ret = new Returns();
        $ret->setSender($sender);
        $ret->setRecipient($recipient);
        $ret->setNote($params['note']);
        $ret->addReturnItem($item);

        $em->persist($ret);
        $em->flush();

        $item->setRet($ret);

        $em->flush();

        return $this->json([
            'code' => 0,
        ]);
    }

    #[Route('/retail/new', methods: ['POST'])]
    public function retailNew(Request $request): JsonResponse
    {
        return $this->json([
            'code' => 0,
        ]);
    }

    #[Route('/dine/new', methods: ['POST'])]
    public function dineNew(Request $request): JsonResponse
    {
        return $this->json([
            'code' => 0,
        ]);
    }
}
