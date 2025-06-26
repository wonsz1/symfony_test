<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\DTO\PostDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Enumerations\PostStatus;
use App\Service\AppMetrics;

class PostController extends AbstractController
{
    public function __construct(private AppMetrics $appMetrics)
    {
    }

    #[Route('/post', name: 'post_index')]
    public function index(PostRepository $postRepository): Response
    {
        $this->appMetrics->incrementCall('post_index', 'GET');
        $posts = $postRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/post/new', name: 'post_new')]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $postDto = new PostDTO();
        $form = $this->createForm(PostType::class, $postDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $postDto->setAuthor($this->getUser());
            // Generate slug from title
            $postDto->setSlug($this->slugify($postDto->getTitle()));
            // Set publishedAt only if status is 'published'
            if ($postDto->getStatus() === PostStatus::PUBLISHED) {
                $postDto->setPublishedAt(new \DateTimeImmutable());
            } else {
                $postDto->setPublishedAt(null);
            }
            $post = $postDto->toEntity();
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('post_index');
        }
        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function slugify(string $text): string
    {
        // Replace non letter or digits by -
        $text = preg_replace('~[\s\W]+~', '-', $text);
        // Trim
        $text = trim($text, '-');
        // Lowercase
        $text = strtolower($text);
        return $text ?: 'n-a';
    }

    #[Route('/post/{slug}', name: 'post_show')]
    public function show(string $slug, PostRepository $postRepository): Response
    {
        $this->appMetrics->incrementCall('post_show', 'GET');
        $post = $postRepository->findOneBy(['slug' => $slug]);
        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/post/{slug}/edit', name: 'post_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, string $slug, EntityManagerInterface $em): Response
    {
        if (!$this->getUser()) {
            throw new \Exception('User not authenticated');
        }
        $post = $em->getRepository(Post::class)->findOneBy(['slug' => $slug]);
        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }
        $this->denyAccessUnlessGranted('EDIT', $post);
        $postDto = PostDTO::fromEntity($post);
        $form = $this->createForm(PostType::class, $postDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post = $postDto->toEntity($post);
            // Set publishedAt only if status is 'published'
            if ($post->getStatus() === PostStatus::PUBLISHED->value && !$post->getPublishedAt()) {
                $post->setPublishedAt(new \DateTimeImmutable());
            } elseif ($post->getStatus() !== PostStatus::PUBLISHED->value) {
                $post->setPublishedAt(null);
            }
            $em->flush();
            return $this->redirectToRoute('post_show', ['slug' => $post->getSlug()]);
        }
        return $this->render('post/edit.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
        ]);
    }

    #[Route('/post/{slug}/delete', name: 'post_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, string $slug, EntityManagerInterface $em): Response
    {
        $post = $em->getRepository(Post::class)->findOneBy(['slug' => $slug]);
        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }
        $this->denyAccessUnlessGranted('DELETE', $post);
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $em->remove($post);
            $em->flush();
        }
        return $this->redirectToRoute('post_index');
    }
}
