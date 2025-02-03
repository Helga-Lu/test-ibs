<?
namespace Noteshop\d7;
use \Bitrix\Main\Entity;
class BrandTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return "noteshop_brand_d7_table";
    }
    public static function getMap()
    {
        return array(
            new Entity\IntegerField(
                "ID",
                array(
                    "primary" => true,
                    "autocomplete" => true,
                )
            ),
            new Entity\StringField(
                "NAME",
                array(
                    "required" => true,
                )
            ),
        );
    }
}
