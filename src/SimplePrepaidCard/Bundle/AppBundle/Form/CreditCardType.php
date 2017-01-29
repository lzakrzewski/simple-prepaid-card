<?php

declare(strict_types=1);

namespace SimplePrepaidCard\Bundle\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CreditCardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('card_holder', TextType::class, ['constraints' => new Assert\NotBlank()]);
        $builder->add('card_number', TextType::class, ['constraints' => new Assert\NotBlank()]);
        $builder->add('cvv_code', IntegerType::class, ['constraints' => new Assert\NotBlank()]);
        $builder->add('expiry_date_month', TextType::class, ['constraints' => new Assert\NotBlank()]);
        $builder->add('expiry_date_year', TextType::class, ['constraints' => new Assert\NotBlank()]);
        $builder->add('submit', SubmitType::class, ['attr' => ['class' => 'btn-success']]);
    }
}
