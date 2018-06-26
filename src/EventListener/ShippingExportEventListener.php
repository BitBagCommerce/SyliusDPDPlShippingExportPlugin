<?php

namespace BitBag\DpdPlShippingExportPlugin\EventListener;

use BitBag\DpdPlShippingExportPlugin\Api\SoapClientInterface;
use BitBag\DpdPlShippingExportPlugin\Api\WebClientInterface;
use BitBag\SyliusShippingExportPlugin\Event\ExportShipmentEvent;
use DPD\Services\DPDService;

final class ShippingExportEventListener
{
    const DPD_GATEWAY_CODE = 'dpd_pl';
    const BASE_LABEL_EXTENSION = 'pdf';

    /**
     * @var WebClientInterface
     */
    private $webClient;

    private $soapClient;

    /**
     * @param WebClientInterface $webClient
     * @param SoapClientInterface $soapClient
     */
    public function __construct(WebClientInterface $webClient, SoapClientInterface $soapClient)
    {
        $this->webClient = $webClient;
        $this->soapClient = $soapClient;
    }

    /**
     * @param ExportShipmentEvent $exportShipmentEvent
     */
    public function exportShipment(ExportShipmentEvent $exportShipmentEvent)
    {
        $shippingExport = $exportShipmentEvent->getShippingExport();
        $shippingGateway = $shippingExport->getShippingGateway();

        if ($shippingGateway->getCode() !== self::DPD_GATEWAY_CODE) {
            return;
        }

        $shipment = $shippingExport->getShipment();

        $this->webClient->setShippingGateway($shippingGateway);
        $this->webClient->setShipment($shipment);

        try {
            $dpd = new DPDService(
                $shippingGateway->getConfigValue('id'),
                $shippingGateway->getConfigValue('login'),
                $shippingGateway->getConfigValue('password'),
                $shippingGateway->getConfigValue('wsdl')
            );

            $dpd->setSender($this->webClient->getSender());

            $result = $dpd->sendPackage($this->webClient->getParcels(), $this->webClient->getReceiver(), 'SENDER', $this->webClient->getServices());

            $speedlabel = $dpd->generateSpeedLabelsByPackageIds([$result->packageId], $this->webClient->getPickupAddress());

            $protocol = $dpd->generateProtocolByPackageIds([$result->packageId], $this->webClient->getPickupAddress());

            $dpd->pickupRequest(
                [$protocol->documentId],
                $this->webClient->getPickupDate(),
                $this->webClient->getPickupTimeFrom(),
                $this->webClient->getPickupTimeTo(),
                $this->webClient->getContactInfo(),
                $this->webClient->getPickupAddress()
            );

        } catch (\Exception $exception) {
            $exportShipmentEvent->addErrorFlash(sprintf(
                "DPD Web Service for #%s order: %s",
                $shipment->getOrder()->getNumber(),
                $exception->getMessage()));

            return;
        }

        $exportShipmentEvent->saveShippingLabel($speedlabel->filedata, 'pdf');
        $exportShipmentEvent->addSuccessFlash();
        $exportShipmentEvent->exportShipment();
    }
}
