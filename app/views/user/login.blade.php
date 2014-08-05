{{ Form::formOpen($errors, array('class' => 'form')) }}

@include ('includes.autherror')

{{ Form::textField('username', 'Username', null, $errors) }}
{{ Form::passwordField('password', 'Password', $errors) }}

{{ Form::submitField('submit', 'Log in') }}

<p class="group"></p>
<p class="group"></p>
<p class="group">If you've received an <strong>account initialisation code</strong> <a href="#">click here</a>.</p>

{{ Form::close() }}
