<style>
    .container-products{
        padding: 25px;
    }
    .button-create-products{
        margin-bottom: 20px;
        display: flex;
        justify-content: flex-end;
        gap: 15px;
    }
    .button-create-products button{
        padding: 6px 12px;
        background-color: #4CAF50;
        color: white;
        border-radius: 6px;
        cursor: pointer;
        align-items: center;
        border: 1px solid #317033;
        text-align: center;
    }
    .button-create-products button a{
        text-decoration: none;
        color: white;
    }

    .table-products{
        width: 100%;
        border-collapse: collapse;
        text-align: center;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .table-products th,
    .table-products td{
        padding: 12px 10px;
        border: 1px solid #ddd;
    }

    .table-products tr:nth-child(even){
        background-color: #f9f9f9;
    }

    .table-products tbody tr:hover{
        background-color: #f1f1f1;
    }

    .table-products thead {
        background-color: #f2f2f2;
    }

    .table-products th {
        font-weight: bold;
    }

    .delete{
        background: none;
        border: none;
        color: red;
        cursor: pointer;
        font-size: 1em;
        text-decoration: underline;
        padding: 0;
        font-family: inherit;
    }

    .edit{
        background: none;
        border: none;
        color: blue;
        cursor: pointer;
        font-size: 1em;
        text-decoration: underline;
        padding: 0;
        font-family: inherit;
    }

    .search-filter{
        display: flex;
        justify-content: end;
        margin-bottom: 20px;
        gap: 10px;
    }
    .search-bar{
        padding: 8px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .filter{
        padding: 8px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .filtrar, .limpar{
        padding: 10px 18px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }

    h1 {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 30px;
        font-weight: bold;
        color: #000;
    }
</style>
@extends('layouts.app')

@section('title', 'Produtos')

@section('content')
<main class="container-products">
    <div class="titulo">
        <h1>Lista de Funções</h1>
        <div class="button-create-products">
            <button><a href="{{ route('roles.create') }}">Adicionar Funções</a></button>
