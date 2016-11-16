<?php
namespace AdvancedEloquent\Export\Relations;

use Illuminate\Database\Eloquent\Relations\BelongsTo as BaseRelation;

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
        $model = $this->related->import($modelAttributes, $additionalAttributes);
        $this->associate($model);
    }
}