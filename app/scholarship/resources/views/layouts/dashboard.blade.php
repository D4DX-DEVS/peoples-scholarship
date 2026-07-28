@extends('adminlte::page')

@section('adminlte_css')
  @parent
  <link rel="stylesheet" href="{{ URL::asset('pluggins/sweet-alert/lib/sweet-alert.css')}}">
@stop

@section('adminlte_js')
  @parent
  <script src="{{ asset('pluggins/sweet-alert/lib/sweet-alert.js') }}" type="text/javascript"></script>
@stop
