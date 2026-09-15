@extends('layouts.app')

@section('content')
@section('pageTitle', $form->getName())

<div class="container-fluid">
<h3>Responses: {{ $form->getName() }}</h3>
<form-responses :form-id="{{ $form->getId() }}" />
</div>

@endsection
