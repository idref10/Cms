<?php

namespace Opifer\ContentBundle\Form\Type;

use Opifer\ContentBundle\Model\ContentManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Content Parent Form Type
 */
class ContentParentType extends AbstractType
{
    /** @var ContentManagerInterface */
    protected $contentManager;

    /**
     * Constructor.
     *
     * @param ContentManagerInterface $contentManager
     */
    public function __construct(ContentManagerInterface $contentManager)
    {
        $this->contentManager = $contentManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new CallbackTransformer(
            function ($original) {
                if (is_null($original)) {
                    return null;
                }

                return $original->getId();
            },
            function ($submitted) {
                if (null == $submitted) {
                    return null;
                }

                $content = $this->contentManager->getRepository()->find($submitted);

                return $content;
            }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $optionsResolver)
    {
        $tree = $this->contentManager->getRepository()->childrenHierarchy();
        $optionsResolver->setDefaults([
            'tree' => $tree
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (is_int($view->vars['data'])) {
            $view->vars['value'] = $view->vars['data'];
        }
        $view->vars['tree'] = $options['tree'];
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return EntityType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'opifer_content_parent';
    }
}
