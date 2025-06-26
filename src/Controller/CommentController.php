<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CommentController extends AbstractController
{
    #[Route('/comment/approve/{id}', name: 'comment_approve')]
    #[IsGranted('ROLE_ADMIN')]
    public function approve(Comment $comment, EntityManagerInterface $em): Response
    {
        $comment->setIsApproved(true);
        $em->flush();
        return $this->redirectToRoute('post_show', ['slug' => $comment->getPost()->getSlug()]);
    }

    #[Route('/comment/delete/{id}', name: 'comment_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id, EntityManagerInterface $em): Response
    {
        $comment = $em->getRepository(Comment::class)->find($id);
        if (!$comment) {
            throw $this->createNotFoundException('Comment not found');
        }
        $this->denyAccessUnlessGranted('DELETE', $comment);
        $postSlug = $comment->getPost()->getSlug();
        $em->remove($comment);
        $em->flush();
        return $this->redirectToRoute('post_show', ['slug' => $postSlug]);
    }

    #[Route('/comment/add/{postSlug}', name: 'comment_add')]
    #[IsGranted('ROLE_USER')]
    public function add(Request $request, string $postSlug, EntityManagerInterface $em): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setCreatedAt(new \DateTimeImmutable());
            // Odszukaj post po slug
            $post = $em->getRepository(Post::class)->findOneBy(['slug' => $postSlug]);
            $comment->setPost($post);
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute('post_show', ['slug' => $postSlug]);
        }
        return $this->render('comment/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
