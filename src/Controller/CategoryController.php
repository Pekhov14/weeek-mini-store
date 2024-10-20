<?php

namespace App\Controller;

use App\Service\CategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    public function __construct(private readonly CategoryService $categoryService)
    {
    }

    #[Route('/api/categories/tree', name: 'app_category_tree', methods: ['GET'])]
    public function getCategoryTree(): JsonResponse
    {
        $categoryTree = $this->categoryService->getCategoryTree();
        return $this->json($categoryTree);
    }
}