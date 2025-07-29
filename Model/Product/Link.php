<?php
namespace Renga\CustomLinkProduct\Model\Product;

class Link extends \Magento\Catalog\Model\Product\Link
{
    const LINK_TYPE_SIMILAR = 10;
    const LINK_TYPE_REPAIR = 11;
    const LINK_TYPE_FUNCTIONAL = 12;

    const LINK_TYPE_SIMILAR_CODE = 'similarlink';
    const LINK_TYPE_REPAIR_CODE = 'repairlink';
    const LINK_TYPE_FUNCTIONAL_CODE = 'functionallink';

    /**
     * @return $this
     */
    public function useSimilarLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_SIMILAR);
        return $this;
    }

    /**
     * @return $this
     */
    public function useRepairLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_REPAIR);
        return $this;
    }

    /**
     * @return $this
     */
    public function useFunctionalLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_FUNCTIONAL);
        return $this;
    }
}
