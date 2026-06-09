<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Seeder extends CI_Controller {

    public function jawaban_koleksi()
    {
        // ===============================
        // TEMPLATE JAWABAN (56 PERTANYAAN)
        // ===============================
        $jawabanTemplate = [
            1=>'222222',2=>'Gajah',3=>'Kebun Binatang',4=>'UMUM',5=>'PROVINSI',
            6=>'1',7=>'1',8=>'Q',9=>'1',10=>'1',11=>'Q',12=>'Q',13=>'Q',14=>'Q',
            15=>'1',16=>'1',17=>'1',18=>'1',19=>'1',20=>'1',21=>'1',22=>'1',
            23=>'1',24=>'1',25=>'1',26=>'1',27=>'1',28=>'1',29=>'1',30=>'1',
            31=>'1',32=>'1',33=>'1',34=>'1',35=>'1',36=>'1',37=>'1',38=>'1',
            39=>'Q',40=>'1',41=>'1',42=>'1',43=>'1',44=>'Q',45=>'1',46=>'1',
            47=>'1',48=>'1',49=>'1',50=>'1',51=>'1',52=>'1',53=>'1',54=>'1',
            55=>'1',56=>'1'
        ];

        $totalData = 10000;

        $this->db->trans_start();

        for ($i = 1; $i <= $totalData; $i++) {

            // ===============================
            // INSERT identitas_input_koleksi
            // ===============================
            $this->db->insert('identitas_input_koleksi', [
                'nomor_pokok'   => 'TEST-' . time() . '-' . $i,
                'tahun_data'    => date('Y'),
                'ip_address'    => $this->input->ip_address(),
                'session_id'    => 'seeder-test'
            ]);

            $id_koleksi = $this->db->insert_id();

            // ===============================
            // INSERT 56 jawaban_koleksi
            // ===============================
            $batch = [];

            foreach ($jawabanTemplate as $id_pertanyaan => $jawaban) {
                $batch[] = [
                    'id_koleksi'    => $id_koleksi,
                    'id_pertanyaan' => $id_pertanyaan,
                    'jawaban'       => $jawaban
                ];
            }

            $this->db->insert_batch('jawaban_koleksi', $batch);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo "❌ Gagal insert data testing";
        } else {
            echo "✅ Berhasil insert 10.000 identitas × 56 jawaban";
        }
    }
}
