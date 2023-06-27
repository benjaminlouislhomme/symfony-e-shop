<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Product;
use App\Form\ProductType;
use App\Form\FindProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;

class IndexController extends AbstractController
{
  #[Route('/', name: 'app_index', methods: ['GET'])]
  public function index(
    ProductRepository $productRepository,
    CategoryRepository $categoryRepository,
    Request $request
  ): Response {

    $categories = $categoryRepository->findBy([], ['name' => 'ASC']);

    $category = $categoryRepository->findBy([
      'name' => $request->get('cat')
    ]);

    $products = $productRepository->findBy(
      $category ? ['category' => $category] : [],
    );

    return $this->render('index/index.html.twig', [
      'products' => $products,
      'categories' => $categories,
    ]);
  }
}
