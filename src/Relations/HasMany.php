<?php
namespace AdvancedEloquent\Export\Relations;

use Illuminate\Database\Eloquent\Relations\HasMany as BaseRelation;
use AdvancedEloquent\Export\Interfaces\Importable;
use AdvancedEloquent\Export\Exceptions\NotImportableException;

/**
* 
*/
class HasMany extends BaseRelation
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
            throw new NotImportableException(
                trans( 'eloquent-export::import.not_importable', [ 'class' => get_class($this->related) ] )
            );
        }
        $attributes = array_merge($additionalAttributes, [$this->getPlainForeignKey() => $this->getParentKey()]);
        foreach ($models as $model) {
            $this->related->import($model, $attributes);
        }
    }
}