<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Search;

use SprykerFeature\Zed\ProductSearch\Communication\Plugin\Installer;
use SprykerEngine\Zed\Kernel\AbstractBundleConfig;
use SprykerFeature\Shared\Application\ApplicationConstants;
use SprykerFeature\Zed\Installer\Communication\Plugin\AbstractInstallerPlugin;

class SearchConfig extends AbstractBundleConfig
{

    /**
     * @return AbstractInstallerPlugin[]
     */
    public function getInstaller()
    {
        return [
            new Installer(),
        ];
    }

    /**
     * @return string
     */
    public function getElasticaDocumentType()
    {
        return $this->get(ApplicationConstants::ELASTICA_PARAMETER__DOCUMENT_TYPE);
    }

}
