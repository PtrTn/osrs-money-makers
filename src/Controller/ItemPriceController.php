<?php

namespace App\Controller;

use App\Repository\ItemPriceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ItemPriceController extends AbstractController
{
    #[Route('/', name: 'app_items')]
    public function index(ItemPriceRepository $repository): Response
    {
        $items = $repository->findAll();

        return $this->render('items/index.html.twig', [
            'items' => $items,
        ]);
    }
}
