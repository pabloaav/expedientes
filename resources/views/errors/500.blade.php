@extends('errors::layout')

@section('title', 'Error')

@section('message', __( $exception->getMessage() ? $exception->getMessage() :'El servidor no esta disponible por el
momento.'))