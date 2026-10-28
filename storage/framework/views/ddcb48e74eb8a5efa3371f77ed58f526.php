<?php $__env->startSection('title', $album->name); ?>
<?php $__env->startSection('content'); ?>
<?php $mins = (int) ceil($album->songs->sum('duration') / 60); ?>
<div class="d-flex gap-4 align-items-end flex-wrap mb-4">
  <div class="hero-cover" style="background:<?php echo e($album->cover_css); ?>"><?php if (! ($album->cover_path)): ?><i class="bi bi-disc"></i><?php endif; ?></div>
  <div class="min-w-0">
    <small class="text-secondary fw-semibold">Album<?php echo e($album->genre ? ' · '.$album->genre : ''); ?></small>
    <h1 class="display-5 fw-bold mb-1"><?php echo e($album->name); ?></h1>
    <p class="mb-1">
      <?php if($album->artist): ?><a class="text-decoration-none fw-semibold text-white" href="<?php echo e(route('artists.show', $album->artist)); ?>"><?php echo e($album->artist->name); ?></a><?php endif; ?>
      <span class="text-secondary"><?php echo e($album->year ? '· '.$album->year : ''); ?></span>
    </p>
    <small class="text-secondary"><?php echo e($album->songs->count()); ?> lagu<?php echo e($mins ? ', ±'.$mins.' menit' : ''); ?></small>
  </div>
</div>
<div class="d-flex flex-wrap gap-2 mb-4">
  <button class="btn btn-accent" data-playall="#list"><i class="bi bi-play-fill"></i> Putar album</button>
  <button class="btn btn-ghost" data-playall="#list" data-shuffle="1"><i class="bi bi-shuffle"></i> Acak</button>
  <div class="dropdown">
    <button class="btn btn-ghost dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-plus-lg"></i> Simpan semua ke playlist</button>
    <ul class="dropdown-menu">
      <?php $__empty_1 = true; $__currentLoopData = $playlists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <li><form method="post" action="<?php echo e(route('albums.playlist', [$album, $pl])); ?>"><?php echo csrf_field(); ?><button class="dropdown-item"><?php echo e($pl->name); ?></button></form></li>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <li><span class="dropdown-item-text text-secondary">Buat playlist dulu</span></li>
      <?php endif; ?>
    </ul>
  </div>
</div>
<div id="list" class="vstack gap-1" data-tracklist>
  <?php $__currentLoopData = $album->songs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php echo $__env->make('partials.track', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\soundora\resources\views/albums/show.blade.php ENDPATH**/ ?>