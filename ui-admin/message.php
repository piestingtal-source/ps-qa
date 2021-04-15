<?php if (!defined('ABSPATH')) die('Kein direkter Zugriff erlaubt!'); ?>

<?php $msg = __( 'Einstellungen gespeichert.', QA_TEXTDOMAIN ); ?>

<?php if ( isset( $_POST['save'] ) ): ?>
<div class="updated below-h2" id="message">
    <p><?php echo $msg; ?></p>
</div>
<?php endif; ?>
