@layout('main')

@section('maincontent')
<form class="form-inline">
	<legend>Subscription History</legend>
	<input type="text" placeholder="Sub UUID" data-bind="value: viewModel.subuuid">
	<button class="btn" data-bind="click: viewModel.search">Search</button>
</form>
<hr>
<table class="table table-striped">
	<thead>
		<tr>
			<th>Account Code</th>
			<th>Type</th>
			<th>Activity Date</th>
			<th>Plan Code</th>
			<th>Plan Name</th>
			<th>Quantity</th>
			<th>Amount</th>
		</tr>
	</thead>
	<tbody data-bind="foreach: viewModel.records">
		<tr>
			<td data-bind="text: account_code"></td>
			<td> <a href="#" data-bind="text: type, click: rawnotification"></a></td>
			<td data-bind="text: activity_date"></td>
			<td data-bind="text: plan_code"></td>
			<td data-bind="text: plan_name"></td>
			<td data-bind="text: quantity"></td>
			<td data-bind="text: amount"></td>
		</tr>
	</tbody>
</table>
<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Raw Notification</h3>
  </div>
  <div class="modal-body">
  	<pre id="rawdata"></pre>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>
@endsection