<?php
namespace AdvancedEloquent\Export\Relations;

use lluminate\Database\Eloquent\Relations\BelongsToMany as BaseRelation;
use AdvancedEloquent\Export\Interfaces\Importable;
use AdvancedEloquent\Export\Exceptions\ImportException;

/**
* 
*/
class BelongsToMany extends BaseRelation
{
    /**
     * [import description]
     * @param  Array  $models               [description]
     * @param  array  $additionalAttributes [description]
     * @return [type]                       [description]
     */
    public function import(Array $models, $additionalAttributes = [])
    {
        if ( !($this->related instanceof Importable) ) {
            throw new ImportException(
                trans( 'eloquent-export::import.not_importable', [ 'class' => get_class($this->related) ] )
            );
        }
        foreach ($models as $model) {
            $instance = $this->related->import($model, $additionalAttributes);
            $this->attach($instance->getKey());
        }
    }
}