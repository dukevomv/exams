@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-xs-12 col-md-8 col-md-offset-2">
      <div class="jumbotron">
			  <h1>403</h1>
			  <p>Unauthorized Request</p>
			  <p><a class="btn btn-primary btn-lg" href="{{url('/')}}" role="button">Return to Homepage</a></p>
			</div>
    </div>
  </div>
</div>
@endsection
