<?php $edit = isset($p); ?>
<div class="modal fade" id="<?php echo e($edit ? 'editPl' : 'newPl'); ?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" method="post" action="<?php echo e($edit ? route('playlists.update', $p) : route('playlists.store')); ?>">
      <?php echo csrf_field(); ?> <?php if($edit): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
      <div class="modal-header border-0"><h5 class="modal-title"><?php echo e($edit ? 'Edit playlist' : 'Buat playlist'); ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
      <div class="modal-body vstack gap-3">
        <div><label class="form-label">Nama</label><input name="name" class="form-control" required maxlength="60" value="<?php echo e($edit ? $p->name : ''); ?>" placeholder="Belajar Coding"></div>
        <div><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" rows="3" maxlength="255" placeholder="Kumpulan lagu untuk menemani belajar."><?php echo e($edit ? $p->description : ''); ?></textarea></div>
      </div>
      <div class="modal-footer border-0"><button class="btn btn-accent w-100"><?php echo e($edit ? 'Simpan perubahan' : 'Buat playlist'); ?></button></div>
    </form>
  </div>
</div>
<?php /**PATH C:\xampp\htdocs\soundora\resources\views/partials/playlist-modal.blade.php ENDPATH**/ ?>