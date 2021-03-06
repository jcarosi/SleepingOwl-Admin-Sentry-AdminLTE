@if ( ! empty($title))
	<div class="row">
		<div class="col-lg-12">
			<h2 style="margin-top:0;"><small>{{ $title }}</small></h2>
		</div>
	</div>
@endif
@if ($creatable)
	<a class="btn btn-primary flat" href="{{ $createUrl }}"><i class="fa fa-plus"></i> {{ trans('admin::lang.table.new-entry') }}</a>
@endif
<div class="pull-right tableActions">
	@foreach ($actions as $action)
		{!! $action->render() !!}
	@endforeach
</div>
<div class="box">
	<table class="table table-striped datatables" data-order="{{ json_encode($order) }}" data-url="{{ $url }}">
		<thead>
			<tr>
				@foreach ($columns as $column)
					{!! $column->header()->render() !!}
				@endforeach
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>