<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\Usersmodel;
use App\Models\Soalmodel;
use App\Models\SoalmodelSKMateri;
use App\Models\Jawabanmodel;

class Soalsikapkerjamateri extends BaseController
{
    protected $session;
    protected $usermodel;
    protected $soalmodel;
    protected $jawabanmodel;
    protected $SoalmodelSKMateri;
    public function __construct()
	{
		$this->session = \Config\Services::session();
        $this->usermodel = new Usersmodel();
        $this->soalmodel = new Soalmodel();
        $this->jawabanmodel = new Jawabanmodel();
        $this->SoalmodelSKMateri = new SoalmodelSKMateri();
	}

    private function getTargetQuestionCount($materi_id): int
    {
        return in_array((int)$materi_id, [9, 10]) ? 120 : 50;
    }

    private function nullifyActiveColumnSoal($group_id, $materi_id, $sk_group_id, $kolom_id): void
    {
        $db = db_connect();
        $db->table('soal')
           ->set('status_cd', 'nullified')
           ->where('group_id', $group_id)
           ->where('materi', $materi_id)
           ->where('sk_group_id', $sk_group_id)
           ->where('kolom_id', $kolom_id)
           ->where('status_cd', 'normal')
           ->update();
    }

    public function index()
    {
        if ($this->session->get("user_nm") == "") {
			return redirect('/');
		} else {
            $data = [
                'materi' => $this->soalmodel->getjawAllJMateri()->getResult()
            ];
            return view('admin/soalskmateri/soal', $data);
        }
        
    }

    public function tambahsoalSkMateri() {
        $kolom_id = $this->request->getUri()->getSegment(4);
        $group_id = $this->request->getUri()->getSegment(5);
        $materi_id = $this->request->getUri()->getSegment(6);
        $sk_group_id = $this->request->getUri()->getSegment(7);

        if ($this->session->get("user_nm") == "") {
			return redirect('/');
		} else {
            
            $getSoal = $this->soalmodel->getSoalByIdSK($kolom_id, $group_id, $materi_id, $sk_group_id)->getResult();
            if (count($getSoal) > 0) {
                $soal_id_new = $getSoal[0]->soal_id;
            } else {
                $soal_id_new = NULL;
            }

            $total = count($getSoal);
            $size  = $total > 0 ? ceil($total / 4) : 0;

            $bagian1 = $size > 0 ? array_slice($getSoal, 0, $size) : [];
            $bagian2 = $size > 0 ? array_slice($getSoal, $size, $size) : [];
            $bagian3 = $size > 0 ? array_slice($getSoal, $size * 2, $size) : [];
            $bagian4 = $size > 0 ? array_slice($getSoal, $size * 3) : [];

            $data = [
                'kolom' => $this->soalmodel->getKolomById($kolom_id)->getResult(),
                'bagian1' => $bagian1,
                'bagian2' => $bagian2,
                'bagian3' => $bagian3,
                'bagian4' => $bagian4,
                'jawaban' => $this->jawabanmodel->getJawabanBySoalId($soal_id_new)->getResult(),
                'soal_id' => $soal_id_new,
                "materi_id" => $materi_id,
                "kolom_id" => $kolom_id,
                "sk_group_id" => $sk_group_id
            ];

            return view('admin/soalskmateri/tambahsoalskmateri',$data);
        }
    }

    public function showkolom() {
        $materi_id  = $this->request->getPost('materi_id');

        $res    = $this->SoalmodelSKMateri->getKolomSkMateri(4, $materi_id)->getResult();
        
        echo json_encode($res);
    }

    public function viewEditsoalSkMateri() {
        $kolom_id = $this->request->getUri()->getSegment(4);
        $group_id = $this->request->getUri()->getSegment(5);
        $materi_id = $this->request->getUri()->getSegment(6);
        $sk_group_id = $this->request->getUri()->getSegment(7);

        if ($this->session->get("user_nm") == "") {
			return redirect('/');
		} else {
            
            $getSoal = $this->soalmodel->getSoalByIdSK($kolom_id, $group_id, $materi_id, $sk_group_id)->getResult();
            if (count($getSoal) > 0) {
                $soal_id_new = $getSoal[0]->soal_id;
                $typeSoal = $getSoal[0]->typesoal;
            } else {
                $soal_id_new = NULL;
                $typeSoal = "text";
            }

            $total = count($getSoal);
            $size  = $total > 0 ? ceil($total / 4) : 0;

            $bagian1 = $size > 0 ? array_slice($getSoal, 0, $size) : [];
            $bagian2 = $size > 0 ? array_slice($getSoal, $size, $size) : [];
            $bagian3 = $size > 0 ? array_slice($getSoal, $size * 2, $size) : [];
            $bagian4 = $size > 0 ? array_slice($getSoal, $size * 3) : [];

            $data = [
                'kolom' => $this->soalmodel->getKolomById($kolom_id)->getResult(),
                'bagian1' => $bagian1,
                'bagian2' => $bagian2,
                'bagian3' => $bagian3,
                'bagian4' => $bagian4,
                'jawaban' => $this->jawabanmodel->getJawabanBySoalId($soal_id_new)->getResult(),
                'soal_id' => $soal_id_new,
                "materi_id" => $materi_id,
                "kolom_id" => $kolom_id,
                "sk_group_id" => $sk_group_id,
                "typeSoal" => $typeSoal
            ];
            
            return view('admin/soalskmateri/editsoalskmateri',$data);
        }
    }

