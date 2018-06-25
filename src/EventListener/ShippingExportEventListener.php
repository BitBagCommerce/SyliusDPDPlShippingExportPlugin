<?php

namespace BitBag\DpdPlShippingExportPlugin\EventListener;

use BitBag\DpdPlShippingExportPlugin\Api\SoapClientInterface;
use BitBag\DpdPlShippingExportPlugin\Api\WebClientInterface;
use BitBag\SyliusShippingExportPlugin\Event\ExportShipmentEvent;

/**
 * @author Mikołaj Król <mikolaj.krol@bitbag.pl>
 */
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
            $requestData = $this->webClient->getRequestData();

            $response = $this->soapClient->createShipment($requestData, $shippingGateway->getConfigValue('wsdl'));
        } catch (\Exception $exception) {
            $exportShipmentEvent->addErrorFlash(sprintf(
                "DPD Web Service for #%s order: %s",
                $shipment->getOrder()->getNumber(),
                $exception->getMessage()));

            return;
        }

        $labelContent = base64_decode($response->createShipmentResult->label->labelContent);
        $extension = self::BASE_LABEL_EXTENSION;

        if ($response->createShipmentResult->label->labelType === 'ZBLP') {
            $extension = 'zpl';
        }

        $exportShipmentEvent->saveShippingLabel($labelContent, $extension);
        $exportShipmentEvent->addSuccessFlash();
        $exportShipmentEvent->exportShipment();
    }
}
