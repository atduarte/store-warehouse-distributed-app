<?php
/**
 * Created by IntelliJ IDEA.
 * User: carlos
 * Date: 21/05/2015
 * Time: 22:30
 */

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/** @MongoDB\Document() */
class Order extends Document{
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
    /** @MongoDB\String() */
    public $state;
}