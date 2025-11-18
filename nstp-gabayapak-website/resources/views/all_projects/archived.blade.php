@extends('layouts.app')

@section('title', 'Archived Projects')

@section('content')
    <x-all-projects :section="'Archived Projects'" :projects="$projects" />
@endsection