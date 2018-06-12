<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CategoryStorage;

use Spryker\Zed\Kernel\AbstractBundleConfig;

class CategoryStorageConfig extends AbstractBundleConfig
{
    /**
     * @return bool
     */
    public function isSendingToQueue()
    {
        return true;
    }

    /**
     * @return string|null
     */
    public function getCategoryTreeSynchronizationPoolName()
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getCategoryNodeSynchronizationPoolName()
    {
        return null;
    }
}
