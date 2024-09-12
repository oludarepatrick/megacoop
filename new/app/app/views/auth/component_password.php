<?= form_open('auth/login') ?>
<div class="form-group">
    <label for="password">PASSWORD</label>
    <p>Please enter your password</p>
    <div class="input-group">
        <input name="identity" type="hidden" value="<?= $identity ?>">
        <input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="<?= lang('password') ?>">
    </div>
</div>
<div class="form-group mb-0 text-center">
    <button class="btn btn-primary btn-block btn-lg" type="submit" id="login" onclick="auth_loader()"><i class="mdi mdi-login"></i> <?= lang('login') ?> </button>
    <button id="auth" style="display: none" class="btn btn-primary btn-block btn-lg" type="button" disabled>
        <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
        <span class="">Authentication...</span>
    </button>
</div>

<?= form_close() ?>