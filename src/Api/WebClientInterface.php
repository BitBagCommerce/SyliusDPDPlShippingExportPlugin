<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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
