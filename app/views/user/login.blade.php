{{ Form::open(array('class' => 'form')) }}

{{ Form::textField('username', 'Username') }}
{{ Form::passwordField('password', 'Password') }}

{{ Form::submitField('submit', 'Log in') }}

{{ Form::close() }}