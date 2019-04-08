<?php

namespace App\Controller;

use App\Entity\Article;

use App\Entity\Commentaire;
use App\Form\ArticleType;
use App\Form\CommentaireType;
use App\Repository\ArticleRepository;
use App\Repository\CommentaireRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;



class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        $image = $articleRepository->findAll();

        $dossier_image = $this->getParameter('uploads_directory');

        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),

        ]);
    }

    /**
     * @Route("/admin/new", name="article_new", methods={"GET","POST"})
     */
    public function new(Request $request, ArticleRepository $articleRepository): Response
    {


        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        $dossier_image = $this->getParameter('uploads_directory');


        if ($form->isSubmitted() && $form->isValid()) {

            $file = $request->files->get('article')['image'];
            if ($file) {
                $new_image = md5(uniqid()) . '.' . $file->guessExtension();
                /*$new_image = $this->generateUniqueFileName().'.'.$file->guessExtension();*/
                $file->move(
                    $dossier_image,
                    $new_image

                );
                $article->setImage($new_image);
            }
            $article = $form->getData();


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/article/{id}", name="article_show", methods={"GET","POST"})
     */
    public function show(Article $article, Request $request,ObjectManager $Manager): Response
    {
        $commentaire = new commentaire();
        $user = $this->getUser();


        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire->setDate(new\DateTime())
                ->setArticle($article);
            $commentaire->setUser($user);
            $Manager->persist($commentaire);
            $Manager->flush();



            /*$commentaire->setUser($this->getUser());
            $commentaire->setArticle($this->getArticle());
            $this->getDoctrine()->getManager()->flush();*/

            return $this->redirectToRoute('article_show',[
                'id' => $article->getId()]);

        }





        return $this->render('article/show.html.twig', [
            'article' => $article,
             'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/admin/{id}/edit", name="article_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        $dossier_image = $this->getParameter('uploads_directory');

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $request->files->get('article')['image'];
            if ($file) {
                $new_image = md5(uniqid()) . '.' . $file->guessExtension();
                /*$new_image = $this->generateUniqueFileName().'.'.$file->guessExtension();*/
                $file->move(
                    $dossier_image,
                    $new_image

                );
                $article->setImage($new_image);
            }
            $article = $form->getData();

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('article_index', [
                'id' => $article->getId(),
            ]);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Commentaire $commentaire
     * @return mixed
     * @Route("admin/{id}/Delete" , name="com_delete",methods={"DELETE"})
     */
    public function deleteCom(Request $request,Commentaire $commentaire,$id){
        if ($this->isCsrfTokenValid('delete' . $commentaire->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($commentaire);
            $entityManager->flush();
        }
        return $this->redirectToRoute('article_index');
    }

    /**
     * @Route("/admin/delete/{id}", name="article_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($article);
                $entityManager->flush();
        }

        return $this->redirectToRoute('article_index');
    }
}
