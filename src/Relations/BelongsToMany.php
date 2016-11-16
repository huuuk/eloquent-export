<?php
namespace AdvancedEloquent\Export\Relations;

use lluminate\Database\Eloquent\Relations\BelongsToMany as BaseRelation;

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
        foreach ($models as $model) {
            $instance = $this->related->import($model, $additionalAttributes);
            $this->attach($instance->getKey());
        }
    }
}