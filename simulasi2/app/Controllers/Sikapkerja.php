<?php

namespace App\Controllers;
use App\Models\Soalmodel;
use App\Models\Latihanmodel;


class Sikapkerja extends BaseController
{

	protected $session;
	protected $soalmodel;
	protected $latihanmodel;
	public function __construct()
	{
		$this->session = \Config\Services::session();
        $this->session->start();
        $this->soalmodel = new Soalmodel();
        $this->latihanmodel = new Latihanmodel();
	}

    public function index()
    {
        $request = \Config\Services::request();
        $materi_id = $request->uri->getSegment(2);
        $data['group'] = $this->latihanmodel->getSKgroup()->getResult();
        return view('front/sikapkerja/sikapkerja',$data);
    }

    public function pilihansk() {
        $request = \Config\Services::request();
        $sk_group_id = $request->uri->getSegment(3);
        $data['sk_group_id'] = $sk_group_id;
        $data['is_new_sk'] = in_array((int)$sk_group_id, [9, 10]);
        return view('front/sikapkerja/petunjuksoal',$data);
    }

    public function ujian() {
        $request = \Config\Services::request();
        $sk_group_id = $request->uri->getSegment(3);
        $data['sk_group_id'] = $sk_group_id;
        $data['is_new_sk'] = in_array((int)$sk_group_id, [9, 10]);
        return view('front/sikapkerja/ujian',$data);
    }

	

