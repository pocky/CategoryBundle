<?php

/*
 * This file is part of the Black package.
 *
 * (c) Alexandre Balmes <albalmes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Black\Bundle\CategoryBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class CategoryType
 *
 * @package Black\Bundle\CategoryBundle\Form\Type
 * @author  Alexandre Balmes <albalmes@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
abstract class CategoryType extends AbstractType
{
    /**
     * @var type 
     */
    protected $class;

    /**
     * @var
     */
    protected $dbDriver;

    /**
     * @param $class
     * @param $dbDriver
     */
    public function __construct($class, $dbDriver)
    {
        $this->class    = $class;
        $this->dbDriver = $dbDriver;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                    'label'         => 'category.admin.category.name.text',
                    'required'      => true
                )
            )
            ->add('slug', 'text', array(
                    'label'         => 'category.admin.category.slug.text',
                    'required'      => false
                )
            )
            ->add('description', 'textarea', array(
                    'label'         => 'category.admin.category.description.text',
                    'required'      => false
                )
            )
            ->add('parent',
                ($this->dbDriver == 'mongodb') ? 'document' : 'entity',
                array(
                    'class'         => $this->class,
                    'property'      => 'name',
                    'label'         => 'category.admin.category.parent.label',
                    'empty_value'   => 'category.admin.category.parent.empty',
                    'required'      => false
                )
            );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'    => $this->class,
                'intention'     => 'category_form'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'black_category_category';
    }
}
