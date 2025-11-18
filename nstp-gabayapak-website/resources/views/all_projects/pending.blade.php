@extends('layouts.app')

@section('title', 'Pending Projects')

@section('content')
    <x-all-projects :section="'Pending Projects'" :projects="$projects" />
@endsection