<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\CategoryStorage\Expander;

use Generated\Shared\Transfer\ProductAbstractCategoryStorageCollectionTransfer;

interface ProductCategoryStorageExpanderInterface
{
    public function expandProductCategoriesWithParentIds(
        ProductAbstractCategoryStorageCollectionTransfer $productAbstractCategoryStorageCollectionTransfer,
        string $localeName,
        string $storeName
    ): ProductAbstractCategoryStorageCollectionTransfer;
}
