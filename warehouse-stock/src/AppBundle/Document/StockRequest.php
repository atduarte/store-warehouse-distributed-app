<?php

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JsonSerializable;

/**
 * @MongoDB\Document
 */
class StockRequest implements JsonSerializable
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\String
     */
    protected $title;

    /**
     * @MongoDB\Integer
     */
    protected $quantity;

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     * @return self
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer $quantity
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return array
     */
    function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'timestamp' => (new \MongoId($this->id))->getTimestamp() ,
            'title' => $this->title,
            'quantity' => $this->quantity
        ];
    }
}