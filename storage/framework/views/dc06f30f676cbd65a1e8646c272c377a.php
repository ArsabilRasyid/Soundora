<?php $__env->startSection('title', 'Playlist'); ?>
<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h2 fw-bold m-0">Playlist</h1>
  <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#newPl"><i class="bi bi-plus-lg"></i> Playlist baru</button>
</div>
<div class="row g-3 row-cols-2 row-cols-md-3 row-cols-xl-4">
  <?php $__empty_1 = true; $__currentLoopData = $lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="col">
      <a href="<?php echo e(route('playlists.show', $pl)); ?>" class="pl-card">
        <div class="pl-cover" style="background:<?php echo e(\App\Models\Song::gradient($pl->id)); ?>"><i class="bi bi-music-note-beamed"></i></div>
        <div class="fw-bold text-truncate"><?php echo e($pl->name); ?></div>
        <small class="text-secondary d-block text-truncate"><?php echo e($pl->description ?: 'Tanpa deskripsi'); ?></small>
        <small class="text-secondary"><?php echo e($pl->songs_count); ?> lagu</small>
      </a>
    </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="col-12 text-center text-secondary py-5">Belum ada playlist. Buat yang pertama, misalnya “Belajar Coding”.</div>
  <?php endif; ?>
</div>
<?php echo $__env->make('partials.playlist-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\soundora\resources\views/playlists/index.blade.php ENDPATH**/ ?>