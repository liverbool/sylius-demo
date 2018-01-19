<?php

declare(strict_types=1);

namespace Toro\SyliusToroPayPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

final class GatewayConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('toropay_client_id', TextType::class, [
                'required' => true,
                'label' => 'toro.form.gateway_configuration.toropay_client_id',
                'constraints' => [
                    new NotBlank([
                        'message' => 'toro.gateway_config.toropay_client_id.not_blank',
                        'groups' => ['sylius', 'toropay'],
                    ])
                ],
            ])
            ->add('toropay_client_secret', TextType::class, [
                'required' => true,
                'label' => 'toro.form.gateway_configuration.toropay_client_secret',
                'constraints' => [
                    new NotBlank([
                        'message' => 'toro.gateway_config.toropay_client_secret.not_blank',
                        'groups' => ['sylius', 'toropay'],
                    ])
                ],
            ])
            ->add('toropay_redirect_uri', UrlType::class, [
                'required' => true,
                'label' => 'toro.form.gateway_configuration.toropay_redirect_uri',
                'constraints' => [
                    new NotBlank([
                        'message' => 'toro.gateway_config.toropay_redirect_uri.not_blank',
                        'groups' => ['sylius', 'toropay'],
                    ])
                ],
            ])
            ->add('toropay_country_code', CountryType::class, [
                'required' => true,
                'label' => 'toro.form.gateway_configuration.toropay_country_code',
                'constraints' => [
                    new NotBlank([
                        'message' => 'toro.gateway_config.toropay_country_code.not_blank',
                        'groups' => ['sylius', 'toropay'],
                    ])
                ],
            ])
            ->add('toropay_sandbox', CheckboxType::class, [
                'label' => 'toro.form.gateway_configuration.toropay_sandbox',
            ])
        ;
    }
}
