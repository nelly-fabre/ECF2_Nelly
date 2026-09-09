<?php

namespace App\Form;

use App\Entity\Absence;
use App\Entity\Student;
use App\Entity\Reason;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class AbsenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('absenceDay', DateType::class, [
                'label' => 'Date de l\'absence',
                'widget' => 'single_text',
            ])
            ->add('reason', EntityType::class, [
                'class' => Reason::class,
                'choice_label' => 'reasonName',
                'label' => 'Motif',
                'placeholder' => 'Sélectionnez un motif',
            ])
            ->add('student', EntityType::class, [
                'class' => Student::class,
                'choice_label' => function (Student $student) {
                    return $student->getFirstName() . ' ' . $student->getFamilyName();
                },
                'label' => 'Stagiaire',
                'placeholder' => 'Sélectionnez un stagiaire',
            ])
            ->add('documentFile', FileType::class, [
                'label' => 'Justificatif (PDF uniquement)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File(
                        maxSize: '5M',
                        mimeTypes: [
                            'application/pdf',

                        ],
                        mimeTypesMessage: 'Veuillez déposer un fichier PDF',
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Absence::class,
        ]);
    }
}
