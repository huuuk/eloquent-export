<?php 
namespace AdvancedEloquent\Export;

use Illuminate\Database\Eloquent\Collection as BaseEloquentCollection;
use AdvancedEloquent\Export\Traits\ExportCollection;
use AdvancedEloquent\Export\Interfaces\Exportable;

/**
 * 
 */
class Collection extends BaseEloquentCollection implements Exportable
{
    use ExportCollection;
}