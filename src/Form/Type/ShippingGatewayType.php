<?php

namespace BitBag\DpdPlShippingExportPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
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
            ->add('shipment_start_hour', TextType::class, [
                'label' => 'bitbag.ui.shipment_start_hour',
            ])
            ->add('shipment_end_hour', TextType::class, [
                'label' => 'bitbag.ui.shipment_end_hour',
            ])
            ->add('pickup_breaking_hour', TextType::class, [
                'label' => 'bitbag.ui.pickup_breaking_hour',
            ])
            ->add('cod_payment_method_code', TextType::class, [
                'label' => 'bitbag.ui.cod_payment_method_code',
            ])
        ;
    }
}
