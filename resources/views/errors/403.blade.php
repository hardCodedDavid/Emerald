@extends('errors::minimal')

@section('title', __('Unauthorized'))
@section('code', '403')
@section('message', __('You are not allowed to carry out this action.'))
