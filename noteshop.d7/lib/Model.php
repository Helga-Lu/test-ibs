<?
namespace Noteshop\d7;
use \Bitrix\Main\Entity;
class ModelTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return "noteshop_model_d7_table";
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
            new Entity\IntegerField(
                "BRAND_ID"
            ),
            new Entity\ReferenceField(
                "BRAND",
                '\Noteshop\d7\BrandTable',
                array("=this.BRAND_ID" => "ref.ID"),
				array('join_type' => 'LEFT')
            ),
        );
    }
}
