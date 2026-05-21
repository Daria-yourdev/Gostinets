@extends('admin.layout')

@section('title', $product->name)
@section('heading', $product->name)

@section('content')

<a href="{{ route('admin.products.index') }}" class="admin-back">← к кладовой</a>

<form action="{{ route('admin.products.update', $product) }}" method="POST" class="admin-form">
    @csrf
    @method('PATCH')
    @include('admin.products._form', ['product' => $product])
</form>

@endsection