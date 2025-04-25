<?php
namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(PostRepository $postRepository): Response
    {
        // Pobierz tylko opublikowane posty, posortowane od najnowszych
        $posts = $postRepository->findBy([
            'status' => 'published'
        ], [
            'publishedAt' => 'DESC'
        ]);
        return $this->render('home/index.html.twig', [
            'posts' => $posts,
        ]);
    }
}
