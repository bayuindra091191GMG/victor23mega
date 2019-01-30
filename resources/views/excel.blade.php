{{ Form::open(['route'=>['excel-post'],'method' => 'post', 'enctype'=>'multipart/form-data']) }}
    Input File <input type="file" name="file"/>
<input type="submit" value="Submit"/>
{{ Form::close() }}