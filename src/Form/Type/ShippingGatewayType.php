<?php

namespace BitBag\DpdPlShippingExportPlugin\Form\Type;

use BitBag\DpdPlShippingExportPlugin\Api\WebClient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ShippingGatewayType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('wsdl', TextType::class, [
                'label' => 'bitbag.ui.dpd_wsdl',
            ])
            ->add('id', TextType::class, [
                'label' => 'bitbag.ui.dpd_id',
            ])
            ->add('login', TextType::class, [
                'label' => 'bitbag.ui.dpd_login',
            ])
            ->add('password', TextType::class, [
                'label' => 'bitbag.ui.dpd_password',
            ])
            ->add('name', TextType::class, [
                'label' => 'bitbag.ui.name',
            ])
            ->add('company', TextType::class, [
                'label' => 'bitbag.ui.company',
            ])
            ->add('address', TextType::class, [
                'label' => 'bitbag.ui.address',
            ])
            ->add('city', TextType::class, [
                'label' => 'bitbag.ui.city',
            ])
            ->add('postal_code', TextType::class, [
                'label' => 'bitbag.ui.postal_code',
            ])
            ->add('email', TextType::class, [
                'label' => 'bitbag.ui.email',
            ])
            ->add('phone_number', TextType::class, [
                'label' => 'bitbag.ui.phone_number',
            ])
            ->add('cud', CheckboxType::class, [
                'label' => 'dpd.ui.cud',
            ])
            ->add('guarantee', ChoiceType::class, [
                'label' => 'dpd.ui.guarantee',
                'required' => false,
                'choices' => [
                    'dpd.ui.standard' => '',
                    'dpd.ui.time0930' => WebClient::GUARANTEE_TIME0930,
                    'dpd.ui.time1200' => WebClient::GUARANTEE_TIME1200,
                    'dpd.ui.saturday' => WebClient::GUARANTEE_SATURDAY,
                ],
            ])
            ->add('cod_payment_method_code', TextType::class, [
                'label' => 'bitbag.ui.cod_payment_method_code',
            ])
        ;
    }
}