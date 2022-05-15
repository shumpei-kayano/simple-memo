@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        新規メモ作成
    </div>
    <form class="card-body" action="/store" method="POST">
        @csrf
        <div class="mb-3">
            <textarea class="form-control" name="content" rows="3" placeholder="ここにメモを入力"></textarea>
        </div>
        @foreach ($tags as $t)
        <div class="form-check form-check-inline mb-3">
            <input class="form-check-input" type="checkbox" name="tags[]" value="{{ $t['id'] }}" id="{{ $t['id'] }}">
            <label class="form-check-label" for="{{ $t['id'] }}">
              {{ $t['name'] }}
            </label>
          </div>
        @endforeach
        <input type="text" class="form-control w-50 mb-3" name="new_tag" placeholder="新しいタグを入力">
        <button type="submit" class="btn btn-primary">保存</button>
    </form>
</div>
@endsection
