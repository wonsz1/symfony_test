<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\DTO\CategoryDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'category_index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category/new', name: 'category_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $categoryDto = new CategoryDTO();
        $form = $this->createForm(CategoryType::class, $categoryDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $categoryDto->toEntity();
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('category_index');
        }
        return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/category/{slug}', name: 'category_show')]
    public function show(string $slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);
        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/category/{slug}/edit', name: 'category_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, string $slug, EntityManagerInterface $em): Response
    {
        $category = $em->getRepository(Category::class)->findOneBy(['slug' => $slug]);
        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }
        
        $categoryDto = CategoryDTO::fromEntity($category);
        $form = $this->createForm(CategoryType::class, $categoryDto);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Validate slug if it was changed
            if (!$this->validateSlug($form, $categoryDto->getSlug(), $category, $em)) {
                return $this->render('category/edit.html.twig', [
                    'form' => $form->createView(),
                    'category' => $category,
                ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200));
            }
            
            $categoryDto->toEntity($category);
            $em->flush();
            return $this->redirectToRoute('category_show', ['slug' => $category->getSlug()]);
        }
        
        return $this->render('category/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    private function validateSlug(
        \Symfony\Component\Form\FormInterface &$form,
        string $newSlug,
        Category $category,
        EntityManagerInterface $em
    ): bool {
        // Only validate if the slug was changed
        if ($newSlug === $category->getSlug()) {
            return true;
        }

        // Validate slug format
        if (!preg_match('/^[a-z0-9-]+$/', $newSlug)) {
            $form->addError(new FormError('Slug can only contain lowercase letters, numbers, and hyphens'));
            return false;
        }

        // Check if slug is already used by another category
        $existingCategory = $em->getRepository(Category::class)
            ->findOneBy(['slug' => $newSlug]);
        
        if ($existingCategory && $existingCategory->getId() !== $category->getId()) {
            $form->addError(new FormError('This slug is already in use'));
            return false;
        }

        return true;
    }

    #[Route('/category/{slug}/delete', name: 'category_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, string $slug, EntityManagerInterface $em): Response
    {
        $category = $em->getRepository(Category::class)->findOneBy(['slug' => $slug]);
        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $em->remove($category);
            $em->flush();
        }
        return $this->redirectToRoute('category_index');
    }
}
