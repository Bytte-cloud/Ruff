@extends('layouts.admin')
@include('partials/admin.settings.nav', ['activeTab' => 'theme'])

@section('title')
    Theme
@endsection

@section('content-header')
    <h1>Theme &amp; Appearance<small>Customise the look of the user-facing dashboard.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.settings') }}">Settings</a></li>
        <li class="active">Theme</li>
    </ol>
@endsection

@section('content')
    @yield('settings::nav')

    @php
        $tokens = array_keys($theme['modes']['light']);
        $modes = ['light' => 'Light mode', 'dark' => 'Dark mode'];
    @endphp

    <form action="{{ route('admin.settings.theme') }}" method="POST" id="themeForm">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Presets</h3>
                    </div>
                    <div class="box-body">
                        <p class="text-muted" style="margin-bottom:14px;">Start from a preset, then fine-tune anything below. Recommended.</p>
                        <div style="display:flex;flex-wrap:wrap;gap:10px;">
                            @foreach($presets as $key => $preset)
                                <button type="button" class="btn btn-default js-preset" data-preset="{{ $key }}">{{ $preset['name'] ?? ucfirst($key) }}</button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="box">
                    <div class="box-header with-border"><h3 class="box-title">Behaviour</h3></div>
                    <div class="box-body">
                        <div class="form-group">
                            <label class="control-label">Default mode</label>
                            <div>
                                <label class="radio-inline"><input type="radio" name="default_mode" value="light" @if($theme['default_mode'] === 'light') checked @endif> Light</label>
                                <label class="radio-inline"><input type="radio" name="default_mode" value="dark" @if($theme['default_mode'] === 'dark') checked @endif> Dark</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label><input type="checkbox" name="allow_toggle" value="1" @if(!empty($theme['allow_toggle'])) checked @endif> Let users switch between light &amp; dark</label>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label><input type="checkbox" name="share_accent" value="1" @if(!empty($theme['share_accent_with_admin'])) checked @endif> Share the accent color with the admin panel</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="box">
                    <div class="box-header with-border"><h3 class="box-title">Geometry</h3></div>
                    <div class="box-body">
                        @php $geoLabels = ['radius' => 'Corner radius', 'radius_sm' => 'Corner radius (small)', 'border_width' => 'Border width']; @endphp
                        <div class="row">
                            @foreach($theme['geometry'] as $gkey => $gval)
                                <div class="form-group col-sm-4">
                                    <label class="control-label">{{ $geoLabels[$gkey] ?? ucfirst(str_replace('_', ' ', $gkey)) }}</label>
                                    <input type="text" class="form-control" name="geometry[{{ $gkey }}]" value="{{ $gval }}" placeholder="e.g. 12px">
                                </div>
                            @endforeach
                        </div>
                        <p class="text-muted" style="margin:0;"><small>Any CSS length works (px, rem, etc.).</small></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            @foreach($modes as $mode => $modeLabel)
                <div class="col-md-6">
                    <div class="box">
                        <div class="box-header with-border"><h3 class="box-title">{{ $modeLabel }} colors</h3></div>
                        <div class="box-body">
                            @foreach($tokens as $token)
                                @php $val = $theme['modes'][$mode][$token] ?? '#000000'; @endphp
                                <div class="form-group" style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
                                    <label class="control-label" style="flex:1 1 auto;margin:0;">{{ $labels[$token] ?? ucfirst(str_replace('_', ' ', $token)) }}</label>
                                    <input type="color" value="{{ \Illuminate\Support\Str::startsWith($val, '#') ? $val : '#000000' }}"
                                           data-target="{{ $mode }}-{{ $token }}" class="js-swatch"
                                           style="width:38px;height:34px;border:1px solid var(--border-strong, #ccc);border-radius:8px;padding:2px;background:#fff;cursor:pointer;">
                                    <input type="text" class="form-control js-color" style="flex:0 0 120px;"
                                           id="{{ $mode }}-{{ $token }}" name="{{ $mode }}[{{ $token }}]" value="{{ $val }}"
                                           data-mode="{{ $mode }}" data-token="{{ $token }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header with-border"><h3 class="box-title">Live preview</h3></div>
                    <div class="box-body">
                        <div style="display:flex;gap:10px;margin-bottom:14px;">
                            <button type="button" class="btn btn-sm btn-default js-preview-mode active" data-mode="light">Light</button>
                            <button type="button" class="btn btn-sm btn-default js-preview-mode" data-mode="dark">Dark</button>
                        </div>
                        <div id="themePreview" style="border-radius:14px;padding:22px;transition:all .2s;">
                            <div id="tpCard" style="padding:18px;border-style:solid;">
                                <div style="font-size:17px;font-weight:700;margin-bottom:4px;" id="tpTitle">Your dashboard</div>
                                <div id="tpMuted" style="font-size:13px;margin-bottom:14px;">This is how surfaces, text and the accent will look.</div>
                                <button type="button" id="tpBtn" style="border:0;padding:8px 16px;border-radius:8px;color:#fff;font-weight:600;cursor:default;">Primary action</button>
                                <span id="tpLink" style="margin-left:14px;font-weight:600;">A themed link</span>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        {!! csrf_field() !!}
                        <button type="submit" name="_method" value="PATCH" class="btn btn-primary pull-right">Save Theme</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        (function () {
            var presets = @json($presets);

            // Keep the color swatch and the text field in sync.
            document.querySelectorAll('.js-swatch').forEach(function (sw) {
                sw.addEventListener('input', function () {
                    var t = document.getElementById(sw.dataset.target);
                    if (t) { t.value = sw.value; updatePreview(); }
                });
            });
            document.querySelectorAll('.js-color').forEach(function (inp) {
                inp.addEventListener('input', function () {
                    var sw = document.querySelector('.js-swatch[data-target="' + inp.id + '"]');
                    if (sw && /^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test(inp.value)) { sw.value = inp.value; }
                    updatePreview();
                });
            });

            // Apply a preset to every field.
            document.querySelectorAll('.js-preset').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var p = presets[btn.dataset.preset];
                    if (!p) return;
                    ['light', 'dark'].forEach(function (mode) {
                        Object.keys(p.modes[mode] || {}).forEach(function (token) {
                            var inp = document.getElementById(mode + '-' + token);
                            if (inp) {
                                inp.value = p.modes[mode][token];
                                var sw = document.querySelector('.js-swatch[data-target="' + inp.id + '"]');
                                if (sw && /^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test(inp.value)) sw.value = inp.value;
                            }
                        });
                    });
                    Object.keys(p.geometry || {}).forEach(function (g) {
                        var gi = document.querySelector('[name="geometry[' + g + ']"]');
                        if (gi) gi.value = p.geometry[g];
                    });
                    if (p.default_mode) {
                        var r = document.querySelector('input[name="default_mode"][value="' + p.default_mode + '"]');
                        if (r) r.checked = true;
                    }
                    updatePreview();
                });
            });

            // Live preview.
            var previewMode = 'light';
            document.querySelectorAll('.js-preview-mode').forEach(function (b) {
                b.addEventListener('click', function () {
                    previewMode = b.dataset.mode;
                    document.querySelectorAll('.js-preview-mode').forEach(function (x) { x.classList.remove('active'); });
                    b.classList.add('active');
                    updatePreview();
                });
            });

            function val(token) {
                var el = document.getElementById(previewMode + '-' + token);
                return el ? el.value : '';
            }
            function updatePreview() {
                var radius = (document.querySelector('[name="geometry[radius]"]') || {}).value || '14px';
                var bw = (document.querySelector('[name="geometry[border_width]"]') || {}).value || '1px';
                document.getElementById('themePreview').style.background = val('background');
                var card = document.getElementById('tpCard');
                card.style.background = val('surface');
                card.style.borderColor = val('border');
                card.style.borderWidth = bw;
                card.style.borderRadius = radius;
                document.getElementById('tpTitle').style.color = val('text');
                document.getElementById('tpMuted').style.color = val('text_muted');
                var btn = document.getElementById('tpBtn');
                btn.style.background = val('accent');
                btn.style.borderRadius = (document.querySelector('[name="geometry[radius_sm]"]') || {}).value || '8px';
                document.getElementById('tpLink').style.color = val('accent');
            }
            updatePreview();
        })();
    </script>
@endsection
