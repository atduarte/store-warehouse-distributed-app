<?php
/**
 * Created by IntelliJ IDEA.
 * User: carlos
 * Date: 21/05/2015
 * Time: 22:37
 */

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/** @MongoDB\Document() */
class Document {
    /** @MongoDB\Id() */
    public $id;
}