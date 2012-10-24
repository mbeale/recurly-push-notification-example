@layout('main')

@section('maincontent')
	<h1>Dashboard</h1>
	<table class="table">
		<thead>
			<tr>
				<td></td>
				<td>Today</td>
				<td>This Week</td>
				<td>This Month</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>New Sign Ups</td>
				<td><span class="dmetric">{{$su_today}}</span></td>
				<td><span class="dmetric">{{$su_week}}</span></td>
				<td><span class="dmetric">{{$su_month}}</span></td>
			</tr>	
			<tr>
				<td>Cancellations</td>
				<td><span class="dmetric">{{$c_today}}</span></td>
				<td><span class="dmetric">{{$c_week}}</span></td>
				<td><span class="dmetric">{{$c_month}}</span></td>
			</tr>	
			<tr>
				<td>Revenue</td>
				<td><span class="dmetric">{{$r_today}}</span></td>
				<td><span class="dmetric">{{$r_week}}</span></td>
				<td><span class="dmetric">{{$r_month}}</span></td>
			</tr>	
		</tbody>
	</table>
@endsection