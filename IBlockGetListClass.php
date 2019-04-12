<?php
/**
 * Created by PhpStorm.
 * User: Shand
 * Date: 4/12/2019
 * Time: 2:52 PM
 */

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;

class IBlockGetListClass
{
    /**
     * @var $localCache bool
     * @var $cacheTime int
     * @var $clearCache bool
     * @var $cacheID array
     */

    public $localCache = true;
    public $cacheTime = 3600;
    public $clearCache = false;
    public $cacheID;

    public function getList($arOrder, $arFilter, $arSelect)
    {
        if (CModule::IncludeModule('iblock')) {

            $res = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
            $arResult = Array();

            while ($result = $res->Fetch()) {
                $arResult[] = $result;
            }

            return $arResult;
        }

        return false;
    }

    public function getCashedList(array $arOrder = Array("SORT" => "ASC"), $arFilter = Array(), array $arSelect = Array("ID", "IBLOCK_ID", "NAME"))
    {
        if ($this->localCache) {

            $cacheParams['order'] = $arOrder;
            $cacheParams['filter'] = $arFilter;
            $cacheParams['group'] = false;
            $cacheParams['nav'] = false;
            $cacheParams['select'] = $arSelect;

            $this->cacheID = md5(serialize($cacheParams));

            $obCache = \Bitrix\Main\Data\Cache::createInstance();

            if ($obCache->initCache($this->cacheTime, $this->cacheID, "/")) {
                $arResult = $obCache->GetVars();
            } else {
                $arResult = $this->getList($arOrder, $arFilter, $arSelect);
            }

            if ($obCache->startDataCache() && !$this->clearCache) {
                $obCache->endDataCache($arResult);
            }

            if ($this->clearCache) {
                $obCache->clean($this->cacheID);
            }

            return $arResult;

        } else {
            return $this->getList($arOrder, $arFilter, $arSelect);
        }
    }

}