    public function sikapkerjaujian() {
        $request = \Config\Services::request();
        $proc = $this->request->getPost("proc");
        $soal_id = $this->request->getPost("soal_id");
        $jawaban_id = $this->request->getPost("jawaban_id");
        $group_id = $this->request->getPost("group_id");
        $no_soal = $this->request->getPost("no_soal");
        $pilihan_nm = $this->request->getPost("pilihan_nm");
        $kolom_id = $this->request->getPost("kolom_id");
        $materi = $this->request->getPost("materi");
        $sk_group_id = $this->request->getPost("sk_group_id");
        $date = date("Y-m-d H:i:s");
        $is_new_sk = in_array((int)$sk_group_id, [9, 10]);
        $max_soal = $is_new_sk ? 120 : 50;

        if ($proc == "start") {
            $getUsed = $this->soalmodel->getResponByMateriId($this->session->user_id,$sk_group_id)->getResult();
            if (count($getUsed)>0) {
                $used = $getUsed[0]->used + 1;
                $this->session->set("used",$used);
              } else {
                $used = 1;
                $this->session->set("used",$used);
              }
        }
        
        if ($jawaban_id !== null && $jawaban_id !== "") {
            $data = [
                "jawaban_id" => $jawaban_id,
                "pilihan_nm" => $pilihan_nm,
                "soal_id" => $soal_id,
                "no_soal" => $no_soal,
                "group_id" => $group_id,
                "materi" => $materi,
                "used" => $this->session->used,
                "kolom_id" => $kolom_id,
                "created_user_id" => $this->session->user_id,
                "created_dttm" => $date,
                "session" => $this->session->session
            ];
            $respon_id = $this->soalmodel->simpanResponSK($data);
        }
        $no_soal = $no_soal + 1;

        if ($proc == "persiapan") {
            if ($is_new_sk && $kolom_id >= 10) {
                echo json_encode(array("ret"=>"selesai"));
            } else {
                echo json_encode(array("ret"=>"persiapan", "kolom"=>$kolom_id));
            }
        } else if ($no_soal == ($max_soal + 1) && $group_id == 4 && $kolom_id <= 10) {
            if ($is_new_sk && $kolom_id >= 10) {
                echo json_encode(array("ret"=>"selesai"));
            } else {
                echo json_encode(array("ret"=>"persiapan", "kolom"=>$kolom_id));
            }
        } else if ($group_id == 4 && $kolom_id >= 11) {
            echo json_encode(array("ret"=>"selesai"));
        } else {
            $res = $this->soalmodel->getSoalSK($no_soal,4,98,$kolom_id,$sk_group_id)->getResult();
            
            if (count($res)>0) {
                $getjawaban = $this->soalmodel->getjawaban($res[0]->soal_id)->getResult();
                if ($is_new_sk) {
                    $letters = ['A', 'B', 'C', 'D', 'E'];
                    $refItems = [];
                    $optItems = [];
                    $jwbId = !empty($getjawaban) ? $getjawaban[0]->jawaban_id : 0;

                    if ($res[0]->typesoal == "gambar") {
                        $jwbParts = !empty($getjawaban) ? explode('|', $getjawaban[0]->jawaban_nm) : [];
                        foreach ($jwbParts as $jwb_nm) {
                            if ($jwb_nm === '') continue;
                            $src = base_url("images/soalsk/kolom/$kolom_id/sk_group/$sk_group_id/$jwb_nm");
                            $refItems[] = "<img src='$src' class='sk-new-ref-img' alt=''>";
                            $optItems[] = "<img src='$src' class='sk-new-opt-img' alt=''>";
                        }
                        $qParts = explode('|', $res[0]->soal_nm);
                        $qHtml = "";
                        foreach ($qParts as $q_nm) {
                            if ($q_nm === '') continue;
                            $src = base_url("images/soalsk/kolom/$kolom_id/sk_group/$sk_group_id/$q_nm");
                            $qHtml .= "<div class='sk-new-q-item'><img src='$src' class='sk-new-q-img' alt=''></div>";
                        }
                    } else {
                        $jwbParts = !empty($getjawaban) ? preg_split('//u', $getjawaban[0]->jawaban_nm, -1, PREG_SPLIT_NO_EMPTY) : [];
                        foreach ($jwbParts as $ch) {
                            $safeCh = htmlspecialchars($ch, ENT_QUOTES, 'UTF-8');
                            $refItems[] = "<span class='sk-new-ref-char'>$safeCh</span>";
                            $optItems[] = "<span class='sk-new-opt-char'>$safeCh</span>";
                        }
                        $qParts = preg_split('//u', $res[0]->soal_nm, -1, PREG_SPLIT_NO_EMPTY);
                        $qHtml = "";
                        foreach ($qParts as $ch) {
                            $safeCh = htmlspecialchars($ch, ENT_QUOTES, 'UTF-8');
                            $qHtml .= "<div class='sk-new-q-item'><span class='sk-new-q-char'>$safeCh</span></div>";
                        }
                    }

                    $refHtml = "";
                    for ($idx = 0; $idx < 5; $idx++) {
                        $sym = $refItems[$idx] ?? '';
                        $ltr = $letters[$idx];
                        $refHtml .= "<div class='sk-new-ref-item'><div class='sk-new-ref-symbol'>$sym</div><div class='sk-new-ref-letter'>$ltr</div></div>";
                    }

                    $optHtml = "";
                    for ($idx = 0; $idx < 5; $idx++) {
                        $sym = $optItems[$idx] ?? '';
                        $ltr = $letters[$idx];
                        $optHtml .= "<button type='button' onclick='startujian(\"next\",\"$ltr\",$jwbId,".$res[0]->soal_id.",$group_id,$no_soal,$kolom_id,$materi)' class='sk-new-opt-btn'><span class='sk-new-opt-symbol'>$sym</span><span class='sk-new-opt-letter'>$ltr</span></button>";
                    }

                    $ret = "<div class='sk-new-body'>
                        <div class='sk-new-ref-box'>
                            <div class='sk-new-caption'>Berikut adalah gambar referensi lengkap untuk kolom ini:</div>
                            <div class='sk-new-ref-row'>$refHtml</div>
                        </div>
                        <div class='sk-new-q-section'>
                            <div class='sk-new-caption'>Soal berikut menampilkan urutan gambar. Pilih jawaban yang sesuai:</div>
                            <div class='sk-new-q-row'>$qHtml</div>
                        </div>
                        <div class='sk-new-opt-section'>
                            <div class='sk-new-caption'>Pilih salah satu jawaban berikut:</div>
                            <div class='sk-new-opt-row'>$optHtml</div>
                        </div>
                    </div>";
                } else {
                    $ret = "<div class='col-md-12'>
                        <table border='0' style='margin: 0 auto;'>
                            <tbody>
                                <tr style='font-size: 75px; font-weight: bold; text-align: center;'>";
                                if ($res[0]->typesoal == "gambar") {
                                    foreach ($getjawaban as $key) {
                                        $jawaban_nm = explode('|', $key->jawaban_nm);
                                        foreach ($jawaban_nm as $jwb_nm) {
                                            $src = base_url("images/soalsk/kolom/$kolom_id/sk_group/$sk_group_id/$jwb_nm");
                                            $ret .= "<td width='70'><img src='$src' style='height: 100px; width: 100px; margin: 5px;'></td>";
                                        }
                                    }
                                } else {
                                    foreach ($getjawaban as $key) {
                                        $jawaban_nm = preg_split('//u', $key->jawaban_nm, -1, PREG_SPLIT_NO_EMPTY);
                                        foreach ($jawaban_nm as $jwb_nm) {
                                            $ret .= "<td width='70'>".htmlspecialchars($jwb_nm, ENT_QUOTES, 'UTF-8')."</td>";
                                        }
                                    }
                                }
                                
                            $ret .= "</tr>
                                <tr style='font-size:35px;font-weight:normal;text-align:center;'>
                                    <td>A</td>
                                    <td>B</td>
                                    <td>C</td>
                                    <td>D</td>
                                    <td>E</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class='col-md-12' style='width:100%; margin-top: 30px;'>
                        <label style='font-size:20px;' for='Pertanyaan'>Pertanyaan ".$no_soal."</label>
                        <div class='col-md-12 row' style='display:flex; justify-content:center; flex-wrap:wrap;'>";
                        if ($res[0]->typesoal == "gambar") {
                            foreach ($res as $keySoal) {
                                $soal_nm = explode('|', $keySoal->soal_nm);
                                foreach ($soal_nm as $jwb_nm) {
                                    $src = base_url("images/soalsk/kolom/$kolom_id/sk_group/$sk_group_id/$jwb_nm");
                                    $ret .= "<img src='$src' style='height: 100px; width: 100px; margin: 5px;'>";
                                }
                            }
                        } else {
                            foreach ($res as $keySoal) {
                                $soal_nm = preg_split('//u', $keySoal->soal_nm, -1, PREG_SPLIT_NO_EMPTY);
                                foreach ($soal_nm as $jwb_nm) {
                                    $ret .= "<div class='col-md-2' style='background-color:grey;min-height:70px;font-size:65px;font-weight:bold;text-align:center;margin:10px;display: inline-block;'>
                            ".htmlspecialchars($jwb_nm, ENT_QUOTES, 'UTF-8')."</div>";
                                }
                            }
                        }
                            
                            
                    $ret .= "</div>
                        <div class='col-md-12' style='display:flex;'>";
                        foreach ($getjawaban as $k) {
                            $jawaban_id = $k->jawaban_id;
                            $ret .= "<button onclick='startujian(\"next\",\"A\",".$jawaban_id.",".$res[0]->soal_id.",$group_id,$no_soal,$kolom_id,$materi)' style='margin:5px;font-weight:bold;font-size: 20px;'
                            class='btn btn-block btn-outline-success'>A</button>
                        <button onclick='startujian(\"next\",\"B\",".$jawaban_id.",".$res[0]->soal_id.",$group_id,$no_soal,$kolom_id,$materi)' style='margin:5px;font-weight:bold;font-size: 20px;'
                            class='btn btn-block btn-outline-success'>B</button>
                        <button onclick='startujian(\"next\",\"C\",".$jawaban_id.",".$res[0]->soal_id.",$group_id,$no_soal,$kolom_id,$materi)' style='margin:5px;font-weight:bold;font-size: 20px;'
                            class='btn btn-block btn-outline-success'>C</button>
                        <button onclick='startujian(\"next\",\"D\",".$jawaban_id.",".$res[0]->soal_id.",$group_id,$no_soal,$kolom_id,$materi)' style='margin:5px;font-weight:bold;font-size: 20px;'
                            class='btn btn-block btn-outline-success'>D</button>
                        <button onclick='startujian(\"next\",\"E\",".$jawaban_id.",".$res[0]->soal_id.",$group_id,$no_soal,$kolom_id,$materi)' style='margin:5px; margin-right: 10px; font-weight:bold;font-size: 20px;'
                            class='btn btn-block btn-outline-success'>E</button>";
                        }
                            
                    $ret .= "</div>
                    </div>";
                }
                echo json_encode(array("ret"=>$ret, "kolom"=>$kolom_id,"group_id"=>$group_id,"no_soal"=>$no_soal), JSON_UNESCAPED_UNICODE);
            } else {
                $ret = "soal_tidak_ada";
                echo json_encode(array("ret"=>$ret));
            }
        }
    }

