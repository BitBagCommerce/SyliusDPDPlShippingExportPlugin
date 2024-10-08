<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\DpdPlShippingExportPlugin\Behat\Page\Admin\ShippingGateway;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    /**
     * @param string $name
     */
    public function selectShippingMethod($name);

    /**
     * @param string $field
     * @param string $option
     */
    public function selectFieldOption($field, $option);

    /**
     * @param string $field
     * @param string $value
     */
    public function fillField($field, $value);

    public function submit();
}
