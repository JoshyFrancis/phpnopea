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
						<?php
							//var_dump($errors->all());
						?>
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
					<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
						<label for="email" class="col-md-4 control-label">Email</label>

						<div class="col-md-6">
							<input id="email" type="text" class="form-control" name="email" value="{{ old('email') }}" notrequired>

							@if ($errors->has('email'))
								<span class="help-block">
									<strong>{{ $errors->first('email') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
						<label for="password" class="col-md-4 control-label">Password</label>

						<div class="col-md-6">
							<input id="password" type="text" class="form-control" name="password" value="{{ old('password') }}" notrequired>

							@if ($errors->has('password'))
								<span class="help-block">
									<strong>{{ $errors->first('password') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
						<label for="password_confirmation" class="col-md-4 control-label">Confirm Password</label>

						<div class="col-md-6">
							<input id="password_confirmation" type="text" class="form-control" name="password_confirmation" value="{{ old('password_confirmation') }}" notrequired>

							@if ($errors->has('password_confirmation'))
								<span class="help-block">
									<strong>{{ $errors->first('password_confirmation') }}</strong>
								</span>
							@endif
						</div>
					</div>
					<div class="form-group{{ $errors->has('profilepic') ? ' has-error' : '' }}">
						<label for="profilepic" class="col-md-4 control-label">Profile Picture</label>

						<div class="col-md-6"> 
							<input type="file" name="profilepic[]" multiple accept="image/*" accept2="*/*">
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
