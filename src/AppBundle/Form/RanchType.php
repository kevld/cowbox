<?php
/**
 * Created by PhpStorm.
 * User: K.viroulaud
 * Date: 06/12/15
 * Time: 10:42
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class RanchType
 * @package AppBundle\Form
 */
class RanchType extends AbstractType
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
        return 'add_ranch_form';
    }


}

