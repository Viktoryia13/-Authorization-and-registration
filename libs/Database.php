<?php
class Database
{

    public static function getTable($name)
    {
        // Устанавливаем соединение
        $table = simplexml_load_file(DB_PATH . $name . ".xml");
        return $table;
    }

    /**
   * Сохранение данных в таблицу c названием
   * @param object $data (SimpleXMLElement)
   * @param string $name
   */
    public static function saveTable($data, $table)
    {
        $data->asXML(DB_PATH . $table . ".xml");
    }
}
