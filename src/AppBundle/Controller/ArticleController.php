<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Article;
use AppBundle\Form\ArticleType;

/**
 * Article controller.
 *
 * @Route("/")
 */
class ArticleController extends Controller
{
    /**
     * Lists all Article entities.
     *
     * @Route("/", name="_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $articles = $em->getRepository('AppBundle:Article')->findBy(array(),array(),4,0);
        $prev = $em->getRepository('AppBundle:Article')->findBy(array(),array(),1,4);

         if(empty($prev) ){
           $isPrev = false;
         }else{
           $isPrev = true;
         }

        return $this->render('article/index.html.twig', array(
            'articles' => $articles,
            'page' =>0,
            'isPrev' =>$isPrev,
        ));
    }

    /**
     * Lists all Article entities.
     *
     * @Route("/page/{id}", name="page")
     * @Method("GET")
     */
    public function pageAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $start = ($id*4)-1;

        $articles = $em->getRepository('AppBundle:Article')->findBy(array(),array(),4,$start);
        $prev = $em->getRepository('AppBundle:Article')->findBy(array(),array(),1,$start+4);

        if(empty($prev) ){
          $isPrev = false;
        }else{
          $isPrev = true;
        }

        return $this->render('article/index.html.twig', array(
            'articles' => $articles,
            'page' =>$id,
            'isPrev' =>$isPrev,
        ));
    }

    /**
     * Creates a new Article entity.
     *
     * @Route("/new", name="_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $article = new Article();
        $form = $this->createForm('AppBundle\Form\ArticleType', $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('_show', array('id' => $article->getId()));
        }

        return $this->render('article/new.html.twig', array(
            'article' => $article,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Article entity.
     *
     * @Route("/article/{id}", name="_show")
     * @Method("GET")
     */
    public function showAction(Article $article)
    {
        $deleteForm = $this->createDeleteForm($article);

        return $this->render('article/show.html.twig', array(
            'article' => $article,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Article entity.
     *
     * @Route("/article/{id}/edit", name="_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Article $article)
    {
        $deleteForm = $this->createDeleteForm($article);
        $editForm = $this->createForm('AppBundle\Form\ArticleType', $article);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('_edit', array('id' => $article->getId()));
        }

        return $this->render('article/edit.html.twig', array(
            'article' => $article,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Article entity.
     *
     * @Route("/article/{id}/delete", name="_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Article $article)
    {
        $form = $this->createDeleteForm($article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($article);
            $em->flush();
        }

        return $this->redirectToRoute('_index');
    }

    /**
     * Creates a form to delete a Article entity.
     *
     * @param Article $article The Article entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Article $article)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('_delete', array('id' => $article->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Find article by categories
     *
     * @Route("/categories/{cat}", name="categories")
     * @Method("GET")
     */
    public function findCat($cat)
    {
      $em = $this->getDoctrine()->getManager();

      $articles = $em->getRepository('AppBundle:Article')->findByCategorie($cat);

        $message = "Cette catégorie est vide.";


      return $this->render('article/index.html.twig', array(
          'articles' => $articles,
          'page' =>0,
          'isPrev' =>false,
          'message' => $message,
      ));
    }


    /**
     * Find article by categories
     *
     * @Route("/search", name="search")
     * @Method("post")
     */
    public function searchBox(Request $request)
    {
      $keyword = "%".$request->get('keyword')."%";
      $keywordToPrint = $request->get('keyword');
      $em = $this->getDoctrine()->getManager();
      $qb = $em->getRepository('AppBundle:Article')->createQueryBuilder('a');
      $qb->select('a')
         ->where('a.description LIKE :keyword ')
          ->setParameter('keyword', $keyword);
         $query = $qb->getQuery();
     $articles = $query->getResult();

      $message = "Aucun resultat pour ".$keywordToPrint;


      return $this->render('article/index.html.twig', array(
          'articles' => $articles,
          'page' =>0,
          'isPrev' =>false,
          'message' => $message,

      ));
    }

}
