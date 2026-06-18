@extends('layouts.admin')

@section('title')
    Administration
@endsection

@section('content-header')
    <h1>Administrative Overview<small>A quick glance at your system.</small></h1>
    <ol class="breadcrumb">
        <li class="active">Admin</li>
    </ol>
@endsection

@section('content')
<div class="row">
    <div class="col-xs-6 col-md-3">
        <a href="{{ route('admin.users') }}" class="stat-link">
            <div class="info-box bg-blue">
                <span class="info-box-icon"><i class="fa fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Users</span>
                    <span class="info-box-number">{{ number_format($stats['users']) }}</span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xs-6 col-md-3">
        <a href="{{ route('admin.servers') }}" class="stat-link">
            <div class="info-box bg-teal">
                <span class="info-box-icon"><i class="fa fa-server"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Servers</span>
                    <span class="info-box-number">{{ number_format($stats['servers']) }}</span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xs-6 col-md-3">
        <a href="{{ route('admin.nodes') }}" class="stat-link">
            <div class="info-box bg-green">
                <span class="info-box-icon"><i class="fa fa-sitemap"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Nodes</span>
                    <span class="info-box-number">{{ number_format($stats['nodes']) }}</span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xs-6 col-md-3">
        <a href="{{ route('admin.locations') }}" class="stat-link">
            <div class="info-box bg-yellow">
                <span class="info-box-icon"><i class="fa fa-globe"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Locations</span>
                    <span class="info-box-number">{{ number_format($stats['locations']) }}</span>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="box {{ $version->isLatestPanel() ? 'box-success' : 'box-danger' }}">
            <div class="box-header with-border">
                <h3 class="box-title">System Information</h3>
            </div>
            <div class="box-body">
                @if ($version->isLatestPanel())
                    You are running Ruff Panel version <code>{{ config('app.version') }}</code>. Your panel is up-to-date!
                @else
                    Your panel is <strong>not up-to-date!</strong> The latest version is <a href="https://github.com/Ruff/Panel/releases/v{{ $version->getPanel() }}" target="_blank"><code>{{ $version->getPanel() }}</code></a> and you are currently running version <code>{{ config('app.version') }}</code>.
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Helpful Resources</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-6 col-sm-3">
                        <a href="{{ $version->getDiscord() }}"><button class="btn btn-warning btn-block"><i class="fa fa-fw fa-support"></i> Get Help <small>(Discord)</small></button></a>
                    </div>
                    <div class="col-xs-6 col-sm-3">
                        <a href="https://Ruff.io"><button class="btn btn-default btn-block"><i class="fa fa-fw fa-link"></i> Documentation</button></a>
                    </div>
                    <div class="col-xs-6 col-sm-3">
                        <a href="https://github.com/Ruff/panel"><button class="btn btn-default btn-block"><i class="fa fa-fw fa-github"></i> GitHub</button></a>
                    </div>
                    <div class="col-xs-6 col-sm-3">
                        <a href="{{ $version->getDonations() }}"><button class="btn btn-success btn-block"><i class="fa fa-fw fa-heart"></i> Support the Project</button></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
