<?php
namespace AdvancedEloquent\Export\Relations;

use Illuminate\Database\Eloquent\Relations\BelongsTo as BaseRelation;
use AdvancedEloquent\Export\Interfaces\Importable;
use AdvancedEloquent\Export\Exceptions\NotImportableException;

/**
* 
*/
class BelongsTo extends BaseRelation
{
    /**
     * [import description]
     * @param  Array  $modelAttributes      [description]
     * @param  [type] $additionalAttributes [description]
     * @return [type]                       [description]
     */
    public function import(Array $modelAttributes, $additionalAttributes)
    {
        dd($this->related);
        if ( !($this->related instanceof Importable) ) {
            throw new NotImportableException(
                trans( 'eloquent-export::import.not_importable', [ 'class' => get_class($this->related) ] )
            );
        }
        $model = $this->related->import($modelAttributes, $additionalAttributes);
        $this->associate($model);
    }
}