<?php $__env->startSection('title', $liked ? 'Lagu Disukai' : 'Library'); ?>
<?php $__env->startSection('content'); ?>
<div class="d-flex flex-wrap gap-3 justify-content-between align-items-center mb-3">
  <h1 class="h2 fw-bold m-0"><?php echo e($liked ? 'Lagu Disukai' : 'Library'); ?></h1>
  <form method="get" class="d-flex gap-2">
    <?php if($liked): ?><input type="hidden" name="liked" value="1"><?php endif; ?>
    <input name="q" value="<?php echo e($q); ?>" class="form-control" placeholder="Cari judul atau artis">
    <button class="btn btn-ghost"><i class="bi bi-search"></i></button>
  </form>
</div>
<div class="d-flex gap-2 mb-4">
  <button class="btn btn-accent" data-playall="#list"><i class="bi bi-play-fill"></i> Putar semua</button>
  <button class="btn btn-ghost" data-playall="#list" data-shuffle="1"><i class="bi bi-shuffle"></i> Acak</button>
  <a href="<?php echo e(route('songs.create')); ?>" class="btn btn-ghost ms-auto"><i class="bi bi-plus-lg"></i> Tambah lagu</a>
</div>
<div id="list" class="vstack gap-1" data-tracklist>
  <?php $__empty_1 = true; $__currentLoopData = $songs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php echo $__env->make('partials.track', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="text-center text-secondary py-5"><?php echo e($liked ? 'Belum ada lagu yang disukai. Tekan ikon hati pada lagu.' : 'Tidak ada lagu. Tambahkan lagu pertamamu.'); ?></div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\soundora\resources\views/songs/index.blade.php ENDPATH**/ ?>