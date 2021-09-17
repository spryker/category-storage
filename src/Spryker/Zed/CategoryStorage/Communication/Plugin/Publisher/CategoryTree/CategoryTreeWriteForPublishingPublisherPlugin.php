<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CategoryStorage\Communication\Plugin\Publisher\CategoryTree;

use Spryker\Shared\CategoryStorage\CategoryStorageConfig;
use Spryker\Shared\CategoryStorage\CategoryStorageConstants;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\PublisherExtension\Dependency\Plugin\PublisherPluginInterface;

/**
 * @method \Spryker\Zed\CategoryStorage\Business\CategoryStorageFacadeInterface getFacade()
 * @method \Spryker\Zed\CategoryStorage\Persistence\CategoryStorageQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\CategoryStorage\Communication\CategoryStorageCommunicationFactory getFactory()
 * @method \Spryker\Zed\CategoryStorage\CategoryStorageConfig getConfig()
 */
class CategoryTreeWriteForPublishingPublisherPlugin extends AbstractPlugin implements PublisherPluginInterface
{
    /**
     * {@inheritDoc}
     * - Publishes category tree data by `CategoryStore` publish event.
     * - Publishes category tree data by `CategoryTree` publish events.
     * - Publishes category tree data after (create/update/delete) publish events.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     * @param string $eventName
     *
     * @return void
     */
    public function handleBulk(array $eventEntityTransfers, $eventName)
    {
        $this->getFacade()->writeCategoryTreeStorageCollection();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return array<string>
     */
    public function getSubscribedEvents(): array
    {
        return [
            CategoryStorageConstants::CATEGORY_STORE_PUBLISH,
            CategoryStorageConstants::CATEGORY_STORE_UNPUBLISH,
            CategoryStorageConfig::CATEGORY_TREE_PUBLISH,
            CategoryStorageConfig::CATEGORY_AFTER_PUBLISH_CREATE,
            CategoryStorageConfig::CATEGORY_AFTER_PUBLISH_UPDATE,
            CategoryStorageConfig::CATEGORY_AFTER_PUBLISH_DELETE,
        ];
    }
}
