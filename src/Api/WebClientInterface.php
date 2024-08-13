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
use Sylius\Component\Core\Model\ShipmentInterface;

interface WebClientInterface
{
    public const GUARANTEE_TIME0930 = 'TIME0930';

    public const GUARANTEE_TIME1200 = 'TIME1200';

    public const GUARANTEE_SATURDAY = 'SATURDAY';

    public function setShippingGateway(ShippingGatewayInterface $shippingGateway): void;

    public function setShipment(ShipmentInterface $shipment): void;

    public function getSender(): array;

    public function getParcels(): array;

    public function getReceiver(): array;

    public function getServices(): array;

    public function getPickupAddress(): array;
}
