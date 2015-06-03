<?php

namespace AppBundle\Command;

use AppBundle\Document\StockRequest;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StoreQueueCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('store-queue:pull')
            ->setDescription('Get the items on store-queue');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $storeUrl = $this->getContainer()->getParameter('store_url');

        $client = new Client();
        $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();

        $i = 0;
        while (true) {
            try {
                $response = $client->get($storeUrl . '/queue/pop');
            } catch (\Exception $ex) {
                break;
            }

            if ($response->getStatusCode() != 200) {
                break;
            }

            $response = json_decode($response->getBody());

            $stockRequest = new StockRequest();
            $stockRequest->setTitle($response->title);
            $stockRequest->setQuantity($response->quantity);

            $dm->persist($stockRequest);

            $i++;
        }

        $dm->flush();

        $output->writeln($i . ' new orders added');
    }
}

