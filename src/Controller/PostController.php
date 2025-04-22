<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class PostController extends AbstractController
{
    #[Route('/post', name: 'post_index')]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/post/new', name: 'post_new')]
    #[IsGranted('ROLE_AUTHOR')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setAuthor($this->getUser());
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('post_index');
        }
        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/post/{slug}', name: 'post_show')]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/post/{slug}/edit', name: 'post_edit')]
    #[IsGranted('ROLE_AUTHOR')]
    public function edit(Request $request, Post $post, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $post);
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('post_show', ['slug' => $post->getSlug()]);
        }
        return $this->render('post/edit.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
        ]);
    }

    #[Route('/post/{slug}/delete', name: 'post_delete', methods: ['POST'])]
    #[IsGranted('ROLE_AUTHOR')]
    public function delete(Request $request, Post $post, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('DELETE', $post);
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $em->remove($post);
            $em->flush();
        }
        return $this->redirectToRoute('post_index');
    }
}
