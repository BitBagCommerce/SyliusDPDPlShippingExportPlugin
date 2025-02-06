<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\DpdPlShippingExportPlugin\EventListener;

use BitBag\DpdPlShippingExportPlugin\Api\WebClientInterface;
use BitBag\SyliusShippingExportPlugin\Entity\ShippingExportInterface;
use Doctrine\Persistence\ObjectManager;
use DPD\Services\DPDService;
use Exception;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Webmozart\Assert\Assert;

final class ShippingExportEventListener
{
    public const DPD_GATEWAY_CODE = 'dpd_pl';

    private WebClientInterface $webClient;

    private RequestStack $requestStack;

    private Filesystem $fileSystem;

    private ObjectManager $shippingExportManager;

    private string $shippingLabelsPath;

    private string $labelFileFormat;

    private string $labelPageFormat;

    private string $labelType;

    public function __construct(
        WebClientInterface $webClient,
        RequestStack $requestStack,
        FileSystem $fileSystem,
        ObjectManager $shippingExportManager,
        string $shippingLabelsPath,
        string $labelFileFormat,
        string $labelPageFormat,
        string $labelType
    ) {
        $this->webClient = $webClient;
        $this->requestStack = $requestStack;
        $this->fileSystem = $fileSystem;
        $this->shippingExportManager = $shippingExportManager;
        $this->shippingLabelsPath = $shippingLabelsPath;
        $this->labelFileFormat  = $labelFileFormat;
        $this->labelPageFormat = $labelPageFormat;
        $this->labelType = $labelType;
    }

    public function exportShipment(ResourceControllerEvent $exportShipmentEvent): void
    {
        /** @var ShippingExportInterface|mixed $shippingExport */
        $shippingExport = $exportShipmentEvent->getSubject();
        Assert::isInstanceOf($shippingExport, ShippingExportInterface::class);

        $shippingGateway = $shippingExport->getShippingGateway();
        Assert::notNull($shippingGateway);

        if (self::DPD_GATEWAY_CODE !== $shippingGateway->getCode()) {
            return;
        }

        $shipment = $shippingExport->getShipment();

        $this->webClient->setShippingGateway($shippingGateway);

        Assert::notNull($shipment);

        $this->webClient->setShipment($shipment);
        /** @var Session $session */
        $session = $this->requestStack->getSession();

        try {
            $dpd = new DPDService(
                $shippingGateway->getConfigValue('id'),
                $shippingGateway->getConfigValue('login'),
                $shippingGateway->getConfigValue('password'),
                $shippingGateway->getConfigValue('wsdl'),
            );

            $dpd->setSender($this->webClient->getSender());

            $result = $dpd->sendPackage($this->webClient->getParcels(), $this->webClient->getReceiver(), 'SENDER', $this->webClient->getServices());

            $speedLabel = $dpd->generateSpeedLabelsByPackageIds([$result->packageId], $this->webClient->getPickupAddress(), 'DOMESTIC', $this->labelFileFormat, $this->labelPageFormat, $this->labelType);    /** @phpstan-ignore-line */
        } catch (Exception $exception) {
            $session->getFlashBag()->add('error', sprintf(
                'DPD Web Service for #%s order: %s',
                null !== $shipment->getOrder() ? (string) $shipment->getOrder()->getNumber() : '',
                $exception->getMessage(),
            ));

            return;
        }

        $session->getFlashBag()->add('success', 'bitbag.ui.shipment_data_has_been_exported');
        $this->saveShippingLabel($shippingExport, $speedLabel->filedata, strtolower($this->labelFileFormat));   /** @phpstan-ignore-line */
        $this->markShipmentAsExported($shippingExport, $result->parcels[0]);
    }

    public function saveShippingLabel(
        ShippingExportInterface $shippingExport,
        string $labelContent,
        string $labelExtension,
    ): void {
        $labelPath = $this->shippingLabelsPath
            . '/' . $this->getFilename($shippingExport)
            . '.' . $labelExtension;

        $this->fileSystem->dumpFile($labelPath, $labelContent);
        $shippingExport->setLabelPath($labelPath);

        $this->shippingExportManager->persist($shippingExport);
        $this->shippingExportManager->flush();
    }

    private function getFilename(ShippingExportInterface $shippingExport): string
    {
        $shipment = $shippingExport->getShipment();
        Assert::notNull($shipment);

        $order = $shipment->getOrder();
        Assert::notNull($order);

        $orderNumber = $order->getNumber();

        $shipmentId = $shipment->getId();

        return implode(
            '_',
            [
                $shipmentId,
                preg_replace('~[^A-Za-z0-9]~', '', (string) $orderNumber),
            ],
        );
    }

    private function markShipmentAsExported(ShippingExportInterface $shippingExport, $parcel): void
    {
        $shippingExport->setState(ShippingExportInterface::STATE_EXPORTED);
        $shippingExport->setExportedAt(new \DateTime());
        $shippingExport->setExternalId($parcel->Waybill);

        $this->shippingExportManager->persist($shippingExport);
        $this->shippingExportManager->flush();
    }
}
