<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\DpdPlShippingExportPlugin\Api;

use BitBag\SyliusShippingExportPlugin\Entity\ShippingGatewayInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Webmozart\Assert\Assert;

final class WebClient implements WebClientInterface
{
    public const DATE_FORMAT = 'Y-m-d';

    private ShippingGatewayInterface $shippingGateway;

    private ShipmentInterface $shipment;

    public function setShippingGateway(ShippingGatewayInterface $shippingGateway): void
    {
        $this->shippingGateway = $shippingGateway;
    }

    public function setShipment(ShipmentInterface $shipment): void
    {
        $this->shipment = $shipment;
    }

    public function getSender(): array
    {
        return [
            'fid' => $this->getShippingGatewayConfig('id'),
            'name' => $this->getShippingGatewayConfig('name'),
            'company' => $this->getShippingGatewayConfig('company'),
            'address' => $this->getShippingGatewayConfig('address'),
            'city' => $this->getShippingGatewayConfig('city'),
            'postalCode' => str_replace('-', '', $this->getShippingGatewayConfig('postal_code')),
            'countryCode' => 'PL',
            'email' => $this->getShippingGatewayConfig('email'),
            'phone' => $this->getShippingGatewayConfig('phone_number'),
        ];
    }

    public function getReceiver(): array
    {
        $shippingAddress = $this->getOrder()->getShippingAddress();
        Assert::notNull($shippingAddress);

        return [
            'company' => $shippingAddress->getCompany(),
            'name' => $shippingAddress->getFullName(),
            'address' => $shippingAddress->getStreet(),
            'city' => $shippingAddress->getCity(),
            'postalCode' => $this->getPostCode(),
            'countryCode' => 'PL',
            'phone' => $shippingAddress->getPhoneNumber(),
            'email' => '',
        ];
    }

    public function getParcels(): array
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

    public function getServices(): array
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

        if (method_exists($this->getOrder(), 'getDpdCud') && true === $this->getOrder()->getDpdCud()) {
            $services['cud'] = '';
        }

        if (method_exists($this->getOrder(), 'getDpdGuarantee') && null !== $this->getOrder()->getDpdGuarantee()) {
            $services['guarantee'] = [
                'type' => $this->getOrder()->getDpdGuarantee(),
            ];
        }

        return $services;
    }

    public function getPickupAddress(): array
    {
        return [
            'fid' => $this->getShippingGatewayConfig('id'),
        ];
    }

    public function getContactInfo(): array
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

    public function getPickupTimeTo(): string
    {
        return $this->getShippingGatewayConfig('shipment_end_hour');
    }

    private function getOrder(): OrderInterface
    {
        $order = $this->shipment->getOrder();
        Assert::notNull($order);

        return $order;
    }

    private function isCashOnDelivery(): bool
    {
        $codPaymentMethodCode = $this->getShippingGatewayConfig('cod_payment_method_code');
        $payments = $this->getOrder()->getPayments();

        foreach ($payments as $payment) {
            $paymentMethod = $payment->getMethod();

            if (null === $paymentMethod) {
                continue;
            }

            return $paymentMethod->getCode() === $codPaymentMethodCode;
        }

        return false;
    }

    private function resolvePickupDate(): string
    {
        $now = new \DateTime();
        $breakingHour = $this->getShippingGatewayConfig('pickup_breaking_hour');

        if ($now->format('H') >= (int) $breakingHour) {
            $tomorrow = $now->modify('+1 day');

            return $this->resolveWeekend($tomorrow)->format(self::DATE_FORMAT);
        }

        return $this->resolveWeekend($now)->format(self::DATE_FORMAT);
    }

    private function resolveWeekend(\DateTime $date): \DateTime
    {
        $dayOfWeek = (int) $date->format('N');

        if (6 === $dayOfWeek) {
            return $date->modify('+2 days');
        }

        if (7 === $dayOfWeek) {
            return $date->modify('+1 day');
        }

        return $date;
    }

    private function getShippingGatewayConfig(string $config): mixed
    {
        return $this->shippingGateway->getConfigValue($config);
    }

    private function getPostCode(): string
    {
        $shippingAddress = $this->getOrder()->getShippingAddress();
        Assert::notNull($shippingAddress);

        $postCode = $shippingAddress->getPostcode();

        return null !== $postCode ? str_replace('-', '', $postCode) : '';
    }
}
