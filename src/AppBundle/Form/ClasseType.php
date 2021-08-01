<?php
/**
 * Created by PhpStorm.
 * User: K.viroulaud
 * Date: 24/01/16
 * Time: 15:29
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ClasseType
 * @package AppBundle\Form
 */
class ClasseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', 'text', array('required' => true))
        ;
    }
    public function getDefaultOptions(array $options)
    {

    }

    public function getName()
    {
        return 'add_classe_form';
    }


}

