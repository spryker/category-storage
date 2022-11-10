<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\CategoryStorage\Communication\Plugin\Publisher;

use Codeception\Test\Unit;
use Spryker\Zed\CategoryStorage\Communication\Plugin\Publisher\CategoryTreePublisherTriggerPlugin;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group CategoryStorage
 * @group Communication
 * @group Plugin
 * @group Publisher
 * @group CategoryStoragePublisherTest
 * Add your own group annotations below this line
 */
class CategoryStoragePublisherTest extends Unit
{
    /**
     * @return void
     */
    public function testCategoryTreePublisherTriggerPluginGetDataWithZeroOffset(): void
    {
        // Arrange
        $plugin = new CategoryTreePublisherTriggerPlugin();

        // Act
        $resultData = $plugin->getData(0, rand());

        // Assert
        $this->assertNotEmpty($resultData);
    }

    /**
     * @return void
     */
    public function testCategoryTreePublisherTriggerPluginGetDataWithNotZeroOffset(): void
    {
        // Arrange
        $plugin = new CategoryTreePublisherTriggerPlugin();

        // Act
        $resultData = $plugin->getData(1, rand());

        // Assert
        $this->assertEmpty($resultData);
    }
}
