@layout('main')

@section('maincontent')
<div class="row">
	<div class="span4 offset4">
		@if ($message != '')
		<div class="alert alert-error">{{$message}}</div>
		@endif
		<form action="login" method="post" class="form">
			<legend>Login</legend>
			<div class="control-group {{ $errors->has('username') ? 'error' : '' }}">
  				<label class="control-label" for="username">Username</label>
  				<div class="controls">
    				<input type="text" id="username" name="username" value="{{Input::old('username')}}">
    				@if($errors->has('username'))
    				<span class="help-inline">{{ $errors->first('username')}}</span>
    				@endif
  				</div>
			</div>
			<div class="control-group {{ $errors->has('password') ? 'error' : '' }}">
  				<label class="control-label" for="username">Password</label>
  				<div class="controls">
    				<input type="password" id="password" name="password">
    				@if($errors->has('password'))
    				<span class="help-inline">{{ $errors->first('password')}}</span>
    				@endif
  				</div>
			</div>
			<br>
			<button class="btn">Login</button>
		</form>
	</div>
</div>
@endsection
