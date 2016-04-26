<?php

namespace Agna\Yii2\Doctrine\Component\ODM\MongoDB;

use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * Abstract model
 *
 * @author stoned
 */
abstract class AbstractCollection
{
    /**
     * Document manager handling the model
     *
     * @var DocumentManager
     */
    protected $documentManager;

    /**
     * Constructor
     *
     * @param DocumentManager $documentManager
     */
    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    /**
     * Persists the model
     *
     * @todo Active Record compatible save method
     */
    //public function save($runValidation = true, $attributeNames = null)
    public function save()
    {
        $this->documentManager->persist($this);
    }
}