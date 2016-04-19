<?php
    require_once "config.php";

     class DataBase{

     	private $mysqli;// переменная  для подключения к бд

        // подключение к бд выполняется в конструкторе, т.е при создании экземляра класса 
     	function __construct(){

            $this->mysqli = new mysqli($GLOBALS["host"], $GLOBALS["user"],$GLOBALS["password"],$GLOBALS["db"]);
            $this->mysqli->query("SET NAMES 'utf8'");  

            if ($this->mysqli->connect_errno) // обработка ошибок подключения к бд
            { 
                $this->writeLog("Connect failed:".$mysqli->connect_error."\n");// использование функции записи в файл,в данном случае записываются ошибки
                exit();
            } 
        }

        // функция запроса
        public function select($table_name, $fields, $where = ""){

            $table_name = "`".$table_name."`";  // названия полей, таблиц, бд в mysql практикуется заключать в апострофы 

            //перебор полей заданных  пользователем
            for($i = 0; $i < count($fields); $i++)
                if ((strpos($fields[$i], "(")===false) && ($fields[$i] != "*")) $fields[$i] = "`".$fields[$i]."`";// если нет символов ( и * заключаем в апострофы   

            $fields = implode("," , $fields);// преобразуем массив в строку 

            // формирование sql запроса.
            if($where) $query = "SELECT $fields FROM $table_name WHERE $where";
            else $query = "SELECT $fields FROM $table_name";

            $info = "Дата - ".date("d-m-Y")."\nВремя - ".date("d-m-Y")."\nЗапрос - ".$query."\n";
            $this->writeLog($info); // запись в файл даты,  времени и запроса, вне зависимости от результата его выполнения

            $result_set = $this->mysqli->query($query);

            // если запрос не верен ошибку выводим в файл
            if (!$result_set) { 
                $this->writeLog("Ошибка запроса. Будьте внимательнее\n"); 
                return false;
            } 

            // формирование массива с данными, вернушимися в качестве ответа, пользователю на его запрос
            $i = 0;
            while ($row = $result_set->fetch_assoc()){
                $data[$i] = $row;
                $i++;
            } 

            $result_set->close();

            return $data;    
        }

        private function writeLog($info){

            $file = fopen("log.txt", "a+t"); // открытие файла log.txt
            fwrite($file, iconv("UTF-8", "WINDOWS-1251",  $info)); // запись в файл строки из параметра функции,iconv-для борьбы с кракозябрами
            fclose($file); // закрытие файла
        }

        // в деструкторе отключемся от mysql
        function __destruct(){
            if($this->mysqli) $this->mysqli->close(); 
        } 
?>