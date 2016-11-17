<?php
namespace AdvancedEloquent\Export;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use AdvancedEloquent\Export\Exceptions\ImportException;
use AdvancedEloquent\Export\Exceptions\NotImportableException;
use ReflectionClass;

class ExportController extends Controller
{
    
    public function getExport(Request $request)
    {
        $reflection = new ReflectionClass( $request->get('type') );
        if ( $reflection->implementsInterface('AdvancedEloquent\Export\Interfaces\Exportable')) {
            $object = $reflection->newInstance()->findOrFail($request->get('id'));
            // dd($object->export());
            $fileName = sys_get_temp_dir() . DIRECTORY_SEPARATOR . time() . '.json';
            $fp = fopen($fileName, 'w');
            fwrite($fp, json_encode( $object->export(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            fclose($fp);
            $userFileName = strtolower($reflection->getShortName()).'_'.$object->id.'_'.date('d-m-Y_H:i:s').'.json';
            return response()->download($fileName, $userFileName);
        }
        return;
    }

    public function postImport(Request $request)
    {
        // Если нет файла, то нет импорта
        if ( !$request->hasFile('import_file') || !$request->file('import_file')->isValid() ) {
            throw new ImportException(trans('eloquent-export::import.no_file'));
            // return "No file.";
        }

        $data = json_decode(file_get_contents( $request->file('import_file')->getRealPath() ), true);
        
        // Если не указан класс, то нет импорта
        if(!array_key_exists('class', $data)) {
            throw new ImportException(trans('eloquent-export::import.no_classname'));
            // return 'Invalid file format, no class.';
        }

        // Смотрим поддерживаемые классы, если переменная пустая, то поддерживаются
        // все классы, если же нет, то проверяем совпадает с тем что в файле импорта
        $supportedTypes = $request->get('supported_types', []);
        if( count($supportedTypes) > 0 && !in_array($data['class'], $supportedTypes) ) {
            throw new ImportException(trans('eloquent-export::import.wrong_type'));
            // return 'Not supported object type.';
        }

        // Проверяем если класс поддерживет импорт/экспорт
        $reflection = new ReflectionClass( $data['class'] );
        if ( !$reflection->implementsInterface('AdvancedEloquent\Export\Interfaces\Importable')) {
            throw new NotImportableException(trans('eloquent-export::import.not_importable', ['class' => $data['class']]));
            // return 'Presented class don\'t support import/export operations.';
        }

        $additionalAttributes = $request->get('additional_attributes', []);

        // Ну соответсвенно импортируем
        $reflection->newInstance()->import($data, $additionalAttributes);

        if ($url = $request->get('suceess_redirect_url', false)) {
            return redirect($url)->withSuccessImport(true);
        }
        else {
            return redirect()->back()->withSuccessImport(true);
        }
    }
}
