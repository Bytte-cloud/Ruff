@extends('layouts.admin')

@section('title')
    Announcements
@endsection

@section('content-header')
    <h1>Announcements<small>Offer, announcement and blog cards shown on the client dashboard.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">Announcements</li>
    </ol>
@endsection

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Announcement List</h3>
                <div class="box-tools">
                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#newAnnouncementModal">Create New</button>
                </div>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Title</th>
                            <th class="text-center">Order</th>
                            <th class="text-center">Enabled</th>
                        </tr>
                        @foreach ($announcements as $announcement)
                            <tr>
                                <td><code>{{ $announcement->id }}</code></td>
                                <td>{{ ucfirst($announcement->type) }}</td>
                                <td><a href="{{ route('admin.announcements.view', $announcement->id) }}">{{ $announcement->title }}</a></td>
                                <td class="text-center">{{ $announcement->sort_order }}</td>
                                <td class="text-center">
                                    @if($announcement->enabled)
                                        <span class="label label-success">Yes</span>
                                    @else
                                        <span class="label label-default">No</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="newAnnouncementModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.announcements') }}" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Create Announcement</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-8">
                            <label for="pTitle" class="form-label">Title</label>
                            <input type="text" name="title" id="pTitle" class="form-control" />
                        </div>
                        <div class="form-group col-md-4">
                            <label for="pType" class="form-label">Type</label>
                            <select name="type" id="pType" class="form-control">
                                <option value="announcement">Announcement</option>
                                <option value="offer">Offer</option>
                                <option value="blog">Blog</option>
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="pBody" class="form-label">Body</label>
                            <textarea name="body" id="pBody" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="form-group col-md-8">
                            <label for="pLink" class="form-label">Link (optional)</label>
                            <input type="text" name="link" id="pLink" class="form-control" placeholder="https://..." />
                        </div>
                        <div class="form-group col-md-4">
                            <label for="pOrder" class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" id="pOrder" class="form-control" value="0" />
                        </div>
                        <div class="form-group col-md-12">
                            <div class="checkbox checkbox-primary no-margin-bottom">
                                <input type="checkbox" id="pEnabled" name="enabled" value="1" checked />
                                <label for="pEnabled" class="strong">Enabled</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {!! csrf_field() !!}
                    <button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
