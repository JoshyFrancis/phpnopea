@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>
					<h3>Validation test</h3>
                <div class="panel-body">
                    @if ($errors->any())
						<div class="alert alert-danger">
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif
                </div>
                <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ url('validatortest') }}" >
					{{ csrf_field() }}

					<div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
						<label for="username" class="col-md-4 control-label">User Name</label>

						<div class="col-md-6">
							<input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" notrequired autofocus>
							@if ($errors->has('username'))
								<span class="help-block">
									<strong>{{ $errors->first('username') }}</strong>
								</span>
							@endif
						</div>
					</div>

					<div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
						<label for="title" class="col-md-4 control-label">Title</label>

						<div class="col-md-6">
							<input id="title" type="text" class="form-control" name="title" value="{{ old('title') }}" notrequired>

							@if ($errors->has('title'))
								<span class="help-block">
									<strong>{{ $errors->first('title') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group{{ $errors->has('profilepic') ? ' has-error' : '' }}">
						<label for="profilepic" class="col-md-4 control-label">Profile Picture</label>

						<div class="col-md-6"> 
							<input type="file" name="profilepic" accept="image/*" accept2="*/*">
							@if ($errors->has('profilepic'))
								<span class="help-block">
									<strong>{{ $errors->first('profilepic') }}</strong>
								</span>
							@endif
						</div>	 
					</div> 

					<div class="form-group">
						<div class="col-md-8 col-md-offset-4">
							<button type="submit" class="btn btn-primary">
								Submit
							</button>
							<button type="reset" class="btn btn-primary">
								Reset
							</button>
						</div>
					</div>
				</form>
            </div>
        </div>
    </div>
</div>
@endsection
