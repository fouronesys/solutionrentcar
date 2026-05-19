<?php include "layout/header.php"; ?>

<style>
.privacy-hero{
  background:#0f172a;
  color:#fff;
  padding:150px 0 90px;
}
.privacy-hero h1{
  font-size:56px;
  font-weight:900;
  letter-spacing:-1.5px;
  color:#fff;
  margin-bottom:8px;
}
.privacy-hero .meta{
  color:rgba(255,255,255,.65);
  font-size:14px;
}
.privacy-body{
  background:#fff;
  padding:70px 0;
  color:#1f2937;
  line-height:1.7;
}
.privacy-body .container{ max-width:880px; }
.privacy-body h2{
  color:#0f172a;
  margin-top:36px;
  margin-bottom:14px;
  font-weight:800;
  font-size:24px;
}
.privacy-body p, .privacy-body li{
  font-size:16px;
  color:#374151;
}
.privacy-body ul{
  padding-left:22px;
  margin-bottom:14px;
}
.privacy-body a{
  color:#ff5d00;
  font-weight:600;
}
</style>

<section class="privacy-hero">
  <div class="container">
    <h1><?php echo __('privacy_title'); ?></h1>
    <p class="meta"><?php echo __('privacy_updated'); ?>: 19/05/2026</p>
  </div>
</section>

<section class="privacy-body">
  <div class="container">

    <p><?php echo __('privacy_intro'); ?></p>

    <h2><?php echo __('privacy_h_collect'); ?></h2>
    <ul>
      <li><?php echo __('privacy_collect_account'); ?></li>
      <li><?php echo __('privacy_collect_booking'); ?></li>
      <li><?php echo __('privacy_collect_docs'); ?></li>
      <li><?php echo __('privacy_collect_device'); ?></li>
    </ul>

    <h2><?php echo __('privacy_h_use'); ?></h2>
    <ul>
      <li><?php echo __('privacy_use_1'); ?></li>
      <li><?php echo __('privacy_use_2'); ?></li>
      <li><?php echo __('privacy_use_3'); ?></li>
      <li><?php echo __('privacy_use_4'); ?></li>
    </ul>

    <h2><?php echo __('privacy_h_share'); ?></h2>
    <p><?php echo __('privacy_share_text'); ?></p>

    <h2><?php echo __('privacy_h_storage'); ?></h2>
    <p><?php echo __('privacy_storage_text'); ?></p>

    <h2><?php echo __('privacy_h_rights'); ?></h2>
    <p><?php echo str_replace('[email]', '<a href="mailto:admin@fourone.com.do">admin@fourone.com.do</a>', __('privacy_rights_text')); ?></p>

    <h2><?php echo __('privacy_h_push'); ?></h2>
    <p><?php echo __('privacy_push_text'); ?></p>

    <h2><?php echo __('privacy_h_children'); ?></h2>
    <p><?php echo __('privacy_children_text'); ?></p>

    <h2><?php echo __('privacy_h_changes'); ?></h2>
    <p><?php echo __('privacy_changes_text'); ?></p>

    <h2><?php echo __('privacy_h_contact'); ?></h2>
    <p>
      Solutions Rent Car<br>
      <?php echo __('privacy_email'); ?>: <a href="mailto:admin@fourone.com.do">admin@fourone.com.do</a><br>
      Web: <a href="https://solutionsrentcar.do">solutionsrentcar.do</a>
    </p>

  </div>
</section>

<?php include "layout/footer.php"; ?>
