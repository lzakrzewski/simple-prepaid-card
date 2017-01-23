<?php

declare(strict_types=1);

namespace SimplePrepaidCard\Bundle\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CreditCardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('card_number', TextType::class, ['constraints' => new Assert\NotBlank()]);
        $builder->add('card_holder', TextType::class, ['constraints' => new Assert\NotBlank()]);
        $builder->add('ccv', TextType::class, ['constraints' => new Assert\NotBlank()]);
        $builder->add('expires', TextType::class, ['constraints' => new Assert\NotBlank()]);
        $builder->add('save', SubmitType::class);
    }
}
