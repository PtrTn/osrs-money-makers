<?php

namespace App\Controller;

use App\Repository\ItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ItemsController extends AbstractController
{
    #[Route('/', name: 'app_items')]
    public function index(ItemRepository $repository): Response
    {
        $items = $repository->findAllWithPrice();

        return $this->render('items/index.html.twig', [
            'items' => $items,
        ]);
    }
}
