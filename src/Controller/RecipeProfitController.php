<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeProfitController extends AbstractController
{
    #[Route('/')]
    public function index(RecipeRepository $repository): Response
    {
        $recipes = $repository->findAll();

        uasort($recipes, fn(Recipe $recipeA, Recipe $recipeB) => $recipeB->getAvgProfit() <=> $recipeA->getAvgProfit());

        return $this->render('index.html.twig', [
            'recipes' => $recipes,
        ]);
    }
}
