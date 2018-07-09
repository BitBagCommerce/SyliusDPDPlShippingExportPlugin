<?php

namespace BitBag\DpdPlShippingExportPlugin\Api;

use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;

final class WebClient implements WebClientInterface
{
    const DATE_FORMAT = 'Y-m-d';

    /**
     * @var ShippingGatewayInterface
     */
    private $shippingGateway;

    /**
     * @var ShipmentInterface
     */
    private $shipment;

    /**
     * {@inheritdoc}
     */
    public function setShippingGateway(ShippingGatewayInterface $shippingGateway)
    {
        $this->shippingGateway = $shippingGateway;
    }

    /**
     * {@inheritdoc}
     */
    public function setShipment(ShipmentInterface $shipment)
    {
        $this->shipment = $shipment;
    }


    /**
     * @return array
     */
    public function getSender()
    {
        return [
            'fid' => $this->getShippingGatewayConfig('id'),
            'name' => $this->getShippingGatewayConfig('name'),
            'company' => $this->getShippingGatewayConfig('company'),
            'address' => $this->getShippingGatewayConfig('address'),
            'city' => $this->getShippingGatewayConfig('city'),
            'postalCode' => $this->getShippingGatewayConfig('postal_code'),
            'countryCode' => 'PL',
            'email' => $this->getShippingGatewayConfig('email'),
            'phone' => $this->getShippingGatewayConfig('phone_number'),
        ];
    }

    /**
     * @return array
     */
    public function getReceiver()
    {
        $shippingAddress = $this->getOrder()->getShippingAddress();

        return [
            'company' => $shippingAddress->getCompany(),
            'name' => $shippingAddress->getFullName(),
            'address' => $shippingAddress->getStreet(),
            'city' => $shippingAddress->getCity(),
            'postalCode' => str_replace('-', '', $shippingAddress->getPostcode()),
            'countryCode' => 'PL',
            'phone' => $shippingAddress->getPhoneNumber(),
            'email' => '',
        ];
    }

    /**
     * @return array
     */
    public function getParcels()
    {
        $weight = $this->shipment->getShippingWeight();

        if (method_exists($this->getOrder(), 'getCustomWeight') && $this->getOrder()->getCustomWeight()) {
            $weight = $this->getOrder()->getCustomWeight();
        }

        $additionalInfo = '';

        if (method_exists($this->getOrder(), 'getShippingNotes')) {
            $additionalInfo = $this->getOrder()->getShippingNotes();
        }

        return [
            0 => [
                'content' => $this->getOrder()->getNumber() . ' | ' . $additionalInfo,
                'weight' => $weight,
            ],
        ];
    }

    /**
     * @return array
     */
    public function getServices()
    {
        $services = [];

        if ($this->isCashOnDelivery()) {

            $value = $this->getOrder()->getTotal();

            if (method_exists($this->getOrder(), 'getCustomCod') && $this->getOrder()->getCustomCod()) {
                $value = $this->getOrder()->getCustomCod();
            }

            $services['cod'] = [
                'amount' => $value / 100,
                'currency' => 'PLN',
            ];
        }

        if (method_exists($this->getOrder(), 'getDpdCud') && $this->getOrder()->getDpdCud() === true) {
            $services['cud'] = '';
        }

        if (method_exists($this->getOrder(), 'getDpdGuarantee') && $this->getOrder()->getDpdGuarantee() !== null) {
            $services['guarantee'] = [
                'type' => $this->getOrder()->getDpdGuarantee()
            ];
        }

        return $services;
    }

    /**
     * @return array
     */
    public function getPickupAddress()
    {
        return [
            'fid' => $this->getShippingGatewayConfig('id'),
//            'name' => 'NAME',
//            'company' => 'COMPANY',
//            'address' => 'ADDRESS',
//            'city' => 'CITY',
//            'postalCode' => '85132',
//            'countryCode' => 'PL',
//            'email'=> 'test@test.test',
//            'phone' => '777888999',
        ];
    }

    /**
     * @return array
     */
    public function getContactInfo()
    {
        return [
            'name' => $this->getShippingGatewayConfig('name'),
            'company' => $this->getShippingGatewayConfig('company'),
            'email' => $this->getShippingGatewayConfig('email'),
            'phone' => $this->getShippingGatewayConfig('phone_number'),
        ];
    }

    public function getPickupDate(): string
    {
        return $this->resolvePickupDate();
    }

    public function getPickupTimeFrom(): string
    {

        return $this->getShippingGatewayConfig('shipment_start_hour');
    }

    /**
     * @return array
     */
    public function getPickupTimeTo(): string
    {

        return $this->getShippingGatewayConfig('shipment_end_hour');
    }

    /**
     * @return OrderInterface|\Sylius\Component\Order\Model\OrderInterface
     */
    private function getOrder()
    {
        return $this->shipment->getOrder();
    }

    /**
     * @return boolean
     */
    private function isCashOnDelivery()
    {
        $codPaymentMethodCode = $this->getShippingGatewayConfig('cod_payment_method_code');
        $payments = $this->getOrder()->getPayments();

        foreach ($payments as $payment) {
            return $payment->getMethod()->getCode() === $codPaymentMethodCode;
        }

        return false;
    }

    /**
     * @return string
     */
    private function resolvePickupDate()
    {
        $now = new \DateTime();
        $breakingHour = $this->getShippingGatewayConfig('pickup_breaking_hour');

        if (null !== $breakingHour && $now->format('H') >= (int)$breakingHour) {
            $tomorrow = $now->modify("+1 day");

            return $this->resolveWeekend($tomorrow)->format(self::DATE_FORMAT);
        }

        return $this->resolveWeekend($now)->format(self::DATE_FORMAT);
    }

    /**
     * @param \DateTime $date
     *
     * @return \DateTime
     */
    private function resolveWeekend(\DateTime $date)
    {
        $dayOfWeek = (int)$date->format('N');

        if ($dayOfWeek === 6) {

            return $date->modify("+2 days");
        }

        if ($dayOfWeek === 7) {

            return $date->modify("+1 day");
        }

        return $date;
    }

    /**
     * @param $config
     *
     * @return string
     */
    private function getShippingGatewayConfig($config)
    {
        return $this->shippingGateway->getConfigValue($config);
    }
}
