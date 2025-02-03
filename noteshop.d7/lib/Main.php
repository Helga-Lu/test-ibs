<?
namespace Noteshop\d7;
use \Noteshop\d7\DataTable;
use \Bitrix\Main\Entity\Event;
class Main
{
    public static function get()
    {
        $result = DataTable::getList(
            array(
                'select' => array('*')
            )
        );
        $row = $result->fetch();
        print "<pre>";
        print_r($row);
        print "</pre>";
        return $result;
    }
}
