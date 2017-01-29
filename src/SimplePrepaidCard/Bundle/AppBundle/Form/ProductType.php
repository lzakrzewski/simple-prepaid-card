<?php

declare(strict_types=1);

namespace SimplePrepaidCard\Bundle\AppBundle\Form;

use SimplePrepaidCard\Bundle\AppBundle\Form\DataTransformer\MoneyToStringTransformer;
use SimplePrepaidCard\CoffeeShop\Model\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'product_id',
            HiddenType::class,
            [
                'data'        => Product::coffee()->productId(),
                'constraints' => new Assert\NotBlank(),
            ]
        );

        $builder->add(
            'name',
            TextType::class,
            [
                'attr' => [
                    'readonly' => 'readonly',
                ],
                'data'        => Product::coffee()->name(),
                'constraints' => new Assert\NotBlank(),
            ]
        );

        $builder->add(
            'price',
            TextType::class,
            [
                'attr' => [
                    'readonly' => 'readonly',
                ],
                'data'        => Product::coffee()->price(),
                'data_class'  => null,
                'constraints' => new Assert\NotBlank(),
            ]
        );

        $builder->get('price')->addModelTransformer(new MoneyToStringTransformer());

        $builder->add('buy', SubmitType::class, ['attr' => ['class' => 'btn-success']]);
    }
}
