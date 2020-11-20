@extends('layouts.app')

@section('content')
  <div class="container">
    <h2>Javascript</h2>
    <p>Na γραφτεί αλγόριθμος που δέχεται οποιοδήποτε <code>String</code> και επιστρέφει το αντίστροφο του.</p>
    <div class="col-xs-5 col-md-4">
      <input class="col-xs-12" placeholder="input" value="Hello World!" id="input" />
      <textarea class="col-xs-12" name=""  id="execute" >function(x){return x;}</textarea>
    </div>
    <div class="col-xs-2 col-md-1 text-center"><button id="run">Test</button></div>
    <div class="col-xs-5 col-md-4" id="results">Press "Run" to see results.</div>

    <div class="col-xs-1" id="valid"></div>
  </div>
@endsection

@section('scripts')
  <script type="text/javascript">
    $('#run').on('click',function(){
      let script = $('#execute').val()
      let results = eval('('+script+')')
      $('#results').html(results($('#input').val()));
      Evaluate(results)
    })

    function Rule(x){
      let y = '';
      for(let i = x.length - 1;i >= 0; i--){
        y +=  x[i];
      }
      return y;
    }

    function Evaluate(client_code){
      console.log(client_code)
      const y = 'me lene 2';
      let correct = Rule(y)
      let pending = client_code(y)
      console.log(y,correct,pending)
      if(correct == pending){
        $('#valid').html('right');
      } else {
        $('#valid').html('wrong');
      }
    }
  </script>
@endsection