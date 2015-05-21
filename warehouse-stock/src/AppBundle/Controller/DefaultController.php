<?php

namespace AppBundle\Controller;

use AppBundle\Document\StockRequest;
use Documents\CustomRepository\Repository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="list", methods={"GET"})
     * @return JsonResponse
     */
    public function listAction()
    {
        /** @var Repository $requestsRepo */
        $requestsRepo = $this->get('doctrine_mongodb')->getRepository('AppBundle:StockRequest');
        $requests = $requestsRepo->findAll();

        return $this->success($requests);
    }

    /**
     * @Route("/request", name="request-stock", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function requestAction(Request $request)
    {
        $title = $request->get('title', null);
        $quantity = $request->get('quantity', null);

        if ($title == null || $quantity == null) {
            return $this->fail('"title" and "quantity" (GET) parameters are required.');
        }

        $stockRequest = new StockRequest();
        $stockRequest->setTitle($title);
        $stockRequest->setQuantity($quantity);

        $dm = $this->get('doctrine_mongodb')->getManager();
        $dm->persist($stockRequest);
        $dm->flush();

        return $this->success();
    }

    /**
     * @Route("/send/{id}", requirements={"id" = ".+"}, name="send-stock", methods={"GET"})
     * @return JsonResponse
     */
    public function sendStockAction($id)
    {
        /** @var Repository $requestsRepo */
        $requestsRepo = $this->get('doctrine_mongodb')->getRepository('AppBundle:StockRequest');
        $stockRequest = $requestsRepo->findOneBy(['id' => $id]);

        if (!$stockRequest) {
            return $this->notFound();
        }

        // TODO: Call Store

        $dm = $this->get('doctrine_mongodb')->getManager();
        $dm->remove($stockRequest);
        $dm->flush();

        return $this->success($stockRequest);
    }

    protected function notFound($message = 'Not Found')
    {
        return new JsonResponse(['message' => $message], 404);
    }

    protected function fail($message = null)
    {
        return new JsonResponse(['message' => $message], 500);
    }

    protected function success($data = null)
    {
        return new JsonResponse($data);
    }
}
