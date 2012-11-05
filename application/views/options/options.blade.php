@layout('main')

@section('maincontent')
<div class="row">
	<div class="span12">
		@if($success)
		<div class="alert alert-success">{{$success}}</div>
		@endif
		@if($error)
		<div class="alert alert-error">{{$error}}</div>
		@endif
		<form action="{{URL::to('options')}}" method="post">
			<legend>Options</legend>
			<label for="usepush">Collect Push Notifications<input type="checkbox" name="usepush" data-bind="checked: collect" value="Yes"></label>
			<label for="basicusername">Basic Username</label><input type="text" name="basic_name" data-bind="value: basic_name, enable: collect">
			<label for="basicpass">Basic Password</label><input type="text" name="basic_pass" data-bind="value: basic_pass, enable: collect">
			<label for="">Recurly API Key</label><input type="text" name="recurly_public" data-bind="value: recurly_public, enable: collect">
			<label for="basicpass">Recurly Private Key</label><input type="text" name="recurly_private" data-bind="value: recurly_private, enable: collect">
			<br>
			<button class="btn">Save</button>
		</form>
	</div>
</div>
@endsection