<?php

namespace App\BLL;

use App\Repository\FoodRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class FoodBLL
{
    private RequestStack $requestStack;
    private FoodRepository $foodRepository;
    private Security $security;

    public function __construct(RequestStack $requestStack, FoodRepository $foodRepository, Security $security)
    {
        $this->requestStack = $requestStack;
        $this->foodRepository = $foodRepository;
        $this->security = $security;
    }
    public function getFoodConOrdenacion(?string $ordenacion)
    {
        if (!is_null($ordenacion)) { // Cuando se establece un tipo de ordenación específico
            $tipoOrdenacion = 'asc';
            // Por defecto si no se había guardado antes en la variable de sesión
            $session = $this->requestStack->getSession();
            // Abrir la sesión
            $foodOrdenacion = $session->get('foodOrdenacion');
            if (!is_null($foodOrdenacion)) { // Comprobamos si ya se había establecido un orden
                if ($foodOrdenacion['ordenacion'] === $ordenacion) // Por si se ha cambiado de campo a ordenar
                {
                    if ($foodOrdenacion['tipoOrdenacion'] === 'asc')
                        $tipoOrdenacion = 'desc';
                }
            }
            $session->set('foodOrdenacion', [
                // Se guarda la ordenación actual
                'ordenacion' => $ordenacion,
                'tipoOrdenacion' => $tipoOrdenacion
            ]);
        } else {
            // La primera vez que se entra se establece por defecto la ordenación por id ascendente
            $ordenacion = 'id';
            $tipoOrdenacion = 'asc';
        }
        $usuarioLogueado = $this->security->getUser();
        return $this->foodRepository->findFoodConCategoria($ordenacion, $tipoOrdenacion, $usuarioLogueado);
    }
}
