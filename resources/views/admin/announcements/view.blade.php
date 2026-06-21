@extends('layouts.admin')

@section('title')
    Announcements &rarr; {{ $announcement->title }}
@endsection

@section('content-header')
    <h1>{{ $announcement->title }}<small>{{ ucfirst($announcement->type) }}</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.announcements') }}">Announcements</a></li>
        <li class="active">{{ str_limit($announcement->title, 40) }}</li>
    </ol>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-8">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Announcement Details</h3>
            </div>
            <form action="{{ route('admin.announcements.view', $announcement->id) }}" method="POST">
                <div class="box-body">
                    <div class="form-group">
                        <label for="pTitle" class="form-label">Title</label>
                        <input type="text" id="pTitle" name="title" class="form-control" value="{{ $announcement->title }}" />
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label for="pType" class="form-label">Type</label>
                            <select id="pType" name="type" class="form-control">
                                @foreach(['announcement','offer','blog'] as $t)
                                    <option value="{{ $t }}" @if($announcement->type === $t) selected @endif>{{ ucfirst($t) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="pOrder" class="form-label">Sort Order</label>
                            <input type="number" id="pOrder" name="sort_order" class="form-control" value="{{ $announcement->sort_order }}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pBody" class="form-label">Body</label>
                        <textarea id="pBody" name="body" class="form-control" rows="3">{{ $announcement->body }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="pLink" class="form-label">Link (optional)</label>
                        <input type="text" id="pLink" name="link" class="form-control" value="{{ $announcement->link }}" placeholder="https://..." />
                    </div>
                    <div class="form-group">
                        <div class="checkbox checkbox-primary no-margin-bottom">
                            <input type="checkbox" id="pEnabled" name="enabled" value="1" @if($announcement->enabled) checked @endif />
                            <label for="pEnabled" class="strong">Enabled</label>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    {!! csrf_field() !!}
                    {!! method_field('PATCH') !!}
                    <button name="action" value="edit" class="btn btn-sm btn-primary pull-right">Save</button>
                    <button name="action" value="delete" class="btn btn-sm btn-danger pull-left muted muted-hover"><i class="fa fa-trash-o"></i></button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Preview</h3>
            </div>
            <div class="box-body">
                <p><span class="label label-default">{{ $announcement->presentation()['tag'] }}</span></p>
                <h4 style="margin-top:6px;">{{ $announcement->title }}</h4>
                <p class="text-muted">{{ $announcement->body }}</p>
                @if($announcement->link)
                    <p class="small"><a href="{{ $announcement->link }}" target="_blank" rel="noreferrer">{{ $announcement->link }}</a></p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
