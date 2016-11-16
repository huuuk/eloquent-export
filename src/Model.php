<?php 
namespace AdvancedEloquent\Export;

use Illuminate\Database\Eloquent\Model as BaseEloquentModel;
use AdvancedEloquent\Export\Traits\ExportTrait;
use AdvancedEloquent\Export\Interfaces\ExportableInterface;

/**
 * 
 */
class Model extends BaseEloquentModel implements ExportableInterface
{
    use ExportTrait;
}