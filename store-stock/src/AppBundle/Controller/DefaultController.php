<?php

namespace AppBundle\Controller;

use AppBundle\Document\Book;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/app/example", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }
    /**
     * @Route("/book/add", name="book_add")
     */
    public function addBookAction(Request $request){

        $photo       = $request->request->get('photo',false);
        $title       = $request->request->get('title',false);
        $description = $request->request->get('description',false);
        $stock       = $request->request->get('stock',false);

        if(!$photo){
            return new Response('Parameter "photo" missing ');
        }elseif(!$title){
            return new Response('Parameter "title" missing ');
        }elseif(!$description){
            return new Response('Parameter "description" missing ');
        }elseif(!$stock){
            return new Response('Parameter "stock" missing ');
        }

        $book = new Book();
        $book->description = $description;
        $book->title = $title;
        $book->photo = $photo;

        $this->get('doctrine_mongodb')->getManager()->persist($book);
        $this->get('doctrine_mongodb')->getManager()->flush();

        return new JsonResponse($book);
    }
    /**
     * @Route("/book/list", name="book_list")
     */
    public function listBooksAction(){
        $books = $this
            ->get('doctrine_mongodb')
            ->getRepository('AppBundle:Book')
            ->findAll();
        return new JsonResponse($books);
    }

    /**
     * @Route("/order/new", name="order_new")
     */
    public function addOrderAction(){

    }

}
