<?php
/**
 * Created by IntelliJ IDEA.
 * User: carlos
 * Date: 21/05/2015
 * Time: 22:13
 */

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/** @MongoDB\Document() */
class Book extends  Document implements \JsonSerializable
{
    /** @MongoDB\String() */
    public $photo;
    /** @MongoDB\String() */
    public $title;
    /** @MongoDB\String() */
    public $description;
    /** @MongoDB\Int() */
    public $stock;

    function jsonSerialize(){
        return [
            'photo'      => $this->photo,
            'title'      => $this->title,
            'description'=> $this->description,
            'stock'      => $this->stock
        ];
    }
}