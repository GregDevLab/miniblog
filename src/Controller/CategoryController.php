<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'category_index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/admin/category/supprimer/{id}', name: 'category_delete')]
    public function delete(Category $category, EntityManagerInterface $em): Response
    {
        $em->remove($category);
        $em->flush();

        $this->addFlash(
            'success',
            'Votre catégory a bien été supprimé !'
        );

        return $this->redirectToRoute('category_index');
    }

    #[Route('/admin/category/ajouter', name: 'category_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$category` variable has also been updated
            $category = $form->getData();

            $em->persist($category);
            $em->flush();
            // ... perform some action, such as saving the category to the database
            $this->addFlash(
                'success',
                'Votre catégory a bien été ajouté !'
            );

            return $this->redirectToRoute('category_index');
        }

        return $this->renderForm('category/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/category/{id}', name: 'category_show')]
    public function show(Category $category,  PaginatorInterface $paginator, Request $request): Response
    {
        $allArticle = $category->getArticles();
        $articles = $paginator->paginate(
            $allArticle, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );
        $articles->setCustomParameters([
            'align' => 'center',
            'size' => 'medium',
            'rounded' => true
        ]);

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'articles' => $articles
        ]);
    }
}
