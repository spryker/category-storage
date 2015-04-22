<?php

namespace SprykerFeature\Zed\ProductSearch\Business;

use SprykerEngine\Zed\Kernel\Business\AbstractFacade;
use SprykerEngine\Shared\Kernel\Messenger\MessengerInterface;

/**
 * @method ProductSearchDependencyContainer getDependencyContainer()
 */
class ProductSearchFacade extends AbstractFacade
{
    /**
     * @param array $productsRaw
     * @param array $processedProducts
     *
     * @return array
     */
    public function enrichProductsWithSearchAttributes(array $productsRaw, array $processedProducts)
    {
        return $this->getDependencyContainer()
            ->getProductAttributesTransformer()
            ->buildProductAttributes($productsRaw, $processedProducts);
    }

    /**
     * @param array  $productsRaw
     * @param array  $processedProducts
     * @param string $locale
     *
     * @return array
     */
    public function createSearchProducts(array $productsRaw, array $processedProducts, $locale)
    {
        return $this->getDependencyContainer()
            ->getProductSearchProcessor()
            ->buildProducts($productsRaw, $processedProducts, $locale);
    }

    /**
     * @param MessengerInterface $messenger
     */
    public function install(MessengerInterface $messenger)
    {
        $this->getDependencyContainer()->getInstaller($messenger)->install();
    }
}
