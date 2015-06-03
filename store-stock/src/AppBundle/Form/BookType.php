<?php
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Created by PhpStorm.
 * User: cteixeira
 * Date: 5/31/2015
 * Time: 11:20 PM
 */

class BookType extends  AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('photo', 'url', [
            'placeholder' => 'Photo URL',
        ])
        ->add('title', 'text', [
            'placeholder' => 'Title',
        ])
        ->add('description', 'text', [
            'placeholder' => 'Description',
        ])
        ->add('stock', 'integer', [
            'placeholder' => 'Available Stock',
        ]);
    }
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'book_type';
    }
}