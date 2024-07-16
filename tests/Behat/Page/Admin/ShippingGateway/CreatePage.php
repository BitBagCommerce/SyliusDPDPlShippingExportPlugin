<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\DpdPlShippingExportPlugin\Behat\Page\Admin\ShippingGateway;

use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Tests\BitBag\SyliusShippingExportPlugin\Behat\Behaviour\ContainsError;

final class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use ContainsError;

    /**
     * @inheritdoc
     */
    public function selectShippingMethod($name)
    {
        $this->getDocument()->selectFieldOption('Shipping method', $name);
    }

    /**
     * @inheritdoc
     */
    public function selectFieldOption($field, $option)
    {
        $this->getDocument()->selectFieldOption($field, $option);
    }

    /**
     * @inheritdoc
     */
    public function fillField($field, $value)
    {
        $this->getDocument()->fillField($field, $value);
    }

    /**
     * @inheritdoc
     */
    public function submit()
    {
        $this->create();
    }
}
