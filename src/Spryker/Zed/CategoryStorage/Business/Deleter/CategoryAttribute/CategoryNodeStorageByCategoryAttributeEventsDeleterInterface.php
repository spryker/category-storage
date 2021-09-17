<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CategoryStorage\Business\Deleter\CategoryAttribute;

interface CategoryNodeStorageByCategoryAttributeEventsDeleterInterface
{
    /**
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function deleteCategoryNodeStorageCollectionByCategoryAttributeEvents(array $eventEntityTransfers): void;
}
