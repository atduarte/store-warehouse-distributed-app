<?php

namespace AppBundle\Helper;

use Mandrill_Error;

class Email
{
    const MANDRILL_API_KEY = "Zu6GDvDOVMQJ4tCF2IS1Zw";

    /**
     * Send to Many
     *
     * @param $name
     * @param $email
     * @param $subject
     * @param $message
     * @return bool
     */
    public static function send($name, $email, $subject, $message)
    {
        try {
            $mandrill = new \Mandrill(self::MANDRILL_API_KEY);

            $message = array(
                'html' => $message,
                'subject' => $subject,
                'from_email' => 'suporte@revis.pt',
                'from_name' => 'Revis.pt',
                'track_opens' => true,
                'track_clicks' => true,
                'to' => [['email' => $email, 'name' => $name, 'type' => 'to']]
            );

            $async = false;
            $result = $mandrill->messages->send($message, $async);

            return (isset($result[0]['status']) && $result[0]['status'] == 'sent' || $result[0]['status'] == 'queued');
        } catch (Mandrill_Error $e) {
            return false;
        }
    }

    /**
     * @param $order
     */
    public static function orderDispatched($order)
    {
        $message = $order->quantity . ' of ' . $order->title . ' dispatched today.';
        Email::send($order->clientName, $order->email, 'Ready to be Dispatched', $message);
    }

    /**
     * @param $order
     */
    public static function orderReadyToBeDispatched($order)
    {
        $message = $order->quantity . ' of ' . $order->title . ' ready to be dispatched, and will be dispatched tomorrow.';
        Email::send($order->clientName, $order->email, 'Ready to be Dispatched', $message);
    }

    /**
     * @param $order
     */
    public static function orderWaitingStock($order)
    {
        $message = $order->quantity . ' of ' . $order->title . ' not available right now.';
        Email::send($order->clientName, $order->email, 'Waiting for stock', $message);
    }
}