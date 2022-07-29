<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    #[Route('/article/{id}', name: 'article_show')]
    public function index(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('admin/article/ajouter', name: 'article_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$article` variable has also been updated
            $article = $form->getData();

            $em->persist($article);
            $em->flush();
            // ... perform some action, such as saving the article to the database
            $this->addFlash(
                'success',
                'Votre catégory a bien été ajouté !'
            );

            return $this->redirectToRoute('admin_articles');
        }

        return $this->renderForm('article/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/admin/article/supprimer/{id}', name: 'article_delete')]
    public function delete(Article $article, EntityManagerInterface $em): Response
    {
        $em->remove($article);
        $em->flush();

        $this->addFlash(
            'success',
            'Votre article a bien été supprimé !'
        );

        return $this->redirectToRoute('admin_articles');
    }
}
