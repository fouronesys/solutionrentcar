<!-- contact.php -->

<?php include "layout/header.php"; ?>

<style>

.contact-hero{
    background:linear-gradient(rgba(0,0,0,.65),rgba(0,0,0,.65)),
    url('../../<?php echo StockData::getFPrincipal(1)->img_3; ?>') center center/cover no-repeat;
    min-height:420px;
    display:flex;
    align-items:center;
    justify-content:center;
    text-align:center;
    color:#fff;
}

.contact-hero h1{
    font-size:70px;
    font-weight:900;
    margin-bottom:15px;
}

.contact-hero p{
    font-size:18px;
    opacity:.9;
}

.contact-section{
    padding:90px 0;
    background:#f8fafc;
}

.contact-card{
    background:#fff;
    border-radius:30px;
    padding:50px;
    box-shadow:0 15px 50px rgba(0,0,0,.06);
}

.contact-title{
    font-size:40px;
    font-weight:900;
    margin-bottom:15px;
    color:#111827;
}

.contact-subtitle{
    color:#64748b;
    margin-bottom:40px;
    line-height:1.8;
}

.form-control{
    height:58px;
    border-radius:18px;
    border:1px solid rgba(0,0,0,.08);
    box-shadow:none !important;
    padding-left:18px;
}

textarea.form-control{
    height:180px;
    padding-top:18px;
    resize:none;
}

.contact-btn{
    background:rgba(<?php echo $mainColor; ?>,1);
    color:#fff;
    border:none;
    height:58px;
    border-radius:18px;
    padding:0 35px;
    font-weight:800;
    transition:.3s;
}

.contact-btn:hover{
    background:#111827;
}

.contact-info{
    background:#111827;
    color:#fff;
    border-radius:30px;
    padding:50px;
    height:100%;
}

.contact-info h3{
    font-size:32px;
    font-weight:900;
    margin-bottom:30px;
     color:#fff;
}

.contact-info-item{
    margin-bottom:25px;
}

.contact-info-item strong{
    display:block;
    margin-bottom:8px;
    color:rgba(<?php echo $mainColor; ?>,1);
}

@media(max-width:991px){

    .contact-hero h1{
        font-size:48px;
    }

    .contact-card,
    .contact-info{
        padding:30px;
    }

}

</style>

<section class="contact-hero">

    <div class="container">

        <h1 style="color:white;"><?php echo __('contact_title'); ?></h1>

        <p><?php echo __('contact_sub'); ?></p>

    </div>

</section>

<section class="contact-section">

<div class="container">

<div class="row">

<div class="col-lg-7 mb-4">

<div class="contact-card">

<h2 class="contact-title">
<?php echo __('send_message'); ?>
</h2>

<p class="contact-subtitle">
<?php echo __('send_sub'); ?>
</p>

<form method="post">

<div class="row">

<div class="col-md-6 mb-4">
<input type="text" class="form-control" placeholder="<?php echo __('full_name'); ?>" required>
</div>

<div class="col-md-6 mb-4">
<input type="email" class="form-control" placeholder="<?php echo __('email_addr'); ?>" required>
</div>

<div class="col-md-6 mb-4">
<input type="text" class="form-control" placeholder="<?php echo __('phone_num'); ?>">
</div>

<div class="col-md-6 mb-4">
<input type="text" class="form-control" placeholder="<?php echo __('subject'); ?>">
</div>

<div class="col-12 mb-4">
<textarea class="form-control" placeholder="<?php echo __('message'); ?>"></textarea>
</div>

<div class="col-12">
<button type="submit" class="contact-btn">
<?php echo __('send_btn'); ?>
</button>
</div>

</div>

</form>

</div>

</div>

<div class="col-lg-5 mb-4">

<div class="contact-info">

<h3>
<?php echo __('contact_info'); ?>
</h3>

<div class="contact-info-item">
<strong><?php echo __('address'); ?></strong>
<?php echo isset($footerAddressSafe) ? $footerAddressSafe : 'Dominican Republic'; ?>
</div>

<div class="contact-info-item">
<strong><?php echo __('email'); ?></strong>
<?php echo isset($footerEmailSafe) ? $footerEmailSafe : 'info@email.com'; ?>
</div>

<div class="contact-info-item">
<strong><?php echo __('phone'); ?></strong>
<?php echo isset($footerPhoneSafe) ? $footerPhoneSafe : '(000) 000-0000'; ?>
</div>

<div class="contact-info-item">
<strong><?php echo __('hours'); ?></strong>
<?php echo __('hours_val'); ?><br>
8:00 AM - 6:00 PM
</div>

</div>

</div>

</div>

</div>

</section>

<?php include "layout/footer.php"; ?>