@extends('layouts.app')

@section('title', 'Roteirizacao')

@push('styles')
<style>
    #routing-map { width: 100%; height: 100%; min-height: 460px; border-radius: 0 0 12px 12px; }
    .pac-container { z-index: 9999; }
</style>
@endpush

@section('content')

{{-- ══════════════════════════════════════
     HEADER
══════════════════════════════════════ --}}
<div class="mod-header">
    <div class="mod-header-inner">
        <div class="mod-header-icon" style="--mod-color: #0EA5E9">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="3" cy="12" r="2"/><circle cx="21" cy="5" r="2"/><circle cx="21" cy="19" r="2"/>
                <line x1="5" y1="12" x2="19" y2="5.5"/><line x1="5" y1="12" x2="19" y2="18.5"/>
            </svg>
        </div>
        <div class="mod-header-info">
            <nav class="mod-breadcrumb" aria-label="breadcrumb">
                <a href="{{ route('home') }}" class="mod-breadcrumb-link">Inicio</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <a href="{{ route('module.show', 'transporte') }}" class="mod-breadcrumb-link">Transporte</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <span class="mod-breadcrumb-current">Roteirizacao</span>
            </nav>
            <h1 class="mod-title">Roteirizacao de Entregas</h1>
            <p class="mod-description">Planeje rotas otimizadas com calculo automatico de distancia, tempo e sequencia de paradas.</p>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════
     CORPO
══════════════════════════════════════ --}}
<div class="routing-page">

    {{-- PAINEL ESQUERDO: formulario de rota --}}
    <aside class="routing-sidebar">

        <div class="routing-sidebar-header">
            <h2 class="routing-sidebar-title">Planejar rota</h2>
            <p class="routing-sidebar-sub">Informe origem, destino e paradas intermediarias.</p>
        </div>

        {{-- Origem --}}
        <div class="routing-field-group">
            <label class="routing-label" for="origin-input">
                <span class="routing-label-dot routing-label-dot--origin"></span>
                Origem
            </label>
            <input
                id="origin-input"
                type="text"
                class="routing-input"
                placeholder="Ex: Rua das Flores, 100, Sao Paulo"
                autocomplete="off"
            >
        </div>

        {{-- Paradas dinâmicas --}}
        <div id="waypoints-container"></div>

        <button type="button" id="add-waypoint-btn" class="routing-btn-add-stop">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Adicionar parada
        </button>

        {{-- Destino --}}
        <div class="routing-field-group">
            <label class="routing-label" for="destination-input">
                <span class="routing-label-dot routing-label-dot--dest"></span>
                Destino final
            </label>
            <input
                id="destination-input"
                type="text"
                class="routing-input"
                placeholder="Ex: Av. Paulista, 1000, Sao Paulo"
                autocomplete="off"
            >
        </div>

        {{-- Tipo de viagem --}}
        <div class="routing-field-group">
            <label class="routing-label" for="travel-mode">Modo de transporte</label>
            <select id="travel-mode" class="routing-input">
                <option value="DRIVING">Carro / Caminhao</option>
                <option value="BICYCLING">Bicicleta</option>
                <option value="WALKING">A pe</option>
            </select>
        </div>

        {{-- Botao calcular --}}
        <button type="button" id="calc-route-btn" class="routing-btn-calc">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            Calcular rota
        </button>

        <button type="button" id="clear-route-btn" class="routing-btn-clear" style="display:none;">
            Limpar
        </button>

        {{-- Resumo da rota --}}
        <div id="route-summary" class="routing-summary" style="display:none;">
            <div class="routing-summary-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span id="route-duration">—</span>
            </div>
            <div class="routing-summary-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><polyline points="8 7 3 12 8 17"/><polyline points="16 7 21 12 16 17"/></svg>
                <span id="route-distance">—</span>
            </div>
        </div>

        {{-- Instruções passo a passo --}}
        <div id="directions-panel" class="routing-directions"></div>

        {{-- Aviso de configuração --}}
        @if(empty($mapsApiKey))
        <div class="routing-no-key-notice">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <div>
                <strong>Chave da API não configurada.</strong><br>
                Adicione <code>GOOGLE_MAPS_API_KEY=sua_chave</code> no arquivo <code>.env</code><br>
                <small>Execute <code>php artisan maps:verify</code> para verificar a configuração</small>
            </div>
        </div>
        @else
        <div class="routing-no-key-notice" style="background:#FEF3C7;border-color:#F59E0B;color:#92400E;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div>
                <strong>Importante: Habilite as APIs no Google Cloud</strong><br>
                <small>
                    Acesse o <a href="https://console.cloud.google.com/apis/library" target="_blank" style="text-decoration:underline;font-weight:600;">Google Cloud Console</a> e habilite:
                    <strong>Maps JavaScript API</strong>, <strong>Places API</strong> e <strong>Directions API</strong>
                </small>
            </div>
        </div>
        @endif

    </aside>

    {{-- MAPA --}}
    <div class="routing-map-wrap">
        @if(!empty($mapsApiKey))
            <div id="routing-map"></div>
        @else
            <div class="routing-map-placeholder">
                <div class="routing-map-placeholder-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.2">
                        <polygon points="3 11 22 2 13 21 11 13 3 11"/>
                    </svg>
                    <p class="routing-map-placeholder-title">Mapa indisponivel</p>
                    <p class="routing-map-placeholder-text">
                        Configure a variavel <code>GOOGLE_MAPS_API_KEY</code> no <code>.env</code> para ativar o mapa interativo.
                    </p>
                    <a
                        href="https://console.cloud.google.com/apis/credentials"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="routing-map-placeholder-link"
                    >
                        Obter chave no Google Cloud Console &rarr;
                    </a>
                </div>
            </div>
        @endif
    </div>

