<?php

namespace AppBundle\Controller;

use AppBundle\Document\Book;
use AppBundle\Document\Order;
use AppBundle\Document\QueueBook;
use AppBundle\Helper\Email;
use DateInterval;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\Client;
use Mandrill_Error;
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
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function addBookAction(Request $request){

        $photo       = $request->request->get('photo',false);
        $title       = $request->request->get('title',false);
        $description = $request->request->get('description',false);
        $stock       = $request->request->get('stock',false);
        $price       = $request->request->get('price', 1);

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
        $book->stock = $stock;
        $book->price = $price;

        $this->get('doctrine_mongodb')->getManager()->persist($book);
        $this->get('doctrine_mongodb')->getManager()->flush();

        return new JsonResponse($book);
    }

    /**
     * @Route("/book/edit", name="book_edit")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function editBookAction(Request $request){

        $photo       = $request->request->get('photo',false);
        $title       = $request->request->get('title',false);
        $description = $request->request->get('description',false);
        $stock       = $request->request->get('stock',false);


        $book = $this->get('doctrine_mongodb')->getRepository('AppBundle:Book')->findOneBy(['title' => $title]);

        if(!$book){
            return new Response("Book $book not found",400);
        }

        $book->description = $description ?: $book->description;
        $book->title = $title ?: $book->title;
        $book->photo = $photo ?: $book->photo;
        $book->stock = $stock ?: $book->stock;

        $this->get('doctrine_mongodb')->getManager()->persist($book);
        $this->get('doctrine_mongodb')->getManager()->flush();

        return new JsonResponse($book);
    }

    /**
     * @Route("/book/delete", name="book_delete")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function deleteBookAction(Request $request){
        $book = $request->request->get('book');
        if(!$book){
            return new Response("Parameter 'book' not found");
        }

        $book = $this->get('doctrine_mongodb')->getRepository('AppBundle:Book')->find($book);
        if(!$book){
            return new Response("Book $book not found",400);
        }
        $this->get('doctrine_mongodb')->getManager()->remove($book);
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
            return new Response('Parameter "book" missing',400);
        }elseif(!$quantity || $quantity <= 0){
            return new Response('Parameter "quantity" missing or <= 0',400);
        }elseif(!$cliName){
            return new Response('Parameter "clientName " missing',400);
        }elseif(!$address){
            return new Response('Parameter "address " missing',400);
        }elseif(!$email){
            return new Response('Parameter "email   " missing',400);
        }

        $book = $this->get('doctrine_mongodb')->getRepository('AppBundle:Book')->find($book);

        if(!$book){
            return new Response("Book $book not found",400);
        }

        $dm = $this->get('doctrine_mongodb')->getManager();
        $avQuantity = $book->stock;

        $order = new Order();
        $order->address     = $address;
        $order->clientName  = $cliName;
        $order->email       = $email;
        $order->title       = $book->title;
        $order->quantity = $quantity;
        $order->dispatchingTime = null;

        if ($avQuantity > $quantity){
            $book->stock -= $quantity;
            $order->dispatchingTime = (new DateTime())->add(new DateInterval('P1D'))->getTimestamp();
            $order->state = Order::TO_BE_DISPATCHED;

            Email::orderReadyToBeDispatched($order);

            $dm->persist($book);
        }else{
            try{
                $client = new Client();
                $response = $client->post('http://warehouse.dev/request?title=' . urlencode($order->title) . '&quantity=' . $order->quantity * 10);
            }catch (\Exception $e){
                // Put on Queue
                $queueBook = new QueueBook();
                $queueBook->title = $order->title;
                $queueBook->quantity = $order->quantity * 10;

                $dm->persist($queueBook);
            }

            Email::orderWaitingStock($order);

            $order->state = Order::WAITING_EXPEDITION;
        }

        $dm->persist($order);
        $dm->flush();
        return new JsonResponse($order);
    }

    /**
     * @Route("/order/list", name="order_list")
     * @param Request $request
     * @return Response
     * @internal param Request $request
     */
    public function listOrdersAction(Request $request){
        if ($request->get('email')) {
            $orders = $this->get('doctrine_mongodb')->getRepository('AppBundle:Order')->findBy(['email' => $request->get('email')]);
        } else {
            $orders = $this->get('doctrine_mongodb')->getRepository('AppBundle:Order')->findAll();
        }

        return new JsonResponse($orders);
    }

    /**
     * @Route("/queue/pop", name="queue_pop ")
     * @return Response
     * @internal param Request $request
     */
    public function queuePopAction()
    {
        $queueBook = $this->get('doctrine_mongodb')->getRepository('AppBundle:QueueBook')->findOneBy([], ['id' => 'DESC']);

        if(!$queueBook){
            return new Response('',204);
        }

        $this->get('doctrine_mongodb')->getManager()->remove($queueBook);
        $this->get('doctrine_mongodb')->getManager()->flush();

        return new JsonResponse([
            'title' => $queueBook->title,
            'quantity' => $queueBook->quantity
        ]);
    }

    /**
     * @Route("/receive-stock", name="stock_notify", methods={"POST"})
     * @param Request $request
     * @return Response
     * @internal param Request $request
     */
    public function receiveStockAction(Request $request)
    {
        // Receber um title e um quantity
        $title = $request->get('title');
        $quantity = $request->get('quantity');

        if (!$title) {
            return new Response("Parameter 'title' not found ",400);
        } else if(!$quantity) {
            return new Response("Parameter 'quantity' not found ",400);
        }

        // Criar se necessario o livro
        $book = $this->get('doctrine_mongodb')->getRepository('AppBundle:Book')->findOneBy(['title'=>$title]);

        if (!$book) {
            return new Response("Book $book not found",400);
        }

        // Adicionar stock ao livro
        $book->stock += $quantity;
        $this->get('doctrine_mongodb')->getManager()->persist($book);
        $orders = $this->get('doctrine_mongodb')->getRepository('AppBundle:Order')->findBy([
            'title' => $title,
            'state' => Order::WAITING_EXPEDITION
        ]);

        // Se nao há orders pendentes (Waiting_expedition)
        if (count($orders) == 0) {
            return new JsonResponse([]);
        }
        /** @var Order $order */
        foreach ($orders as $order) {
            if ($order->quantity <= $book->stock) {
                $book->stock -= $order->quantity;
                $order->state = Order::TO_BE_DISPATCHED;
                $this->get('doctrine_mongodb')->getManager()->persist($order);
                Email::orderReadyToBeDispatched($order);
            }

            if ($book->stock == 0) {
                break;
            }
        }

        $this->get('doctrine_mongodb')->getManager()->flush();



        return new JsonResponse([]);
    }

    /**
     * @Route("/dispatch", name="dispatch")
     * @return JsonResponse
     */
    public function dispatchAction()
    {
        $orders = $this->get('doctrine_mongodb')->getRepository('AppBundle:Order')->findBy([
            'state' => Order::TO_BE_DISPATCHED
        ]);

        foreach ($orders as $order) {
            $order->status = Order::DISPATCHED;

            Email::orderDispatched($order);

            $this->get('doctrine_mongodb')->getManager()->persist($order);
        }

        $this->get('doctrine_mongodb')->getManager()->flush();

        return new JsonResponse([]);
    }


    public function printOrder(Order $order)
    {
        $client = new Client();

        $message = 'Nome: ' . $order->clientName . ' | Email: ' . $order->email;
        $message .= ' | Title: ' . $order->title . ' | Quantity: ' . $order->quantity;
        $message .= ' | Price: ' . $order->price;

        try {
            $response = $client->post('http://localhost:3000?body=' . $message);
        } catch (\Exception $e) {
        }
    }


}
