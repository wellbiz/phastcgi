
form:
<?=form_open(); ?>
<?=form_input('test_field'); ?>
<?=form_submit('send', 'Send'); ?>

<? var_dump($_POST); ?>