</div>

@endsection

@push('scripts')
@if(!empty($mapsApiKey))
<script>
(function () {
    var mapsApiKey = @json($mapsApiKey);
    var waypointCount = 0;
    var map, directionsService, directionsRenderer;
    var waypointAutocompletes = [];

    // Tratamento de erros globais do Google Maps
    window.gm_authFailure = function() {
        console.error('Google Maps API: Falha na autenticação');
        showMapError(
            'Erro de Autenticação',
            'A chave da API está inválida ou as APIs necessárias não estão habilitadas.<br><br>' +
            '<strong>Passos para resolver:</strong><br>' +
            '1. Acesse o <a href="https://console.cloud.google.com/apis/library" target="_blank" style="color:#3B82F6;text-decoration:underline;">Google Cloud Console</a><br>' +
            '2. Habilite: <strong>Maps JavaScript API</strong>, <strong>Places API</strong>, <strong>Directions API</strong><br>' +
            '3. Verifique se o billing está configurado<br>' +
            '4. Execute: <code style="background:#F1F5F9;padding:2px 6px;border-radius:4px;">php artisan maps:verify</code>'
        );
    };

    function showMapError(title, message) {
        var mapElement = document.getElementById('routing-map');
        if (mapElement) {
            mapElement.innerHTML =
                '<div style="display:flex;align-items:center;justify-content:center;height:100%;background:#FEF2F2;padding:40px;text-align:center;border-radius:0 0 12px 12px;">' +
                    '<div style="max-width:500px;">' +
                        '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="1.5" style="margin:0 auto 16px;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>' +
                        '<h3 style="color:#991B1B;font-size:18px;font-weight:600;margin-bottom:12px;">' + title + '</h3>' +
                        '<div style="color:#B91C1C;font-size:14px;line-height:1.6;">' + message + '</div>' +
                    '</div>' +
                '</div>';
        }
    }

    // Carrega o Google Maps de forma assíncrona
    function loadMaps() {
        var script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?key=' + mapsApiKey
            + '&libraries=places&callback=initRoutingMap';
        script.async = true;
        script.defer = true;
        script.onerror = function() {
            console.error('Erro ao carregar Google Maps API');
            showMapError(
                'Erro ao Carregar',
                'Não foi possível carregar o Google Maps API.<br>' +
                'Verifique sua conexão com a internet e tente novamente.'
            );
        };
        document.head.appendChild(script);
    }

    window.initRoutingMap = function () {
        try {
            if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
                throw new Error('Google Maps API não foi carregada corretamente');
            }

            map = new google.maps.Map(document.getElementById('routing-map'), {
                center: { lat: -14.2350, lng: -51.9253 },
                zoom: 5,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true,
                zoomControl: true,
                styles: [
                    { featureType: 'poi', elementType: 'labels', stylers: [{ visibility: 'off' }] }
                ]
            });

            directionsService  = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer({ map: map });

            // Autocomplete para origem e destino
            new google.maps.places.Autocomplete(document.getElementById('origin-input'),      { types: ['geocode'] });
            new google.maps.places.Autocomplete(document.getElementById('destination-input'), { types: ['geocode'] });

            document.getElementById('calc-route-btn').addEventListener('click', calcRoute);
            document.getElementById('clear-route-btn').addEventListener('click', clearRoute);
            document.getElementById('add-waypoint-btn').addEventListener('click', addWaypoint);
        } catch (error) {
            console.error('Erro ao inicializar o mapa:', error);
            var mapElement = document.getElementById('routing-map');
            if (mapElement) {
                mapElement.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;background:#FEF2F2;color:#991B1B;padding:20px;text-align:center;border-radius:0 0 12px 12px;"><div><strong>Erro ao carregar o mapa</strong><br><small>Verifique o console do navegador para mais detalhes</small></div></div>';
            }
        }
    };

    function addWaypoint() {
        waypointCount++;
        var idx = waypointCount;
        var container = document.getElementById('waypoints-container');

        var wrap = document.createElement('div');
        wrap.className = 'routing-field-group routing-waypoint';
        wrap.id = 'waypoint-wrap-' + idx;

        wrap.innerHTML =
            '<label class="routing-label">' +
                '<span class="routing-label-dot routing-label-dot--stop"></span>' +
                'Parada ' + idx +
            '</label>' +
            '<div style="display:flex;gap:8px;">' +
                '<input id="waypoint-input-' + idx + '" type="text" class="routing-input" placeholder="Endereco da parada" autocomplete="off">' +
                '<button type="button" onclick="removeWaypoint(' + idx + ')" class="routing-btn-remove-stop" title="Remover">' +
                    '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>' +
                '</button>' +
            '</div>';

        container.appendChild(wrap);

        var ac = new google.maps.places.Autocomplete(
            document.getElementById('waypoint-input-' + idx),
            { types: ['geocode'] }
        );
        waypointAutocompletes.push({ id: idx, ac: ac });
    }

    window.removeWaypoint = function (idx) {
        var el = document.getElementById('waypoint-wrap-' + idx);
        if (el) el.remove();
        waypointAutocompletes = waypointAutocompletes.filter(function (w) { return w.id !== idx; });
    };

    function calcRoute() {
        var origin      = document.getElementById('origin-input').value.trim();
        var destination = document.getElementById('destination-input').value.trim();
        var travelMode  = document.getElementById('travel-mode').value;

        if (!origin || !destination) {
            alert('Informe pelo menos origem e destino.');
            return;
        }

        var waypoints = [];
        document.querySelectorAll('.routing-waypoint input[type="text"]').forEach(function (inp) {
            var val = inp.value.trim();
            if (val) {
                waypoints.push({ location: val, stopover: true });
            }
        });

        directionsService.route({
            origin:       origin,
            destination:  destination,
            waypoints:    waypoints,
            optimizeWaypoints: true,
            travelMode:   google.maps.TravelMode[travelMode],
        }, function (result, status) {
            if (status === 'OK') {
                directionsRenderer.setDirections(result);

                var leg      = result.routes[0].legs;
                var totalDist = 0, totalTime = 0;
                leg.forEach(function (l) {
                    totalDist += l.distance.value;
                    totalTime += l.duration.value;
                });

                document.getElementById('route-duration').textContent = formatDuration(totalTime);
                document.getElementById('route-distance').textContent = (totalDist / 1000).toFixed(1) + ' km';
                document.getElementById('route-summary').style.display = 'flex';
                document.getElementById('clear-route-btn').style.display = 'block';

                // Instruções
                var panel = document.getElementById('directions-panel');
                panel.innerHTML = '';
                leg.forEach(function (l, legIdx) {
                    var legTitle = document.createElement('p');
                    legTitle.className = 'routing-directions-leg';
                    legTitle.textContent = (legIdx === 0 ? 'Saindo de: ' : 'De: ') + l.start_address.split(',')[0]
                        + ' → ' + l.end_address.split(',')[0];
                    panel.appendChild(legTitle);

                    l.steps.forEach(function (step) {
                        var div = document.createElement('div');
                        div.className = 'routing-directions-step';
                        div.innerHTML = step.instructions + ' <span class="routing-step-dist">(' + step.distance.text + ')</span>';
                        panel.appendChild(div);
                    });
                });
            } else {
                alert('Nao foi possivel calcular a rota: ' + status);
            }
        });
    }

    function clearRoute() {
        directionsRenderer.setDirections({ routes: [] });
        document.getElementById('origin-input').value      = '';
        document.getElementById('destination-input').value = '';
        document.getElementById('waypoints-container').innerHTML = '';
        waypointCount = 0;
        waypointAutocompletes = [];
        document.getElementById('route-summary').style.display  = 'none';
        document.getElementById('clear-route-btn').style.display = 'none';
        document.getElementById('directions-panel').innerHTML = '';
    }

    function formatDuration(seconds) {
        var h = Math.floor(seconds / 3600);
        var m = Math.floor((seconds % 3600) / 60);
        if (h > 0) return h + 'h ' + m + 'min';
        return m + ' min';
    }

    loadMaps();
})();
</script>
@endif
@endpush

