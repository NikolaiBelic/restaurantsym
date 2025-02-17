<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
use App\Repository\FoodRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ProyectoController extends AbstractController
{
    #[Route('/', name: 'sym_index')]
    public function index(CustomerRepository $customerRepository, FoodRepository $foodRepository)
    {
        return $this->render('index.view.html.twig', [
            'customers' => $customerRepository->findAll(),
            'food' => $foodRepository->findAll()
        ]);
    }

    #[Route('/about', name: 'sym_about')]
    public function about() 
    {
        return $this->render('about.view.html.twig', [
        ]);
    }

    #[Route('/book', name: 'sym_book')]
    public function book() 
    {
        return $this->render('book.view.html.twig', [
        ]);
    }

    #[Route('/menu', name: 'sym_menu')]
    public function menu(FoodRepository $foodRepository) 
    {
        return $this->render('menu.view.html.twig', [
            'food' => $foodRepository->findAll(),
        ]);
    }
}