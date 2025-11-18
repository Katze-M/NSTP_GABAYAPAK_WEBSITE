@extends('layouts.app')

@section('title', 'Projects')

@section('content')
    <x-all-projects :section="'All'" :projects="$projects" />
@endsection