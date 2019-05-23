<?php

namespace app\common\command\make;

use think\console\command\Make;
use think\facade\App;

class Modelx extends Make
{

    const SPACE_SEPARATOR   = " ";
    const NEW_LINE          = "\r\n";

    protected $type = "Modelx";

    protected function configure()
    {
        parent::configure();
        $this->setName('make:modelx')
            ->setDescription('Create a new model class with property doc');
    }

    protected function getStub()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'modelx.stub';
    }

    protected function buildClass($name)
    {
        $stub = file_get_contents($this->getStub());

        $namespace = trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');

        $class = str_replace($namespace . '\\', '', $name);

        $modelPropertyDoc = $this->setModelPropertyDoc($this->getTableName($class), $namespace, $class);

        return str_replace(['{%className%}', '{%namespace%}', '{%app_namespace%}', '{%modelPropertyDoc%}'], [
            $class,
            $namespace,
            App::getNamespace(),
            $modelPropertyDoc,
        ], $stub);

    }

    protected function getNamespace($appNamespace, $module)
    {
        return parent::getNamespace($appNamespace, $module) . '\model';
    }

    private function getTableName($className = "")
    {
        if(empty($className)) return false;
        $prefix = "";
        if(!empty(config('database.prefix'))) $prefix = config('database.prefix');
        $tbName = $prefix . $this->unCamelize($className);
        return $tbName;
    }

    private function setModelPropertyDoc($tableName = "", $nameSpace = "", $className = "")
    {
        if(!$this->isTableExist($tableName)) {
            throw new \think\Exception('Data table you want generate is not exists', 90006);
        }
        $fields_arr = $this->getAllFieldsName($tableName);
        $start = '/**' . self::NEW_LINE;
        $start .= ' * class ' . $className . self::NEW_LINE;
        $start .= ' * @package ' . $nameSpace . self::NEW_LINE;
        $content_pre = ' * @property ';
        $end = ' */' . self::NEW_LINE;
        $content = "";
        $content .= $start;
        foreach ($fields_arr as $val) {
            $content .= $content_pre . self::SPACE_SEPARATOR;
            $content .= $this->replaceFieldsType($this->getAllFieldsType($tableName, $val));
            $content .= self::SPACE_SEPARATOR . $val . self::NEW_LINE;
        }
        $content .= $end;
        return $content;
    }

    private function getAllFieldsName($tableName = "")
    {
        return db()->getTableFields($tableName);
    }

    private function getAllFieldsType($tableName = "", $fieldName = "")
    {
        return db()->getFieldsType($tableName, $fieldName);
    }

    private function isTableExist($tableName = "")
    {
        return !empty(db()->query('SHOW TABLES LIKE '."'". $tableName . "'"));
    }

    private function replaceFieldsType($type = "")
    {
        $type = strtolower($type);
        $mapArr = ["bool", "boolean"];
        $replaceType = $this->mapReplace($mapArr, $type, "bool");
        if(!empty($replaceType)) return $replaceType;
        $mapArr = ["int", "bit", "serial"];
        $replaceType = $this->mapReplace($mapArr, $type, "int");
        if(!empty($replaceType)) return $replaceType;
        $mapArr = ["decimal", "float", "fixed", "double", "numeric"];
        $replaceType = $this->mapReplace($mapArr, $type, "float");
        if(!empty($replaceType)) return $replaceType;
        return "string";
    }

    private function mapReplace($mapArr = [], $type = "", $replaceType = "")
    {
        foreach($mapArr as $map) {
            $pos = strpos($type, $map);
            if(false !== $pos) return $replaceType;
        }
        return false;
    }

    private function unCamelize($camelCaps,$separator='_')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }
}
