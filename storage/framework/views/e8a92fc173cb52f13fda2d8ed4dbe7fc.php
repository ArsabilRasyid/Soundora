<?php $__env->startSection('title', $artist->name); ?>
<?php $__env->startSection('content'); ?>
<div class="d-flex gap-4 align-items-end flex-wrap mb-4">
  <div class="hero-cover" style="border-radius:50%;background:<?php echo e(\App\Models\Song::gradient($artist->id + 300)); ?>"><i class="bi bi-person-fill"></i></div>
  <div>
    <small class="text-secondary fw-semibold">Artis</small>
    <h1 class="display-4 fw-bold mb-1"><?php echo e($artist->name); ?></h1>
    <small class="text-secondary"><?php echo e($artist->songs->count()); ?> lagu, <?php echo e($artist->albums->count()); ?> album</small>
  </div>
</div>
<div class="d-flex gap-2 mb-4">
  <button class="btn btn-accent" data-playall="#list"><i class="bi bi-play-fill"></i> Putar</button>
  <button class="btn btn-ghost" data-playall="#list" data-shuffle="1"><i class="bi bi-shuffle"></i> Acak</button>
</div>
<h2 class="h5 fw-bold mb-3">Album</h2>
<div class="row g-3 row-cols-2 row-cols-md-4 row-cols-xl-6 mb-5">
  <?php $__currentLoopData = $artist->albums; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $al): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col"><a href="<?php echo e(route('albums.show', $al)); ?>" class="pl-card">
      <div class="pl-cover" style="background:<?php echo e($al->cover_css); ?>"><?php if (! ($al->cover_path)): ?><i class="bi bi-disc"></i><?php endif; ?></div>
      <div class="fw-bold text-truncate"><?php echo e($al->name); ?></div>
      <small class="text-secondary"><?php echo e($al->year); ?></small>
    </a></div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<h2 class="h5 fw-bold mb-3">Semua lagu</h2>
<div id="list" class="vstack gap-1" data-tracklist>
  <?php $__currentLoopData = $artist->songs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php echo $__env->make('partials.track', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\soundora\resources\views/artists/show.blade.php ENDPATH**/ ?>