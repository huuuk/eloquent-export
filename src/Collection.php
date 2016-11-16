<?php 
namespace AdvancedEloquent\Export;

use Illuminate\Database\Eloquent\Collection as BaseEloquentCollection;
use AdvancedEloquent\Export\Interfaces\ExportableInterface;

/**
 * 
 */
class Collection extends BaseEloquentCollection implements ExportableInterface {

    public function export()
    {
        return array_map(function($value)
        {
            return $value instanceof ExportableInterface ? $value->export() : $value;

        }, $this->items);
    }
}