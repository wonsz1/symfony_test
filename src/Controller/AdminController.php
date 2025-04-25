<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    #[Route('/admin/posts', name: 'admin_posts')]
    #[IsGranted('ROLE_ADMIN')]
    public function posts(PostRepository $postRepo): Response
    {
        $posts = $postRepo->findBy([], ['createdAt' => 'DESC']);
        return $this->render('admin/posts.html.twig', ['posts' => $posts]);
    }

    #[Route('/admin/comments', name: 'admin_comments')]
    #[IsGranted('ROLE_ADMIN')]
    public function comments(CommentRepository $commentRepo): Response
    {
        $comments = $commentRepo->findBy([], ['createdAt' => 'DESC']);
        return $this->render('admin/comments.html.twig', ['comments' => $comments]);
    }

    #[Route('/admin/users', name: 'admin_users')]
    #[IsGranted('ROLE_ADMIN')]
    public function users(UserRepository $userRepo): Response
    {
        $users = $userRepo->findBy([], ['createdAt' => 'DESC']);
        return $this->render('admin/users.html.twig', ['users' => $users]);
    }
}
