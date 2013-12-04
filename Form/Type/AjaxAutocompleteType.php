<?php

namespace Shtumi\UsefulBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Exception\FormException;
use Shtumi\UsefulBundle\Form\DataTransformer\EntityToPropertyTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AjaxAutocompleteType extends AbstractType
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'entity_alias'      => null,
            'class'             => null,
            'property'          => null,
            'compound'          => false
        ));
    }

    public function getName()
    {
        return 'shtumi_ajax_autocomplete';
    }

    public function getParent()
    {
        return 'text';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entities = $this->container->getParameter('shtumi.autocomplete_entities');

  
        $options['class'] = $entities[$options['entity_alias']]['class'];
        $options['property'] = $entities[$options['entity_alias']]['property'];


        $builder->addViewTransformer(new EntityToPropertyTransformer(
            $this->container->get('doctrine')->getManager(),
            $options['class'],
            $options['property']
        ), true);

        $builder->setAttribute('entity_alias', $options['entity_alias']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['entity_alias'] = $form->getConfig()->getAttribute('entity_alias');
    }

}
