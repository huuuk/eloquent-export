<?php 
namespace AdvancedEloquent\Export;

use Illuminate\Database\Eloquent\Model as BaseEloquentModel;
use AdvancedEloquent\Export\Traits\ExportAndImport;
use AdvancedEloquent\Export\Interfaces\Exportable;
use AdvancedEloquent\Export\Interfaces\Importable;

/**
 * 
 */
abstract class Model extends BaseEloquentModel implements Exportable, Importable
{
    use ExportAndImport;
}