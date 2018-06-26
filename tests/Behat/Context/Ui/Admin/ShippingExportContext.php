<?php

namespace Tests\BitBag\DpdPlShippingExportPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Tests\BitBag\DpdPlShippingExportPlugin\Behat\Mocker\DPDApiMocker;
use Tests\BitBag\SyliusShippingExportPlugin\Behat\Page\Admin\ShippingExport\IndexPageInterface;

final class ShippingExportContext implements Context
{
    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var DPDApiMocker
     */
    private $DPDApiMocker;

    /**
     * @param IndexPageInterface $indexPage
     * @param DPDApiMocker $DPDApiMocker
     */
    public function __construct(
        IndexPageInterface $indexPage,
        DPDApiMocker $DPDApiMocker
    )
    {
        $this->DPDApiMocker = $DPDApiMocker;
        $this->indexPage = $indexPage;
    }

    /**
     * @When I export all new shipments to dpd api
     */
    public function iExportAllNewShipments()
    {
        $this->DPDApiMocker->performActionInApiSuccessfulScope(function () {
            $this->indexPage->exportAllShipments();
        });
    }

    /**
     * @When I export first shipment to dpd api
     */
    public function iExportFirsShipments()
    {
        $this->DPDApiMocker->performActionInApiSuccessfulScope(function () {
            $this->indexPage->exportFirsShipment();
        });
    }
}
