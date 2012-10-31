@layout('main')

@section('maincontent')
<div class="row">
	<div class="span12">
		<form action="{{URL::to('options')}}" method="post">
			<legend>GetAmbassador.com Options</legend>
			<label for="activate">Activate<input type="checkbox" name="activate" ></label>
			<label for="emailnew">Email New Ambassador<input type="checkbox" name="emailnew" ></label>
			<label for="apikey">API Key</label><input type="text" name="apikye">
			<label for="username">Username </label><input type="text" name="username">
			<label for="campaignid">Default Campaign ID</label><input type="text" name="campaignid">
			<br>
			<button class="btn">Save</button>
		</form>
	</div>
</div>
@endsection