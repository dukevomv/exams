@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-8 col-md-offset-2">
      @if(Auth::guest())
        <div class="panel panel-default">
          <div class="panel-heading">Welcome</div>
          <div class="panel-body">
            You can <a href="{{ route('login') }}">login</a> to keep up following the development News.
          </div>
        </div>
      @else
         <!--

              <span class="label label-warning">new2</span>
              <span class="label label-primary">UI UX</span>
              <span class="label label-success">BACKEND</span>
              <span class="label label-default">IN PROGRESS</span>
              <span class="label label-danger">NEW</span> 

          -->

        <div class="panel panel-default">
          <div class="panel-heading">
            Segments Logic &middot; <small class="text-warning">11 November 2017</small>
            <div class="pull-right">
              <span class="label label-danger">NEW</span> 
              <span class="label label-primary">UI UX</span>
              <span class="label label-success">BACKEND</span>
            </div>
          </div>
          <div class="panel-body">
            <p>
              The <a href="{{ url('/segments') }}" target="_blank">Segments</a> section basic functionality is ready with working actions like Listing, Create, Edit and Delete. 
            </p>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">
            Lessons Logic &middot; <small class="text-warning">23 August 2017</small>
            <div class="pull-right">
              <span class="label label-primary">UI UX</span>
              <span class="label label-success">BACKEND</span>
            </div>
          </div>
          <div class="panel-body">
            <p>
              A new Tab <a href="{{ url('/lessons') }}" target="_blank">Lessons</a> is added to the Navigation Bar, along with its
              own basic listing functionalities. 
            </p>
            <p>
              <b>Functionalities added:</b> UI Tables, Dropdown with working FIlters and optional Pagination 
            </p>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">
            First Blood &middot; <small class="text-warning">7 August 2017</small>
            <div class="pull-right">
              <span class="label label-success">BACKEND</span>
            </div>
          </div>
          <div class="panel-body">
            <b>Exams</b> after some time of reconsider, decided to start things from the scratch, for more stuff up its back.
            So here we go with <a href="https://laravel.com/docs/5.4" target="_blank">Laravel 5.4</a> for our framework.
          </div>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
