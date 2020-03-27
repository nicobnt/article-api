<?php


    namespace App\Form\Type;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;

    class AuthorType extends AbstractType
    {

        public function buildForm(FormBuilderInterface $builder, array $option)
        {
            $builder
                ->add('fullname')
                ->add('biography')
                ;
        }

        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver->setDefaults(array(
                'data_class' => 'App\Entity\Author'
            ));
        }
    }