<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\DpdPlShippingExportPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Tests\BitBag\DpdPlShippingExportPlugin\Behat\Mocker\DPDApiMocker;
use Tests\BitBag\SyliusShippingExportPlugin\Behat\Page\Admin\ShippingExport\IndexPageInterface;

final class ShippingExportContext implements Context
{
    /** @var IndexPageInterface */
    private $indexPage;

    /** @var DPDApiMocker */
    private $DPDApiMocker;

    public function __construct(
        IndexPageInterface $indexPage,
        DPDApiMocker $DPDApiMocker,
    ) {
        $this->DPDApiMocker = $DPDApiMocker;
        $this->indexPage = $indexPage;
    }

    /**
     * @When I export all new shipments to dpd api
     */
    public function iExportAllNewShipments()
    {
        $this->DPDApiMocker->performActionInApiSuccessfulScope(function (): void {
            $this->indexPage->exportAllShipments();
        });
    }

    /**
     * @When I export first shipment to dpd api
     */
    public function iExportFirsShipments()
    {
        $this->DPDApiMocker->performActionInApiSuccessfulScope(function (): void {
            $this->indexPage->exportFirsShipment();
        });
    }
}
