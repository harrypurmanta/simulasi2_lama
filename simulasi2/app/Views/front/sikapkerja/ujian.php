<?php
$request = \Config\Services::request();
$session = \Config\Services::session();
$skGroupSeg = (int)$request->uri->getSegment(3);
$isNewSk    = !empty($is_new_sk) || in_array($skGroupSeg, [9, 10]);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Bintang Timur Prestasi - Sikap Kerja</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="<?= base_url() ?>/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/bower_components/Ionicons/css/ionicons.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/dist/css/custom.css">
    <link rel="stylesheet" href="<?= base_url() ?>/dist/css/skins/_all-skins.min.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <style>
    .d-none {
        display: none !important;
    }

    #loader-wrapper {
        display: flex;
        position: fixed;
        z-index: 1060;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        flex-direction: row;
        align-items: center;
        justify-content: center;
        padding: 0.625em;
        overflow-x: hidden;
        transition: background-color 0.1s;
        background-color: rgb(253 253 253 / 58%);
        -webkit-overflow-scrolling: touch;
    }

    .loader {
        border: 10px solid #f3f3f3;
        border-radius: 50%;
        border-top: 10px solid #3af3f5;
        border-bottom: 10px solid #3abcec;
        width: 50px;
        height: 50px;
        -webkit-animation: spin 2s linear infinite;
        animation: spin 2s linear infinite;
        margin: 1.75rem auto;
    }

    @-webkit-keyframes spin {
        0% { -webkit-transform: rotate(0deg); }
        100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    <?php if ($isNewSk): ?>
    body.sk-cat-page {
        background-color: #f4f6f9;
        font-family: 'Segoe UI', 'Source Sans Pro', Arial, sans-serif;
        margin: 0;
        padding: 0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        color: #111827;
        user-select: none;
    }

    .sk-cat-topbar {
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
        height: 60px;
        padding: 0 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
    }

    .sk-cat-brand {
        font-size: 16px;
        font-weight: 700;
        color: #1e3a8a;
        text-decoration: none !important;
    }

    .sk-user-pill {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        border: 1px solid #e5e7eb;
        border-radius: 999px;
        padding: 5px 16px 5px 8px;
        background: #ffffff;
    }

    .sk-user-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #1d6ed8;
        color: #ffffff;
        font-size: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sk-user-meta {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
        text-align: left;
    }

    .sk-user-name {
        font-size: 11.5px;
        font-weight: 700;
        color: #111827;
        text-transform: uppercase;
    }

    .sk-user-sub {
        font-size: 10px;
        color: #6b7280;
        text-transform: uppercase;
    }

    .sk-cat-main {
        flex: 1;
        width: 100%;
        max-width: 1260px;
        margin: 18px auto 74px;
        padding: 0 20px;
    }

    .sk-cat-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        padding: 18px 32px 38px;
        min-height: 520px;
    }

    .sk-kolom-header {
        text-align: center;
        font-size: 18px;
        font-weight: 700;
        color: #111827;
        padding-bottom: 14px;
        border-bottom: 1px solid #f1f5f9;
    }

    .sk-new-body {
        width: 100%;
    }

    .sk-new-ref-box {
        background: #f5f6f8;
        border-radius: 8px;
        padding: 18px 24px 16px;
        margin-top: 14px;
        text-align: center;
    }

    .sk-new-caption {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 10px;
        font-weight: 400;
    }

    .sk-new-ref-row {
        display: inline-flex;
        align-items: flex-end;
        justify-content: center;
        gap: 24px;
        flex-wrap: wrap;
    }

    .sk-new-ref-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 40px;
    }

    .sk-new-ref-symbol {
        min-height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sk-new-ref-char {
        font-size: 40px;
        font-weight: 700;
        color: #111827;
        line-height: 1.1;
    }

    .sk-new-ref-img {
        width: 56px;
        height: 56px;
        object-fit: contain;
    }

    .sk-new-ref-letter {
        font-size: 13px;
        font-weight: 700;
        color: #111827;
        margin-top: 6px;
    }

    .sk-new-q-section {
        margin-top: 32px;
        text-align: center;
    }

    .sk-new-q-row {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 18px;
        margin-top: 4px;
        min-height: 56px;
    }

    .sk-new-q-item {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sk-new-q-char {
        font-size: 40px;
        font-weight: 700;
        color: #111827;
        line-height: 1.1;
    }

    .sk-new-q-img {
        width: 58px;
        height: 58px;
        object-fit: contain;
    }

    .sk-new-opt-section {
        margin-top: 28px;
        text-align: center;
    }

    .sk-new-opt-row {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 18px;
        margin-top: 14px;
        flex-wrap: wrap;
    }

    .sk-new-opt-btn {
        width: 110px;
        height: 100px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: border-color 0.12s ease, background-color 0.12s ease, transform 0.06s ease;
        outline: none;
        padding: 8px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    }

    .sk-new-opt-btn:hover {
        border-color: #2563eb;
        background: #f8fbff;
        box-shadow: 0 3px 10px rgba(37, 99, 235, 0.08);
    }

    .sk-new-opt-btn:active {
        transform: scale(0.97);
        background: #eff6ff;
    }

    .sk-new-opt-symbol {
        min-height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sk-new-opt-char {
        font-size: 30px;
        font-weight: 700;
        color: #111827;
        line-height: 1.1;
    }

    .sk-new-opt-img {
        width: 46px;
        height: 46px;
        object-fit: contain;
    }

    .sk-new-opt-letter {
        font-size: 12.5px;
        font-weight: 600;
        color: #4b5563;
    }

    .sk-cat-bottombar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 54px;
        background: #ffffff;
        border-top: 1px solid #e5e7eb;
        padding: 0 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        z-index: 100;
    }

    .sk-pill-mode {
        background: #1d6ed8;
        color: #ffffff;
        font-size: 12.5px;
        font-weight: 600;
        padding: 6px 18px;
        border-radius: 999px;
    }

    .sk-pill-timer {
        background: #fde8e8;
        color: #b91c1c;
        font-size: 14.5px;
        font-weight: 700;
        padding: 6px 16px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-variant-numeric: tabular-nums;
    }

    .sk-prep-message {
        padding: 90px 20px;
        text-align: center;
        font-size: 18px;
        font-weight: 600;
        color: #4b5563;
    }
    <?php endif; ?>
    </style>
</head>

<?php if ($isNewSk): ?>
<body class="sk-cat-page">
    <header class="sk-cat-topbar">
        <a href="<?= base_url() ?>/sikapkerja" class="sk-cat-brand">Simulasi CAT Polri</a>
        <div class="sk-user-pill">
            <div class="sk-user-avatar"><?= esc($session->user_id ?? '1') ?></div>
            <div class="sk-user-meta">
                <span class="sk-user-name"><?= esc($session->person_nm ?: $session->user_nm ?: 'PESERTA') ?></span>
                <span class="sk-user-sub">No. <?= esc(str_pad((string)($session->user_id ?? '1'), 3, '0', STR_PAD_LEFT)) ?> / <?= esc($session->satuan ?: 'BINTANG TIMUR PRESTASI') ?></span>
            </div>
        </div>
    </header>

    <main class="sk-cat-main">
        <div class="sk-cat-card">
            <div class="sk-kolom-header">
                <span id="lb_kolom">Kolom 1</span>
            </div>
            <div id="dv_soal"></div>
            <div id="dv_button"></div>
        </div>
    </main>

    <footer class="sk-cat-bottombar">
        <span class="sk-pill-mode">Sikap Kerja</span>
        <span class="sk-pill-timer">
            <i class="fa fa-clock-o"></i>
            <span id="countdown">01:05</span>
        </span>
    </footer>

    <div class="d-none" id="loader-wrapper">
        <div class="loader"></div>
    </div>
<?php else: ?>
<body class="hold-transition skin-blue layout-top-nav">
    <div class="wrapper">
        <header class="main-header">
            <?= $this->include('front/navbar') ?>
        </header>
        <div class="content-wrapper">
            <div class="container">
                <section class="content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="bg-gray col-md-8" style="border-radius:5px;">
                                <div class="col-md-12" style="margin-top:10px;">
                                    <label for="">Tahapan Ujian</label>
                                </div>
                                <input type="hidden" id="inp_group_id">
                                <div class='col-lg-3' style='border-radius:10px;'>
                                    <div class='small-box bg-green' style='border-radius:10px;border:1px solid green;'>
                                        <div class='inner text-center'>
                                        Sikap Kerja
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray col-md-3 text-center"
                                style="border-radius:5px;margin-left:10px;height:85px;">
                                <span style="margin-top:15px;">Waktu</span><br>
                                <label style="font-size:30px;" id="countdown">00:00</label>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <div class="col-md-12">
                            <div class="bg-gray col-md-8" style="border-radius:5px;">
                                <div style="margin-top:10px;text-align:center;">
                                    <label style="font-size:20px;" for="kolom" id="lb_kolom">Kolom</label>
                                </div>
                                <div id="dv_soal" class="col-md-12" style="min-height:500px;border-radius:5px;">
                                    
                                </div>
                                <div class="row" style="margin-top:10px;margin-bottom:10px;">
                                    <div class="col-md-12" id="dv_button">

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>
            </div>
        </div>
        <div class="d-none" id='loader-wrapper'>
            <div class="loader"></div>
        </div>
    </div>
<?php endif; ?>

    <script src="<?= base_url() ?>/bower_components/jquery/dist/jquery.min.js"></script>
    <script src="<?= base_url() ?>/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="<?= base_url() ?>/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
    <script src="<?= base_url() ?>/bower_components/fastclick/lib/fastclick.js"></script>
    <script src="<?= base_url() ?>/dist/js/adminlte.min.js"></script>
    <script>
    var timers;
    var isNewSk = <?= $isNewSk ? 'true' : 'false' ?>;
    var isSubmitting = false;

    $(document).ready(function() {
        setTimeout(() => {
            startujian("start","","","",4,0,1,98);
        }, 400);
    });

    function startujian(proc,pilihan_nm,jawaban_id,soal_id,group_id,no_soal,kolom_id,materi) {
        if (proc === "next" && isSubmitting) {
            return;
        }
        if (proc === "next") {
            isSubmitting = true;
        }
        $.ajax({
            url: "<?= base_url('sikapkerja/sikapkerjaujian') ?>",
            type: "post",
            dataType: "json",
            data: {
                "proc": proc,
                "jawaban_id": jawaban_id,
                "soal_id": soal_id,
                "no_soal": no_soal,
                "pilihan_nm": pilihan_nm,
                "group_id": group_id,
                "materi": materi,
                "kolom_id": kolom_id,
                "sk_group_id": <?= $skGroupSeg ?>
            },
            success: function(data) {
                if (data.no_soal == 1) {
                    window.clearInterval(timers);
                    $("#dv_soal").html(data.ret);
                    $("#lb_kolom").text("Kolom " + data.kolom);
                    countdown(isNewSk ? 65 : 61, kolom_id);
                } else if (data.ret == "persiapan") {
                    window.clearInterval(timers);
                    $("#lb_kolom").text("Persiapan . . .");
                    $("#dv_soal").html(isNewSk ? "<div class='sk-prep-message'>Persiapan menuju Kolom " + (parseInt(kolom_id, 10) + 1) + "...</div>" : "");
                    countdown(isNewSk ? 5 : 6, kolom_id, data.ret);
                } else if (data.ret == "selesai") {
                    window.clearInterval(timers);
                    window.location.href = "<?= base_url() ?>/sikapkerja/hasiltryout/" + <?= $skGroupSeg ?>;
                } else if (data.ret == "soal_tidak_ada" || data == "soal_tidak_ada") {
                    alert("Soal tidak ada");
                } else {
                    $("#dv_soal").html(data.ret);
                    $("#lb_kolom").text("Kolom " + data.kolom);
                }
            },
            error: function() {
                alert("Error system");
            },
            complete: function() {
                isSubmitting = false;
            }
        });
    }

    function convertSeconds(s) {
        if (s < 0) s = 0;
        var min = Math.floor(s / 60);
        var sec = s % 60;
        if (sec < 10) sec = "0" + sec;
        if (min < 10) min = "0" + min;
        return min + ":" + sec;
    }

    function countdown(detik, kolom_id, proc) {
        var seconds = detik;
        if (isNewSk) {
            $("#countdown").text(convertSeconds(seconds));
        }
        timers = window.setInterval(function() {
            seconds--;
            $("#countdown").text(convertSeconds(seconds));
            if (seconds <= 0) {
                window.clearInterval(timers);
                if (proc == "persiapan") {
                    kolom_id = parseInt(kolom_id, 10) + 1;
                    startujian("nextkolom","","","",4,0,kolom_id,98);
                } else {
                    startujian("persiapan","","","",4,0,kolom_id,98);
                }
            }
        }, 1000);
    }
    </script>
</body>
</html>
