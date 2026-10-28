<?php $__env->startSection('title', 'Album'); ?>
<?php $__env->startSection('content'); ?>
<div class="d-flex flex-wrap gap-3 justify-content-between align-items-center mb-4">
  <h1 class="h2 fw-bold m-0">Album</h1>
  <form method="get" class="d-flex gap-2">
    <input name="q" value="<?php echo e($q); ?>" class="form-control" placeholder="Cari album atau artis">
    <button class="btn btn-ghost" aria-label="Cari"><i class="bi bi-search"></i></button>
  </form>
</div>
<div class="row g-3 row-cols-2 row-cols-md-3 row-cols-xl-5">
  <?php $__empty_1 = true; $__currentLoopData = $albums; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $al): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="col">
      <a href="<?php echo e(route('albums.show', $al)); ?>" class="pl-card">
        <div class="pl-cover" style="background:<?php echo e($al->cover_css); ?>"><?php if (! ($al->cover_path)): ?><i class="bi bi-disc"></i><?php endif; ?></div>
        <div class="fw-bold text-truncate"><?php echo e($al->name); ?></div>
        <small class="text-secondary d-block text-truncate"><?php echo e($al->artist->name ?? 'Artis tidak diketahui'); ?><?php echo e($al->year ? ', '.$al->year : ''); ?></small>
        <small class="text-secondary"><?php echo e($al->songs_count); ?> lagu</small>
      </a>
    </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="col-12 text-center text-secondary py-5">Belum ada album. Jalankan <code>php artisan soundora:import</code> atau tambahkan lagu dengan nama album.</div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\soundora\resources\views/albums/index.blade.php ENDPATH**/ ?>