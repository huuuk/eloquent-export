<?php
namespace AdvancedEloquent\Export\Traits;

use AdvancedEloquent\Export\Interfaces\Exportable;

trait ExportCollection
{
    public function export()
    {
        return array_map(function($value)
        {
            return $value instanceof Exportable ? $value->export() : $value;

        }, $this->items);
    }
}