<?php

namespace Czarnodziej\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('name', 'text', array(
                    'label'       => 'contact.label.name',
                    'attr'        => array(
                        'placeholder' => 'contact.placeholder.name',
                        'pattern'     => '.{2,}' //minlength
                    ),
                    'constraints' => array(
                        new NotBlank(array('message' => 'contact.name.not_blank')),
                        new Length(array('min' => 2, 'minMessage' => 'contact.name.min_message')),
                    )
                ))
                ->add('email', 'email', array(
                    'label'       => 'contact.label.email',
                    'attr'        => array(
                        'placeholder' => 'contact.placeholder.email',
                    ),
                    'constraints' => array(
                        new NotBlank(array('message' => 'contact.email.not_blank')),
                        new Email(array('message' => 'contact.email.valid'))
                    )
                ))
                ->add('subject', 'text', array(
                    'label'       => 'contact.label.subject',
                    'attr'        => array(
                        'placeholder' => 'contact.placeholder.subject',
                        'pattern'     => '.{3,}' //minlength
                    ),
                    'constraints' => array(
                        new NotBlank(array('message' => 'contact.subject.not_blank')),
                        new Length(array('min' => 3, 'minMessage' => 'contact.subject.min_message'))
                    )
                ))
                ->add('message', 'textarea', array(
                    'label'       => 'contact.label.message',
                    'attr'        => array(
                        'cols'        => 50,
                        'rows'        => 10,
                        'placeholder' => 'contact.placeholder.message'
                    ),
                    'constraints' => array(
                        new NotBlank(array('message' => 'contact.message.not_blank')),
                        new Length(array('min' => 5, 'minMessage' => 'contact.message.min_message'))
                    )
        ));
    }
    public function getName()
    {
        return 'contact';
    }
}
