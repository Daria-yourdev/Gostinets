@extends('admin.layout')

@section('title', 'Новая банка')
@section('heading', 'Новая банка')

@section('content')

<a href="{{ route('admin.products.index') }}" class="admin-back">← к каталогу</a>

<form action="{{ route('admin.products.store') }}" method="POST" class="admin-form">
    @csrf
    @include('admin.products._form', ['product' => $product])
</form>

@endsection