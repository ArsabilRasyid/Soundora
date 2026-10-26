@php $edit = isset($p); @endphp
<div class="modal fade" id="{{ $edit ? 'editPl' : 'newPl' }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" method="post" action="{{ $edit ? route('playlists.update', $p) : route('playlists.store') }}">
      @csrf @if ($edit) @method('PUT') @endif
      <div class="modal-header border-0"><h5 class="modal-title">{{ $edit ? 'Edit playlist' : 'Buat playlist' }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
      <div class="modal-body vstack gap-3">
        <div><label class="form-label">Nama</label><input name="name" class="form-control" required maxlength="60" value="{{ $edit ? $p->name : '' }}" placeholder="Belajar Coding"></div>
        <div><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" rows="3" maxlength="255" placeholder="Kumpulan lagu untuk menemani belajar.">{{ $edit ? $p->description : '' }}</textarea></div>
      </div>
      <div class="modal-footer border-0"><button class="btn btn-accent w-100">{{ $edit ? 'Simpan perubahan' : 'Buat playlist' }}</button></div>
    </form>
  </div>
</div>
