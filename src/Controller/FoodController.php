<?php

namespace App\Controller;

use App\BLL\FoodBLL;
use App\Entity\Food;
use App\Form\FoodType;
use App\Repository\FoodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/food')]
final class FoodController extends AbstractController
{
    #[Route('/', name: 'app_food_index', methods: ['GET'])]
    #[Route('/orden/{ordenacion}', name: 'app_food_index_ordenado', methods: ['GET'])]
    public function index(FoodBLL $foodBLL, string $ordenacion = null): Response
    {
        $food = $foodBLL->getFoodConOrdenacion($ordenacion);
        return $this->render('food/index.html.twig', [
            'food' => $food
        ]);
    }

    #[Route('/busqueda', name: 'app_food_index_busqueda', methods: ['POST'])]
    public function busqueda(Request $request, FoodRepository $foodRepository): Response
    {
        $fechaInicial = $request->request->get('fechaInicial');
        $fechaFinal = $request->request->get('fechaFinal');
        $busqueda = $request->request->get('busqueda');
        $food = $foodRepository->findFood($busqueda, $fechaInicial, $fechaFinal);
        return $this->render('food/index.html.twig', [
            'food' => $food,
            'busqueda' => $busqueda,
            'fechaInicial' => $fechaInicial,
            'fechaFinal' => $fechaFinal
        ]);
    }

    #[Route('/new', name: 'app_food_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $food = new Food();
        $form = $this->createForm(FoodType::class, $food);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $file almacena el archivo subido
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $form['imagen']->getData();
            // Generamos un nombre único
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            // Move the file to the directory where brochures are stored
            $file->move($this->getParameter('images_directory_food'), $fileName);
            // Actualizamos el nombre del archivo en el objeto imagen al nuevo generado
            $food->setImagen($fileName);
            $food->setFecha(new \DateTimeImmutable('today'));
            $entityManager->persist($food);
            $entityManager->flush();

            $this->addFlash('mensaje', 'Se ha creado la imagen ' . $food->getImagen());

            return $this->redirectToRoute('app_food_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('food/new.html.twig', [
            'food' => $food,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_food_show', methods: ['GET'])]
    public function show(Food $food): Response
    {
        return $this->render('food/show.html.twig', [
            'food' => $food,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_food_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Food $food, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FoodType::class, $food);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_food_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('food/edit.html.twig', [
            'food' => $food,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_food_delete', methods: ['POST'])]
    public function delete(Request $request, Food $food, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $food->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($food);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_food_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_food_delete_json', methods: ['DELETE'])]
    public function deleteJson(Food $food, FoodRepository $foodRepository): Response
    {
        $foodRepository->remove($food, true);
        return new JsonResponse(['eliminado' => true]);
    }
}