    public function insertGambarSk()
    {
        $kolom_id    = $this->request->getPost('kolom_id');
        $materi_id   = $this->request->getPost('materi_id');
        $sk_group_id = (int)$this->request->getPost('sk_group_id');
        $soal_nm = [];  
        $allowedMime = ['image/png', 'image/jpg', 'image/jpeg'];

        $path = FCPATH . "images/soalskmateri/materi/$materi_id/kolom/$kolom_id";

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $uploadedFiles = [];
        $letters = [1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D', 5 => 'E'];

        for ($i = 1; $i <= 5; $i++) {
            $file = $this->request->getFile('gambarsk' . $i);

            if (!$file || !$file->isValid()) {
                continue;
            }

            if (!in_array($file->getMimeType(), $allowedMime)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Hanya gambar diperbolehkan (gambarsk' . $i . ')'
                ]);
            }

            $pilihan = $letters[$i];
            $newName = $i . $pilihan  . '_' . $materi_id . '_' . $kolom_id . '.' . $file->getExtension();
            $fullPath = $path . '/' . $newName;

            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            if ($file->move($path, $newName)) {
                $uploadedFiles[] = $newName;
                $soal_nm[] = $newName;
            } else {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => $file->getErrorString()
                ]);
            }
        }

        if (count($uploadedFiles) < 5) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Harap upload kelima gambar (A s/d E)'
            ]);
        }

        $soal_nm_str = implode('|', $soal_nm);
        $res = $this->randomcharGambar($soal_nm_str, $kolom_id, $materi_id, $sk_group_id);
        return $this->response->setJSON($res);
    }

    public function updateGambarSk()
    {
        $kolom_id    = $this->request->getPost('kolom_id');
        $materi_id   = $this->request->getPost('materi_id');
        $sk_group_id = (int)$this->request->getPost('sk_group_id');
        $jawaban_nm_lama = $this->request->getPost('jawaban_nm_lama');
        $soal_nm = [];  
        $allowedMime = ['image/png', 'image/jpg', 'image/jpeg'];

        $path = FCPATH . "images/soalskmateri/materi/$materi_id/kolom/$kolom_id";

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $oldParts = !empty($jawaban_nm_lama) ? explode('|', $jawaban_nm_lama) : [];
        $uploadedFiles = [];
        $letters = [1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D', 5 => 'E'];

        for ($i = 1; $i <= 5; $i++) {
            $file = $this->request->getFile('gambarsk' . $i);

            if (!$file || !$file->isValid()) {
                if (!empty($oldParts[$i - 1])) {
                    $soal_nm[] = $oldParts[$i - 1];
                }
                continue;
            }

            if (!in_array($file->getMimeType(), $allowedMime)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Hanya gambar diperbolehkan (gambarsk' . $i . ')'
                ]);
            }

            $pilihan = $letters[$i];
            $newName = $i . $pilihan  . '_' . $materi_id . '_' . $kolom_id . '.' . $file->getExtension();
            $fullPath = $path . '/' . $newName;

            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            if ($file->move($path, $newName)) {
                $uploadedFiles[] = $newName;
                $soal_nm[] = $newName;
            } else {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => $file->getErrorString()
                ]);
            }
        }

        if (empty($uploadedFiles) || count($soal_nm) < 5) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Tidak ada file yang diupload atau gambar belum lengkap'
            ]);
        }

        $soal_nm_str = implode('|', $soal_nm);
        $res = $this->randomcharGambar($soal_nm_str, $kolom_id, $materi_id, $sk_group_id);
        return $this->response->setJSON($res);
    }

    public function updateclue() {
        if ($this->session->get("user_nm") == "") {
			return view('login');
		} 

        $group_id = 4;
        $sk_group_id = (int)$this->request->getPost('sk_group_id');
        $materi_id = (int)$this->request->getPost('materi_id');
        $jawaban_nm = $this->request->getPost('jawaban_nm');
        $jawaban_nm_lama = $this->request->getPost('jawaban_nm_lama');
        $kolom_id = (int)$this->request->getPost('kolom_id');
        
        if ($jawaban_nm_lama === $jawaban_nm && $jawaban_nm !== '') {
            $existing = $this->soalmodel->getSoalByIdSK($kolom_id, $group_id, $materi_id, $sk_group_id)->getResult();
            $targetCount = $this->getTargetQuestionCount($materi_id);
            if (count($existing) === $targetCount) {
                echo json_encode("finish");
                return;
            }
        }

        $res = $this->randomchar($jawaban_nm, $kolom_id, $materi_id, $sk_group_id);
        echo json_encode($res);
    }

    public function randomcharUpdate($char,$kolom,$materi_id,$sk_group_id,$group_id,$soal_id,$jawaban_nm_lama) {
        return $this->randomchar($char, $kolom, $materi_id, $sk_group_id);
    }

    public function randomcharGambarUpdate($char, $kolom, $materi_id, $sk_group_id, $group_id, $soal_id, $jawaban_nm_lama)
    {
        return $this->randomcharGambar($char, $kolom, $materi_id, $sk_group_id);
    }

    public function randomcharGambar($char, $kolom, $materi_id, $sk_group_id)
    {
        $characters = explode('|', $char);
        $pilihanMap = ['A', 'B', 'C', 'D', 'E'];
        $pilihan = "ABCDE";
        $totalSoal = $this->getTargetQuestionCount($materi_id);

        $this->nullifyActiveColumnSoal(4, $materi_id, $sk_group_id, $kolom);

        $perOption = intdiv($totalSoal, 5);
        $missingPool = [];
        for ($idx = 0; $idx < 5; $idx++) {
            for ($c = 0; $c < $perOption; $c++) {
                $missingPool[] = $idx;
            }
        }
        while (count($missingPool) < $totalSoal) {
            $missingPool[] = random_int(0, 4);
        }
        shuffle($missingPool);

        for ($no = 1; $no <= $totalSoal; $no++) {
            $missingIdx = $missingPool[$no - 1];
            $kunci = $pilihanMap[$missingIdx];

            $remaining = [];
            foreach ($characters as $idx => $item) {
                if ($idx !== $missingIdx) {
                    $remaining[] = $item;
                }
            }
            shuffle($remaining);
            $soal_nm = implode('|', $remaining);

            $data = [
                'soal_nm' => $soal_nm,
                'group_id' => 4,
                'no_soal' => $no,
                'kunci' => $kunci,
                'materi' => $materi_id,
                'status_cd' => 'normal',
                'kolom_id' => $kolom,
                'clue' => $char,
                'sk_group_id' => $sk_group_id,
                'soal_img' => '',
                'pembahasan' => '',
                'pembahasan_img' => '',
                'typesoal' => "gambar"
            ];

            $soal_id = $this->soalmodel->insertsoalSKlatihan($data);

            $datax = [
                "soal_id" => $soal_id,
                "pilihan_nm" => $pilihan,
                "jawaban_nm" => $char,
                "jawaban_img" => "",
                "status_cd" => "normal"
            ];

            $this->soalmodel->insertjawabanSKlatihan($datax);
        }

        return "finish";
    }

    public function randomchar($char, $kolom, $materi_id, $sk_group_id)
    {
        $characters = preg_split('//u', $char, -1, PREG_SPLIT_NO_EMPTY);
        if (count($characters) < 5) {
            return "gagalsoal";
        }
        $characters = array_slice($characters, 0, 5);
        $clueStr = implode('', $characters);
        $pilihanMap = ['A', 'B', 'C', 'D', 'E'];
        $pilihan = "ABCDE";
        $totalSoal = $this->getTargetQuestionCount($materi_id);

        $this->nullifyActiveColumnSoal(4, $materi_id, $sk_group_id, $kolom);

        $perOption = intdiv($totalSoal, 5);
        $missingPool = [];
        for ($idx = 0; $idx < 5; $idx++) {
            for ($c = 0; $c < $perOption; $c++) {
                $missingPool[] = $idx;
            }
        }
        while (count($missingPool) < $totalSoal) {
            $missingPool[] = random_int(0, 4);
        }
        shuffle($missingPool);

        for ($no = 1; $no <= $totalSoal; $no++) {
            $missingIdx = $missingPool[$no - 1];
            $kunci = $pilihanMap[$missingIdx];

            $remaining = [];
            foreach ($characters as $idx => $item) {
                if ($idx !== $missingIdx) {
                    $remaining[] = $item;
                }
            }
            shuffle($remaining);
            $soal_nm = implode('', $remaining);

            $data = [
                'soal_nm' => $soal_nm,
                'group_id' => 4,
                'no_soal' => $no,
                'kunci' => $kunci,
                'materi' => $materi_id,
                'status_cd' => 'normal',
                'kolom_id' => $kolom,
                'clue' => $clueStr,
                'sk_group_id' => $sk_group_id,
                'soal_img' => '',
                'pembahasan' => '',
                'pembahasan_img' => '',
                'typesoal' => "text"
            ];

            $soal_id = $this->soalmodel->insertsoalSKlatihan($data);

            $datax = [
                "soal_id" => $soal_id,
                "pilihan_nm" => $pilihan,
                "jawaban_nm" => $clueStr,
                "jawaban_img" => "",
                "status_cd" => "normal"
            ];
    
            $this->soalmodel->insertjawabanSKlatihan($datax);
        }

        return "finish";
    }
}
