<?php

namespace App\Controller;

use ApiPlatform\Metadata\Post;
use App\Entity\User;
use App\State\UserStateProcessor;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[Route('api/users')]
class UserController extends AbstractController
{
    public function __construct()
    {
    }

//    #[Route(
//        path: '/host',
//        name: 'users_host',
//        defaults: [
//            '_api_resource_class' => User::class,
////            '_api_operation_name' => '_api_/users/users_host',
//        ],
//        methods: ['POST'],
//    )]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'fabien' => 'fabien est bien dans la place',
            'path' => 'src/Controller/UserController.php',
        ]);
    }
}
