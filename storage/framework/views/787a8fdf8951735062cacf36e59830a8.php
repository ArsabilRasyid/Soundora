<?php $__env->startSection('title', isset($song) ? 'Edit Lagu' : 'Tambah Lagu'); ?>
<?php $__env->startSection('content'); ?>
    <?php $edit = isset($song); ?>
    <h1 class="h2 fw-bold mb-4">
        <?php echo e($edit ? 'Edit lagu' : 'Tambah lagu'); ?></h1>
    <?php if($edit && $song->isPreview()): ?>
        <div class="alert alert-info" style="max-width:640px">Lagu ini hanya cuplikan 30 detik dari iTunes. Unggah file
            lengkap milikmu di bawah untuk menggantinya.</div>
    <?php endif; ?>
    <form method="post" action="<?php echo e($edit ? route('songs.update', $song) : route('songs.store')); ?>"
        enctype="multipart/form-data" class="vstack gap-3" style="max-width:640px">
        <?php echo csrf_field(); ?> <?php if($edit): ?>
            <?php echo method_field('PUT'); ?>
        <?php endif; ?>
        <input type="hidden" name="duration" id="duration" value="<?php echo e(old('duration', $song->duration ?? 0)); ?>">
        <div><label class="form-label">Judul</label><input name="title" class="form-control" required maxlength="120"
                value="<?php echo e(old('title', $song->title ?? '')); ?>"></div>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Artis</label><input name="artist" list="artists"
                    class="form-control" required maxlength="80"
                    value="<?php echo e(old('artist', $song->artist->name ?? '')); ?>"><datalist id="artists">
                    <?php $__currentLoopData = $artists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($a); ?>">
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </datalist></div>
            <div class="col-md-6"><label class="form-label">Album <span
                        class="text-secondary">(opsional)</span></label><input name="album" class="form-control"
                    maxlength="80" value="<?php echo e(old('album', $song->album->name ?? '')); ?>"></div>
        </div>
        <div><label class="form-label"><?php echo e($edit ? 'Ganti file audio' : 'File audio'); ?> <span
                    class="text-secondary">(<?php echo e($edit ? 'kosongkan jika tidak diganti · ' : ''); ?>mp3, wav, ogg, m4a, flac ·
                    maks 30 MB)</span></label><input type="file" id="audio" name="audio" accept="audio/*"
                class="form-control"></div>
        <div><label class="form-label">…atau tautan audio</label><input type="url" id="audio_url" name="audio_url"
                class="form-control" placeholder="https://contoh.com/lagu.mp3"
                value="<?php echo e(old('audio_url', $edit && $song->isRemote() ? $song->audio_path : '')); ?>"></div>
        <div><label class="form-label">Sampul <span class="text-secondary">(opsional)</span></label><input type="file"
                name="cover" accept="image/*" class="form-control"></div>
        <div class="d-flex gap-2"><button type="submit"
                class="btn btn-accent"><?php echo e($edit ? 'Simpan perubahan' : 'Simpan lagu'); ?></button><a
                href="<?php echo e(route('songs.index')); ?>" class="btn btn-ghost">Batal</a></div>
    </form>
    <script>
        (() => {
            const f = document.getElementById('audio'),
                u = document.getElementById('audio_url'),
                d = document.getElementById('duration');
            const probe = src => {
                const a = new Audio();
                a.preload = 'metadata';
                a.onloadedmetadata = () => {
                    d.value = Math.round(a.duration) || 0;
                };
                a.src = src;
            };
            f.onchange = () => f.files[0] && probe(URL.createObjectURL(f.files[0]));
            u.onchange = () => u.value && probe(u.value);
        })();
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\soundora\resources\views/songs/create.blade.php ENDPATH**/ ?>