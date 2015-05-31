<?php

namespace AppBundle\Controller;

use AppBundle\Document\Book;
use AppBundle\Document\Order;
use DateInterval;
use DateTime;
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
        return new JsonResponse($books ?: []);
    }

    /**
     * @Route("/order/new", name="order_new")
     * @param Request $request
     * @return Response
     */
    public function addOrderAction(Request $request){

        $book     = $request->request->get('book',false);
        $quantity = $request->request->get('quantity',false);
        $cliName  = $request->request->get('clientName',false);
        $address  = $request->request->get('address',false);
        $email    = $request->request->get('email',false);


        if(!$book){
            return new Response('Parameter "book    " missing',400);
        }elseif(!$quantity || $quantity <= 0){
            return new Response('Parameter "quantity" missing or <= 0',400);
        }elseif(!$cliName){
            return new Response('Parameter "cliName " missing',400);
        }elseif(!$address){
            return new Response('Parameter "address " missing',400);
        }elseif(!$email){
            return new Response('Parameter "email   " missing',400);
        }

        $book = $this->get('doctrine_mongodb')->getRepository('AppBundle:Book')->find(['id' => $book]);

        if(!$book){
            return new Response("Book $book not found",400);
        }

        $dm = $this->get('doctrine_mongodb')->getManager();
        $avQuantity = $book->stock;
        if ($avQuantity > $quantity){
            $book->stock -= $quantity;
            $date = $date = new DateTime(time());
            $date->add(new DateInterval('P1D'));

            $order = new Order();
            $order->address = $address;
            $order->clientName = $cliName;
            $order->email = $email;
            $order->title = $book;
            $order->state = Order::DISPATCHED;
            $order->dispatchingTime = $date;
            $dm->persist($book);
        }else{
            //Enviar email
        }

        $dm->flush();
        return new JsonResponse($order);
    }

}
