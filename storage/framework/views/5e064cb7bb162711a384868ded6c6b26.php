<div class="track" data-id="<?php echo e($s->id); ?>" data-title="<?php echo e($s->title); ?>" data-artist="<?php echo e($s->artist->name); ?>" data-src="<?php echo e($s->audio_url); ?>" data-cover="<?php echo e($s->cover_css); ?>">
  <?php if(isset($playlist)): ?><i class="bi bi-grip-vertical drag" title="Seret untuk mengubah urutan"></i><?php endif; ?>
  <button type="button" class="ibtn" data-play title="Putar"><i class="bi bi-play-fill"></i></button>
  <div class="cov" style="background:<?php echo e($s->cover_css); ?>"></div>
  <div class="t-main"><div class="t-title text-truncate"><?php echo e($s->title); ?><?php if($s->isPreview()): ?><span class="badge rounded-pill ms-2" style="background:var(--panel2);color:var(--mut)" title="Hanya cuplikan 30 detik. Menu ⋯ → Edit untuk mengganti dengan file lengkap.">30 dtk</span><?php endif; ?></div><a class="small text-secondary text-decoration-none" href="<?php echo e(route('artists.show', $s->artist)); ?>"><?php echo e($s->artist->name); ?></a></div>
  <div class="t-album d-none d-md-block text-secondary text-truncate"><?php if($s->album): ?><a class="text-secondary text-decoration-none" href="<?php echo e(route('albums.show', $s->album)); ?>"><?php echo e($s->album->name); ?></a><?php else: ?> — <?php endif; ?></div>
  <button type="button" class="ibtn" data-like data-url="<?php echo e(route('songs.like', $s)); ?>" title="Suka"><i class="bi <?php echo e($s->is_liked ? 'bi-heart-fill text-acc' : 'bi-heart'); ?>"></i></button>
  <small class="text-secondary t-dur"><?php echo e($s->duration_label); ?></small>
  <div class="dropdown">
    <button type="button" class="ibtn" data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}' aria-label="Menu lagu"><i class="bi bi-three-dots"></i></button>
    <ul class="dropdown-menu dropdown-menu-end">
      <li><a class="dropdown-item" href="<?php echo e(route('songs.edit', $s)); ?>"><i class="bi bi-pencil me-2"></i>Edit / ganti audio</a></li>
      <?php if(isset($playlist)): ?>
        <li><form method="post" action="<?php echo e(route('playlists.songs.remove', [$playlist, $s])); ?>" data-turbo-confirm="Keluarkan “<?php echo e($s->title); ?>” dari playlist ini? Lagunya tetap ada di library."><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
          <button class="dropdown-item"><i class="bi bi-dash-circle me-2"></i>Keluarkan dari playlist</button></form></li>
      <?php else: ?>
        <li><h6 class="dropdown-header">Tambah ke playlist</h6></li>
        <?php $__empty_1 = true; $__currentLoopData = $playlists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <li><form method="post" action="<?php echo e(route('playlists.songs.add', [$pl, $s])); ?>"><?php echo csrf_field(); ?><button class="dropdown-item"><?php echo e($pl->name); ?></button></form></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <li><span class="dropdown-item-text text-secondary">Belum ada playlist</span></li>
        <?php endif; ?>
        <li><hr class="dropdown-divider"></li>
        <li><form method="post" action="<?php echo e(route('songs.destroy', $s)); ?>" data-turbo-confirm="Hapus “<?php echo e($s->title); ?>” dari SOUNDORA? Lagu juga hilang dari semua playlist."><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
          <button class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Hapus lagu</button></form></li>
      <?php endif; ?>
    </ul>
  </div>
</div>
<?php /**PATH C:\xampp\htdocs\soundora\resources\views/partials/track.blade.php ENDPATH**/ ?>