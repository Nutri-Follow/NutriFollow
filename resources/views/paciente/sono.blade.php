<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Pacientes</title>
        <style>
            .bd-example-row {
                padding-top: 0.75rem;
                padding-bottom: 0.75rem;
                border-bottom: 1px solid;
            }
        </style>
    </head>
    @extends('home')
    <x-guest-layout>
        <div class="row cards justify-content-center pt-4">
            <div class="col-6">
                <br><br><br>
                <div class="mx-auto" style="width: 680px;">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                        <a class="nav-link" href="/nutricionista/paciente/relatorio-dieta/{{$id}}">Dieta</a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link" href="/nutricionista/paciente/agua/{{$id}}">Consumo de Água</a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link active" href="/nutricionista/paciente/sono/{{$id}}" >Qualidade do Sono</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/nutricionista/paciente/exercicio/{{$id}}">Exercício</a>
                        </li>
                    </ul>
                </div>
    
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div id="container"></div>
                    </div>
                    <div class="col align-self-end " style="width: 150px;">
                        <a href="/nutricionista/listar/pacientes" class="btn btn-outline-secondary btn-sm">Listar Pascientes</a>
                    </div>
                    <br>
                </div>
                <form method="POST" action="{{ route('sono', $id) }}">
                    @csrf
                    <div class="container mt-5" style="max-width: 450px">
                        <div class="row form-group">       
                            <label class="col-sm-5 col-form-label">Início</label> 
                            <label class="col-sm-1 col-form-label">Fim</label>                        
                            <div class="input-daterange input-group" id="datepicker">
                                <input type="date" class="input-sm form-control" name="inicio" autocomplete="off"/>
                                <input type="date" class="input-sm form-control" name="fim" autocomplete="off"/>
                                <button class="btn btn-success" type="submit">Filtrar</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
        
    </div>
    
</x-guest-layout>

</html>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script>
    Highcharts.chart('container', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Relatório de sono do Paciente'
        },
        subtitle: {
            text: 'Quantidade e qualidade de sono do paciente no período'
        },
        xAxis: {
            categories: <?= $dias ?>,
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Sono'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [<?= $duracao ?>, <?= $qualidade ?>]

    });
</script>
