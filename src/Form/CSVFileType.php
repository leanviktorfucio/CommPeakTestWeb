<?php

namespace App\Form;

use App\Entity\CSVFileEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CSVFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('files', FileType::class, [
                    'multiple' => true,
                    'mapped' => true,
                    'required' => true,
                    'attr' => ['class' => 'form-control', 'accept' => '.csv'],
                    'label_format' => 'Upload CSV file',
                    'label_attr' => ['class' => 'form-label']
                ])
            ->add('Upload', SubmitType::class, ['attr' => ['class' => 'mt-3 btn btn-primary']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CSVFileEntity::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token'

        ]);
    }
}
