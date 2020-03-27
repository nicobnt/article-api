<?php


    namespace App\Form\Type;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;

    class ArticleType extends AbstractType
    {

        public function buildForm(FormBuilderInterface $builder, array $option)
        {
            $builder
                ->add('title')
                ->add('content')
                ->add(
                    'author',
                    AuthorType::class,
                    [
                        'required' => false,
                    ]
                );
        }

        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver->setDefaults(array(
                'data_class' => 'App\Entity\Article'
            ));
        }
    }