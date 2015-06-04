<?php
/**
 * Created by IntelliJ IDEA.
 * User: carlos
 * Date: 21/05/2015
 * Time: 22:30
 */

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JsonSerializable;

/** @MongoDB\Document() */
class Order extends Document implements JsonSerializable{
    const WAITING_EXPEDITION = 1;
    const TO_BE_DISPATCHED = 2;
    const DISPATCHED = 3;

    /** @MongoDB\String() */
    public $title;

    /** @MongoDB\Int() */
    public $quantity;

    /** @MongoDB\String() */
    public $clientName;

    /** @MongoDB\String() */
    public $address;

    /** @MongoDB\String() */
    public $email;

    /** @MongoDB\Int() */
    public $state;

    /** @MongoDB\Timestamp() */
    public $dispatchingTime;

    /** @MongoDB\Float() */
    public $price;

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    function jsonSerialize()
    {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'quantity'       => $this->quantity,
            'clientName'     => $this->clientName,
            'address'        => $this->address,
            'email'          => $this->email,
            'state'          => $this->state,
            'dispatchingTime'=> $this->dispatchingTime
        ];
    }
}