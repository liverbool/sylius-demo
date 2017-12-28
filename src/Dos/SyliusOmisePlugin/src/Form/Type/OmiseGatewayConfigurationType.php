<?php

declare(strict_types=1);

namespace Dos\SyliusOmisePlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

final class OmiseGatewayConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('omise_public_key', TextType::class, [
                'required' => true,
                'label' => 'dos.form.gateway_configuration.omise_public_key',
                'constraints' => [
                    new NotBlank([
                        'message' => 'dos.gateway_config.omise_public_key.not_blank',
                        'groups' => ['sylius', 'dos_omise'],
                    ])
                ],
            ])
            ->add('omise_secret_key', TextType::class, [
                'required' => true,
                'label' => 'dos.form.gateway_configuration.omise_secret_key',
                'constraints' => [
                    new NotBlank([
                        'message' => 'dos.gateway_config.omise_secret_key.not_blank',
                        'groups' => ['sylius', 'dos_omise'],
                    ])
                ],
            ])
            ->add('omise_country_code', CountryType::class, [
                'required' => true,
                'label' => 'dos.form.gateway_configuration.omise_country_code',
                'constraints' => [
                    new NotBlank([
                        'message' => 'dos.gateway_config.omise_country_code.not_blank',
                        'groups' => ['sylius', 'dos_omise'],
                    ])
                ],
            ])
            ->add('omise_3d_secure', CheckboxType::class, [
                'label' => 'dos.form.gateway_configuration.omise_3d_secure',
            ])
            ->add('omise_live_mode', CheckboxType::class, [
                'label' => 'dos.form.gateway_configuration.omise_live_mode',
            ])
        ;
    }
}
