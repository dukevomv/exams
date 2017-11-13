@extends('layouts.app')

@section('styles')
  <style type="text/css">
    #execute{
      height:400px;
      border-radius: 3px;
    }
    #results{
      background-color:#15191d;
      border: 1px solid #ccc;
      height:400px;
      color:#407b7b;
      border-radius: 3px;
      padding:20px 30px;
    }
  </style>
@endsection

@section('content')
  <div class="container">
    <textarea class="col-xs-5 col-md-4" name="" id="execute" >console.log('asdas'); return 1</textarea>
    <div class="col-xs-2 col-md-1 text-center"><button id="run">Run</button></div>
    <div class="col-xs-5 col-md-4" id="results">asddsasd</div>
  </div>
@endsection

@section('scripts')
  <script>
    $('#run').on('click',function(){
      let script = $('#execute').val()
      newScript = document.createElement('script');
      newScript.id = 'scriptContainer';
      newScript.text = script;
      console.log(script)
      let results = new Function(script)();
      //let results = new eval('({'+script+'})')
      console.log(results)
      $('#results').html(results);
    })
  </script>
@endsection