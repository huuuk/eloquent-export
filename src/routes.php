<?php
Route::group([ 'middleware' => ['auth'] ], function() {
    Route::get('export', ['as' => 'export', 'uses' => 'AdvancedEloquent\Export\ExportController@getExport']);
    Route::post('import', ['as' => 'import', 'uses' => 'AdvancedEloquent\Export\ExportController@postImport']);
});