<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('locales');
        $resolver->setRequired('defaultLocale');
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['locales'] as $locale) {
            if ($locale === $options['defaultLocale']) {
                $builder
                    ->add('title', null, [
                        'attr' => ['autofocus' => true],
                        'label' => 'label.title',
                    ])
                    ->add('description', TextareaType::class, [
                        'label' => 'label.description',
                    ]);

                continue;
            }

            $builder->add('title_'.$locale, null, [
                'mapped' => false,
                'label' => 'label.title.'.$locale,
                'required' => false,
                ])
                ->add('description_'.$locale, null, [
                    'mapped' => false,
                    'label' => 'label.description.'.$locale,
                    'required' => false,
                ]);
        }
    }
}
