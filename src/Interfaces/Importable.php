<?php 
namespace AdvancedEloquent\Export\Interfaces;

/**
 * 
 */
interface Importable {
    /**
     * [exportableAttributes description]
     * @return [type] [description]
     */
    public static function import(Array $model, Array $additionalAttributes = []);
}