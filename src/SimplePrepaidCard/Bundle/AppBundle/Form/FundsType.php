<?php

declare(strict_types=1);

namespace SimplePrepaidCard\Bundle\AppBundle\Form;

use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

//Todo: Money transformer
class FundsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('amount', TextType::class, ['constraints' => new Assert\NotBlank()]);
    }
}
