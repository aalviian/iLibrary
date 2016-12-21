@if (session()->has('flash_notif.message'))
	<div class="container">
		<div class="alert alert-{{ session()->get('flash_notif.level') }}">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		{!! session()->get('flash_notif.message') !!}
		</div>
	</div>
@endif