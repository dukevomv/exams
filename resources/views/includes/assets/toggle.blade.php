<?php 
	if(!isset($classes))	$classes = [];
	if(!isset($attributes))	$attributes = [];
?>
<label class="toggle {{implode(' ',$classes)}}" @foreach($attributes as $key=>$attr) {{$key}}="{{$attr}}" @endforeach @if(isset($id)) id="{{$id}}" @endif>
  <input type="checkbox" @if($active) checked @endif >
  <span class="slider"></span>
</label>