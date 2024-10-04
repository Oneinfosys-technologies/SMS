<?php $__env->startSection('content'); ?>
    <?php echo $body; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('email.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/school.oneinfosys.com/public_html/resources/views/email/index.blade.php ENDPATH**/ ?>