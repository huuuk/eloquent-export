{!! Form::open([ 'route' => isset($route) ? $route : 'import', 'files' => true, 'class' => 'form-inline' ]) !!}
<div class="form-group">
    {!! Form::file('import_file') !!}
</div>
@if(isset($additionalAttributes) && is_array($additionalAttributes) && count($additionalAttributes) > 0)
    @foreach($additionalAttributes as $name => $value)
        {!! Form::hidden("additional_attributes[{$name}]", $value) !!}
    @endforeach
@endif
@if(isset($supportedTypes) && is_array($supportedTypes) && count($supportedTypes) > 0)
    @foreach($supportedTypes as $type)
        {!! Form::hidden('supported_types[]', $type) !!}
    @endforeach
@endif
{!! Form::submit('Импортировать', ['class' => 'btn btn-primary']) !!}
{!! Form::close() !!}