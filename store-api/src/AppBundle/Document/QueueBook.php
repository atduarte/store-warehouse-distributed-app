<?php
/**
 * Created by PhpStorm.
 * User: cteixeira
 * Date: 6/3/2015
 * Time: 5:21 PM
 */

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/** @MongoDB\Document() */
class QueueBook extends Document
{
    /** @MongoDB\String() */
    public $title;

    /** @MongoDB\Int() */
    public $quantity;


}