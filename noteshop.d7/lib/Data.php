<?
namespace Noteshop\d7;
use \Bitrix\Main\Entity;
use \Bitrix\Main\Application;
class DataTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return "noteshop_d7_table";
    }
    public static function getConnectionName()
    {
        return "default";
    }
    public static function getMap()
    {
        return array(
            // ID
            new Entity\IntegerField(
                "ID",
                array(
                    "primary" => true,
                    "autocomplete" => true,
                )
            ),
            // name
            new Entity\StringField(
                "NAME",
                array(
                    "required" => true,
                )
            ),
            // year
            new Entity\IntegerField(
                "YEAR",
                array(
                    "required" => true,
                )
            ),
            // price
            new Entity\IntegerField(
                "PRICE",
                array(
                    "required" => true,
                )
            ),
            new Entity\IntegerField(
                "MODEL_ID"
            ),
            new Entity\ReferenceField(
                "MODEL",
                '\Noteshop\d7\ModelTable',
                array("=this.MODEL_ID" => "ref.ID")
            ),
            // ссылка на картинку
            new Entity\StringField(
                "LINK_PICTURE",
                array(
                    "column_name" => "LINK_PICTURE_CODE",
                    "validation" => function () {
                        return array(
							//new Entity\Validator\Unique,
                            function ($value, $primary, $row, $field) {
                                if (strlen($value) <= 100)
                                    return true;
                                else
                                    return "Код LINK_PICTURE должен содержать не более 100 символов";
                            }
                        );
                    }
                )
            ),
            new Entity\StringField(
                "ALT_PICTURE",
                array(
                    "required" => false,
                )
            ),

        );
    }
}