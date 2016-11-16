<?php
namespace AdvancedEloquent\Export\Relations;

use Illuminate\Database\Eloquent\Relations\HasMany as BaseRelation;

/**
* 
*/
class HasMany extends BaseRelation;
{
    /**
     * [import description]
     * @param  Array  $models               [description]
     * @param  array  $additionalAttributes [description]
     * @return [type]                       [description]
     */
    public function import(Array $models, $additionalAttributes = [])
    {
        $attributes = array_merge($additionalAttributes, [$this->getPlainForeignKey() => $this->getParentKey()]);
        foreach ($models as $model) {
            $this->related->import($model, $attributes);
        }
    }
}