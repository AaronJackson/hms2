@extends('layouts.app')

@section('content')

<div class="container">
  @foreach ($forms as $form)
    <a href="{{ route('forms.view', $form->getId()) }}">{{ $form->getName() }}</a>
  @endforeach
</div>

@endsection
