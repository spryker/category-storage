<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\CategoryStorage;

use Spryker\Client\CategoryStorage\Dependency\Client\CategoryStorageToLocaleClientInterface;
use Spryker\Client\CategoryStorage\Dependency\Client\CategoryStorageToStorageInterface;
use Spryker\Client\CategoryStorage\Dependency\Client\CategoryStorageToStoreClientInterface;
use Spryker\Client\CategoryStorage\Dependency\Service\CategoryStorageToSynchronizationServiceInterface;
use Spryker\Client\CategoryStorage\Expander\ProductCategoryStorageExpander;
use Spryker\Client\CategoryStorage\Expander\ProductCategoryStorageExpanderInterface;
use Spryker\Client\CategoryStorage\Formatter\CategoryTreeFilterFormatter;
use Spryker\Client\CategoryStorage\Formatter\CategoryTreeFilterFormatterInterface;
use Spryker\Client\CategoryStorage\Formatter\CategoryTreeSearchHttpFormatter;
use Spryker\Client\CategoryStorage\Formatter\CategoryTreeSearchHttpFormatterInterface;
use Spryker\Client\CategoryStorage\Mapper\CategoryNodeStorageMapper;
use Spryker\Client\CategoryStorage\Mapper\CategoryNodeStorageMapperInterface;
use Spryker\Client\CategoryStorage\Mapper\UrlStorageCategoryNodeMapper;
use Spryker\Client\CategoryStorage\Mapper\UrlStorageCategoryNodeMapperInterface;
use Spryker\Client\CategoryStorage\Storage\CategoryNodeStorage;
use Spryker\Client\CategoryStorage\Storage\CategoryNodeStorageInterface;
use Spryker\Client\CategoryStorage\Storage\CategoryTreeStorageReader;
use Spryker\Client\CategoryStorage\Storage\CategoryTreeStorageReaderInterface;
use Spryker\Client\Kernel\AbstractFactory;

/**
 * @method \Spryker\Client\CategoryStorage\CategoryStorageConfig getConfig()
 */
class CategoryStorageFactory extends AbstractFactory
{
    /**
     * @return \Spryker\Client\CategoryStorage\Formatter\CategoryTreeFilterFormatterInterface
     */
    public function createCategoryTreeFilterFormatter(): CategoryTreeFilterFormatterInterface
    {
        return new CategoryTreeFilterFormatter(
            $this->createCategoryTreeStorageReader(),
            $this->createCategoryNodeStorageMapper(),
        );
    }

    /**
     * @return \Spryker\Client\CategoryStorage\Storage\CategoryTreeStorageReaderInterface
     */
    public function createCategoryTreeStorageReader(): CategoryTreeStorageReaderInterface
    {
        return new CategoryTreeStorageReader(
            $this->getStorageClient(),
            $this->getSynchronizationService(),
        );
    }

    /**
     * @return \Spryker\Client\CategoryStorage\Mapper\CategoryNodeStorageMapperInterface
     */
    public function createCategoryNodeStorageMapper(): CategoryNodeStorageMapperInterface
    {
        return new CategoryNodeStorageMapper();
    }

    /**
     * @return \Spryker\Client\CategoryStorage\Storage\CategoryNodeStorageInterface
     */
    public function createCategoryNodeStorage(): CategoryNodeStorageInterface
    {
        return new CategoryNodeStorage(
            $this->getStorageClient(),
            $this->getSynchronizationService(),
        );
    }

    /**
     * @return \Spryker\Client\CategoryStorage\Mapper\UrlStorageCategoryNodeMapperInterface
     */
    public function createUrlStorageCategoryNodeMapper(): UrlStorageCategoryNodeMapperInterface
    {
        return new UrlStorageCategoryNodeMapper(
            $this->getSynchronizationService(),
            $this->getStoreClient(),
            $this->getLocaleClient(),
        );
    }

    /**
     * @return \Spryker\Client\CategoryStorage\Expander\ProductCategoryStorageExpanderInterface
     */
    public function createProductCategoryStorageExpander(): ProductCategoryStorageExpanderInterface
    {
        return new ProductCategoryStorageExpander(
            $this->createCategoryNodeStorage(),
        );
    }

    /**
     * @return \Spryker\Client\CategoryStorage\Dependency\Client\CategoryStorageToStorageInterface
     */
    protected function getStorageClient(): CategoryStorageToStorageInterface
    {
        return $this->getProvidedDependency(CategoryStorageDependencyProvider::CLIENT_STORAGE);
    }

    /**
     * @return \Spryker\Client\CategoryStorage\Dependency\Client\CategoryStorageToLocaleClientInterface
     */
    public function getLocaleClient(): CategoryStorageToLocaleClientInterface
    {
        return $this->getProvidedDependency(CategoryStorageDependencyProvider::CLIENT_LOCALE);
    }

    /**
     * @return \Spryker\Client\CategoryStorage\Dependency\Client\CategoryStorageToStoreClientInterface
     */
    public function getStoreClient(): CategoryStorageToStoreClientInterface
    {
        return $this->getProvidedDependency(CategoryStorageDependencyProvider::CLIENT_STORE);
    }

    /**
     * @return \Spryker\Client\CategoryStorage\Dependency\Service\CategoryStorageToSynchronizationServiceInterface
     */
    public function getSynchronizationService(): CategoryStorageToSynchronizationServiceInterface
    {
        return $this->getProvidedDependency(CategoryStorageDependencyProvider::SERVICE_SYNCHRONIZATION);
    }

    /**
     * @return \Spryker\Client\CategoryStorage\Formatter\CategoryTreeSearchHttpFormatterInterface
     */
    public function createCategoryTreeSearchHttpFormatter(): CategoryTreeSearchHttpFormatterInterface
    {
        return new CategoryTreeSearchHttpFormatter(
            $this->createCategoryTreeStorageReader(),
            $this->createCategoryNodeStorageMapper(),
            $this->getLocaleClient(),
            $this->getStoreClient(),
        );
    }
}
