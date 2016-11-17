# Импорт/экспорт Eloquent моделей
Пакет позовляет производить рекурсивный импорт/экспорт eloquent объектов
## Установка
via composer
```
"required" : {
    ...
    "huuuk/eloquent-export": "^1.0",
    ...
},
```
добавте провайдер
```php
'porviders' => [
    // ...
    'AdvancedEloquent\Export\ExportServiceProvider',
    // ...
],
```

## Использование
Замените родительский класс модели которую хотите экспорировать
```php
namespace App;

use AdvancedEloquent\Export\Model;
// use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'name',
        'age',
        'city_id',
    ];
    
    public function city()
    {
          return $this->belongsTo('App\City');
    }
    
    public function posts()
    {
          return $this->hasMany('App\Post');
    }
    
    public function roles()
    {
          return $this->belongsToMany('App\Role');
    }
}
```
Определите метод `exportableAttributes` в этой модели, он должнен возвращать атрибуты которые вы хотите экспортировать.
Если вы хотите экспортировать отношения модели, просто укажите название метода, котрый возвращает это отношение.
#### На данный момент поддерживаются следующие виды отношений
* BelongsTo
* HasMany
* BelongsToMany

```php
class User extends Model
{
   // ...
    protected function exportableAttributes()
    {
          return [
              'name',
              'age',
              'city',
              'posts',
              'roles',
          ];
    }
    // ...
}
```
И не забудте заменить родительский класс этих моделей.
```php
namespace App;

use AdvancedEloquent\Export\Model;
// use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'name',
        'population',
    ];
    
    protected function exportableAttributes()
    {
          return [
              'name',
              'population',
          ];
    }
}
```
```php
namespace App;

use AdvancedEloquent\Export\Model;
// use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'published_at',
        'user_id',
    ];
    
    protected function exportableAttributes()
    {
          return [
               'title',
               'published_at',
          ];
    }
}
```
```php
namespace App;

use AdvancedEloquent\Export\Model;
// use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'permission',
    ];
    
    protected function exportableAttributes()
    {
          return [
              'name',
              'permission',
          ];
    }
    
    public function users()
    {
          return $this->belongsToMany('App\User');
    }
}
```
Тперь вы можете экспортировать и импортировать как корорль
```php
$user = User::findOrFail($id);
$json = $user->export();

// ....

$user = User::import($json);
```
## Настройка
Пакет содержит конфигурационные файлы, языковые файлы и представления
Чтобы их переопределить просто опубликуйте их
```bash
# config
php artisan vendor:publish --provider="AdvancedEloquent\Export\ExportServiceProvider" --tag="config"
# view
php artisan vendor:publish --provider="AdvancedEloquent\Export\ExportServiceProvider" --tag="views"
# lang
php artisan vendor:publish --provider="AdvancedEloquent\Export\ExportServiceProvider" --tag="lang"
```
Также пакет содержит роуты для импорта экспорта объектов, на которые можно ссылаться так:
```php
<a href="{{route('export', ['type' => get_class($unit), 'id' => $unit->id])}}" class="btn btn-default">
```
и импорт(с помощью встроенной формы)
```php
@include('eloquent-export::import-form',
    [
        // указываются поддерживаемые типы(классы) объектов, например,
        // чтобы в форму импорта структурных подразделений не импортировали
        // другой класс, например  объекты информатизации.
        // если параметр не указан, или пустой , то поддерживаются все типы
        'supportedTypes' => [ App\Unit::class ], 
        // дополнительные атрибуты импортируемого объекта
        // необязательный параметр
        'additionalAttributes' => [ 'parent_id' => $patrentUnit->id ],
        // url на который будет перенаправляться пользователь в случае
        // успешного импорта, если не указан то используется redirect()->back()
        // в обоих случаях при успешном импорте в ссесию записывается
        // переменная success_import = true
        'successRedirect' => route('units.edit', ['id' => $parentId->id]),
    ])
```
Встроенные роуты можно отклчить в `config/export.php`
```php
<?php 
return [
    'builtin_routes' => false,
];
```
#### В след версиях планируется поддержка
* HasOne
* MorphTo
* MorphMany
* MorphToMany