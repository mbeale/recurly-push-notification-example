@layout('main')

@section('maincontent')
<div class="row">
	<div class="span4 offset4">
		@if ($message != '')
		<div class="alert alert-error">{{$message}}</div>
		@endif
		<form action="{{URL::to('change-password')}}" class="form" method="post">
			<legend>Change Password</legend>
			<div class="control-group {{ $errors->has('original') ? 'error' : '' }}">
  				<label class="control-label" for="original">Original</label>
  				<div class="controls">
    				<input type="password" id="original" name="original" value="{{Input::old('original')}}">
    				@if($errors->has('original'))
    				<span class="help-inline">{{ $errors->first('original')}}</span>
    				@endif
  				</div>
			</div>
			<div class="control-group {{ $errors->has('password') ? 'error' : '' }}">
  				<label class="control-label" for="username">New Password</label>
  				<div class="controls">
    				<input type="password" id="password" name="password">
    				@if($errors->has('password'))
    				<span class="help-inline">{{ $errors->first('password')}}</span>
    				@endif
  				</div>
			</div>
			<div class="control-group {{ $errors->has('confirmpassword') ? 'error' : '' }}">
  				<label class="control-label" for="username">Confirm New Password</label>
  				<div class="controls">
    				<input type="password" id="confirmpassword" name="confirmpassword">
    				@if($errors->has('confirmpassword'))
    				<span class="help-inline">{{ $errors->first('confirmpassword')}}</span>
    				@endif
  				</div>
			</div>
			<br>
			<button class="btn">Change</button>
		</form>
	</div>
</div>
@endsection