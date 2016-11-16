<?php 
namespace AdvancedEloquent\Export\Traits;

use AdvancedEloquent\Export\Model;
// use Illuminate\Database\Eloquent\Model;
use AdvancedEloquent\Export\Interfaces\ExportableInterface;
use AdvancedEloquent\Export\Relations\BelongsTo;
use AdvancedEloquent\Export\Relations\BelongsToMany;
use AdvancedEloquent\Export\Relations\HasMany;
use AdvancedEloquent\Export\Collection;
// use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Exception;
use ReflectionClass;

trait ExportTrait {

    /**
     * [exportableAttributes description]
     * @return [type] [description]
     */
    protected function exportableAttributes()
    {
        return [];
    }

    /**
     * [export description]
     * @param  boolean $withChildrenRealtion [description]
     * @return [type]                        [description]
     */
    public function export($withChildrenRealtion  = true)
    {
        $exportData = [
            'class'        => (new ReflectionClass($this))->getName(),
            'dependencies' => [],
            'attributes'   => [],
            'relations'    => [],
        ];

        $attributes = $this->exportableAttributes();

        foreach ($attributes as $attributeName) {

            $attribute = $this->getAttribute($attributeName);
            if ($attribute instanceof Model
                // && $attribute instanceof ExportableInterface
                ) {
                $exportData['dependencies'][$attributeName] = $attribute->export();
            }
            elseif ($attribute instanceof Collection &&
                    // $attribute instanceof ExportableInterface && 
                    $withChildrenRealtion) {
                $exportData['relations'][$attributeName] = $attribute->export();
            }
            elseif(!is_object($attribute)) {
                $exportData['attributes'][$attributeName] = $attribute;
            }
        }
        return $exportData;
    }


    /**
     * [import description]
     * @param  Array  $model                [description]
     * @param  array  $additionalAttributes [description]
     * @return [type]                       [description]
     */
    public static function import(Array $model, $additionalAttributes = [])
    {
        $_this = new static;

        if ( !isset($model['class']) || !($_this instanceof $model['class']) )
            return false;

        $foreignKeys = [];

        // Для начала имортируем все зависимости объекта и собирем внешние ключи
        // чтоб потом правильно определить создавать объект заново
        // или использовать существующий
        foreach ($model['dependencies'] as $attributeName => $attributeValue) {

            // Определяем класс зависимости, естествено с проверкой интерфейса
            $reflection = isset($attributeValue['class']) ? new ReflectionClass($attributeValue['class']) : null;

            if ( $reflection && $reflection->implementsInterface(ExportableInterface::class) ) {

                $dependency = $reflection->newInstance()->import($attributeValue, $additionalAttributes);

                // Сохраняем полученный ключ, для подготовки атрибутов модели
                $foreignKeys[ $_this->$attributeName()->getForeignKey() ] = $dependency->id;
            }
        }

        // Склеиваем собственные аттрибуты модели, полученные внешние ключи,
        // дополнительные атрибуты и смотрим есть ли такой объект в БД
        $attributes = array_merge(
                $foreignKeys,
                $_this->filterAdditionalAttributes($additionalAttributes),
                $_this->filterAdditionalAttributes($model['attributes'])
            );

        $instance = static::firstOrCreate($attributes);

        // Импортируем дочерние объекты
        foreach ($model['relations'] as $attributeName => $attributeValue) {
            $instance->$attributeName()->import($attributeValue, $additionalAttributes);
        }

        return $instance;
    }

    /**
     * [filterAdditionalAttributes description]
     * @param  Array  $attributes [description]
     * @return [type]             [description]
     */
    protected function filterAdditionalAttributes(Array $attributes)
    {
        $filteredKeys = array_intersect( array_keys($attributes), $this->getFillable() );
        return array_only($attributes, $filteredKeys);
    }

    /**
     * [newCollection description]
     * @param  array  $models [description]
     * @return [type]         [description]
     */
    public function newCollection(array $models = array())
    {
       return new Collection($models);
    }

    /**
     * Define an inverse one-to-one or many relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $otherKey
     * @param  string  $relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function belongsTo($related, $foreignKey = null, $otherKey = null, $relation = null)
    {
        // If no relation name was given, we will use this debug backtrace to extract
        // the calling method's name and use that as the relationship name as most
        // of the time this will be what we desire to use for the relationships.
        if (is_null($relation)) {
            list($current, $caller) = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

            $relation = $caller['function'];
        }

        // If no foreign key was supplied, we can use a backtrace to guess the proper
        // foreign key name by using the name of the relationship function, which
        // when combined with an "_id" should conventionally match the columns.
        if (is_null($foreignKey)) {
            $foreignKey = Str::snake($relation).'_id';
        }

        $instance = new $related;

        // Once we have the foreign key names, we'll just create a new Eloquent query
        // for the related models and returns the relationship instance which will
        // actually be responsible for retrieving and hydrating every relations.
        $query = $instance->newQuery();

        $otherKey = $otherKey ?: $instance->getKeyName();

        return new BelongsTo($query, $this, $foreignKey, $otherKey, $relation);
    }

    /**
     * Define a one-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $instance = new $related;

        $localKey = $localKey ?: $this->getKeyName();

        return new HasMany($instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey);
    }

    /**
     * Define a many-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $table
     * @param  string  $foreignKey
     * @param  string  $otherKey
     * @param  string  $relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function belongsToMany($related, $table = null, $foreignKey = null, $otherKey = null, $relation = null)
    {
        // If no relationship name was passed, we will pull backtraces to get the
        // name of the calling function. We will use that function name as the
        // title of this relation since that is a great convention to apply.
        if (is_null($relation)) {
            $relation = $this->getBelongsToManyCaller();
        }

        // First, we'll need to determine the foreign key and "other key" for the
        // relationship. Once we have determined the keys we'll make the query
        // instances as well as the relationship instances we need for this.
        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $instance = new $related;

        $otherKey = $otherKey ?: $instance->getForeignKey();

        // If no table name was provided, we can guess it by concatenating the two
        // models using underscores in alphabetical order. The two model names
        // are transformed to snake case from their default CamelCase also.
        if (is_null($table)) {
            $table = $this->joiningTable($related);
        }

        // Now we're ready to create a new query builder for the related model and
        // the relationship instances for the relation. The relations will set
        // appropriate query constraint and entirely manages the hydrations.
        $query = $instance->newQuery();

        return new BelongsToMany($query, $this, $table, $foreignKey, $otherKey, $relation);
    }

}