    public function hasiltryout() {
        $request = \Config\Services::request();
        $user_id = $this->session->user_id;
        $materi_id = $request->uri->getSegment(3);
        $klm = $this->soalmodel->getKolomSoal()->getResult();
 
                $data = [
                    "kolom" => $klm
                ];
        return view('front/sikapkerja/hasiltryout',$data);
    }

    public function showresult() {
        $materi = $this->request->getPost('materi_id');
        $benar_passhand = 0;
            $salah_passhand = 0;
            $benar_kec = 0;
            $salah_kec = 0;
            $benar_keb = 0;
            $salah_keb = 0;
            $benar_sk  = 0;
            $salah_sk  = 0;
            $total_skor  = 0;
            $passhandskor = $this->soalmodel->getPasshandSkor($this->session->user_id,$this->session->session,$materi)->getResult();
            foreach ($passhandskor as $key) {
                if ($key->kunci == $key->pilihan_nm) {
                    $benar_passhand = $benar_passhand + 1;
                } else {
                    $salah_passhand = $salah_passhand + 1;
                }
            }

            $kecerdasanskor = $this->soalmodel->getKecerdasanSkor($this->session->user_id,$this->session->session,$materi)->getResult();
            foreach ($kecerdasanskor as $kec) {
                if ($kec->kunci == $kec->pilihan_nm) {
                    $benar_kec = $benar_kec + 1;
                } else {
                    $salah_kec = $salah_kec + 1;
                }
            }

            $kepskor = $this->soalmodel->getKepribadianSkor($this->session->user_id,$this->session->session,$materi)->getResult();
            foreach ($kepskor as $kep) {
                if ($kep->kunci == $kep->pilihan_nm) {
                    $benar_keb = $benar_keb + 1;
                } else {
                    $salah_keb = $salah_keb + 1;
                }
            }

            $sikapkerjaskor = $this->soalmodel->getSikapKerjaSkor($this->session->user_id,$this->session->session,$materi)->getResult();
            foreach ($sikapkerjaskor as $sk) {
                if ($sk->kunci == $sk->pilihan_nm) {
                    $benar_sk = $benar_sk + 1;
                } else {
                    $salah_sk = $salah_sk + 1;
                }
            }

            $total_skor = $benar_passhand + $benar_kec + $benar_keb + $benar_sk;
            if ($total_skor >= 61) {
                $styletotalskor = "color:green;";
                $syarat = "(Memenuhi Syarat - MS)";
            } else {
                $styletotalskor = "color:red;";
                $syarat = "(Tidak Memenuhi Syarat - TMS)";
            }
            $ret = "<div class='col-lg-12'>
                    <a href='".base_url()."/home'><button class='btn btn-primary'>Menu Utama</button></a>
                        <div style='width:100%;text-align:center;'>
                            <h1 style='margin:10px;'>SELAMAT !</h1>
                            <h1 style='margin:10px;'>SKOR ANDA</h1>
                            <h1 style='margin:10px;$styletotalskor'>$total_skor $syarat</h1>
                        </div>
                        <div>
                            <ul>
                            <li>Passhand skor <label>$benar_passhand</label></li>
                            <li>Kecerdasan skor <label>$benar_kec</label></li>
                            <li>Kepribadian skor <label>$benar_keb</label></li>
                            <li>Sikap Kerja skor <label>$benar_sk</label></li>
                            </ul>
                        </div>";
            $ret .= "</div>";
        echo json_encode($ret);
    }
    
}