<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDataPendudukRequest;
use App\Http\Requests\UpdateDataPendudukRequest;
use App\Models\DataPenduduk;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DataPendudukController extends BaseController
{
    public function index()
    {
        $module = 'Data Penduduk';
        return view('admin.datapenduduk.index', compact('module'));
    }

    public function get()
    {
        // Mengambil semua data pengguna
        $dataFull = DataPenduduk::all();

        // Mengembalikan response berdasarkan data yang sudah disaring
        return $this->sendResponse($dataFull, 'Get data success');
    }

    public function store(StoreDataPendudukRequest $storeDataPendudukRequest)
    {
        $data = array();
        try {
            $data = new DataPenduduk();
            $data->nama_lengkap = $storeDataPendudukRequest->nama_lengkap;
            $data->nik = $storeDataPendudukRequest->nik;
            $data->jenis_kelamin = $storeDataPendudukRequest->jenis_kelamin;
            $data->tempat_lahir = $storeDataPendudukRequest->tempat_lahir;
            $data->tanggal_lahir = $storeDataPendudukRequest->tanggal_lahir;
            $data->agama = $storeDataPendudukRequest->agama;
            $data->pendidikan = $storeDataPendudukRequest->pendidikan;
            $data->jenis_pekerjaan = $storeDataPendudukRequest->jenis_pekerjaan;
            $data->no_kk = $storeDataPendudukRequest->no_kk;
            $data->status_kawin = $storeDataPendudukRequest->status_kawin;
            $data->status_hubungan = $storeDataPendudukRequest->status_hubungan;
            $data->golongan_darah = $storeDataPendudukRequest->golongan_darah;
            $data->nama_ayah = $storeDataPendudukRequest->nama_ayah;
            $data->nama_ibu = $storeDataPendudukRequest->nama_ibu;
            $data->rt = $storeDataPendudukRequest->rt;
            $data->rw = $storeDataPendudukRequest->rw;
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 400);
        }
        return $this->sendResponse($data, 'Added data success');
    }

    public function show($params)
    {
        $data = array();
        try {
            $data = DataPenduduk::where('uuid', $params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 400);
        }
        return $this->sendResponse($data, 'Show data success');
    }

    public function update(UpdateDataPendudukRequest $updateDataPendudukRequest, $params)
    {
        try {
            $data = DataPenduduk::where('uuid', $params)->first();
            $data->nama_lengkap = $updateDataPendudukRequest->nama_lengkap;
            $data->nik = $updateDataPendudukRequest->nik;
            $data->jenis_kelamin = $updateDataPendudukRequest->jenis_kelamin;
            $data->tempat_lahir = $updateDataPendudukRequest->tempat_lahir;
            $data->tanggal_lahir = $updateDataPendudukRequest->tanggal_lahir;
            $data->agama = $updateDataPendudukRequest->agama;
            $data->pendidikan = $updateDataPendudukRequest->pendidikan;
            $data->jenis_pekerjaan = $updateDataPendudukRequest->jenis_pekerjaan;
            $data->no_kk = $updateDataPendudukRequest->no_kk;
            $data->status_kawin = $updateDataPendudukRequest->status_kawin;
            $data->status_hubungan = $updateDataPendudukRequest->status_hubungan;
            $data->golongan_darah = $updateDataPendudukRequest->golongan_darah;
            $data->nama_ayah = $updateDataPendudukRequest->nama_ayah;
            $data->nama_ibu = $updateDataPendudukRequest->nama_ibu;
            $data->rt = $updateDataPendudukRequest->rt;
            $data->rw = $updateDataPendudukRequest->rw;
            $data->save();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 400);
        }

        return $this->sendResponse($data, 'Update data success');
    }

    public function delete($params)
    {
        $data = array();
        try {
            $data = DataPenduduk::where('uuid', $params)->first();
            $data->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 400);
        }
        return $this->sendResponse($data, 'Delete data success');
    }

    public function export()
    {
        // Mengambil semua data pengguna
        $dataFull = DataPenduduk::all();

        // Buat objek Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Setup halaman dan gaya default
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_FOLIO);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman')->setSize(12);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Judul dan pengaturan lebar kolom otomatis
        $sheet->setCellValue('A1', 'DATA PENDUDUK')->mergeCells('A1:P1');
        $sheet->getStyle('A1')->applyFromArray([
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ]
        ]);

        // Header kolom
        $headers = ['NO', 'NAMA LENGKAP', 'NIK', 'JENIS KELAMIN', 'TEMPAT LAHIR', 'TANGGAL LAHIR', 'AGAMA', 'PENDIDIKAN', 'JENIS PEKERJAAN', 'STATUS KAWIN', 'STATUS HUBUNGAN', 'GOLONGAN DARAH', 'NAMA AYAH', 'NAMA IBU', 'RT', 'RW'];
        $sheet->fromArray($headers, NULL, 'A3');

        // Gaya header
        $sheet->getStyle('A3:P3')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'ACB9CA'],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'font' => ['bold' => true],
        ]);

        // Mengisi data
        $row = 4;
        foreach ($dataFull as $index => $lap) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $lap->nama_lengkap);
            $sheet->setCellValue('C' . $row, $lap->nik);
            $sheet->setCellValue('D' . $row, $lap->jenis_kelamin);
            $sheet->setCellValue('E' . $row, $lap->tempat_lahir);
            $sheet->setCellValue('F' . $row, $lap->tanggal_lahir);
            $sheet->setCellValue('G' . $row, $lap->agama);
            $sheet->setCellValue('H' . $row, $lap->pendidikan);
            $sheet->setCellValue('I' . $row, $lap->jenis_pekerjaan);
            $sheet->setCellValue('J' . $row, $lap->status_kawin);
            $sheet->setCellValue('K' . $row, $lap->status_hubungan);
            $sheet->setCellValue('L' . $row, $lap->golongan_darah);
            $sheet->setCellValue('M' . $row, $lap->nama_ayah);
            $sheet->setCellValue('N' . $row, $lap->nama_ibu);
            $sheet->setCellValue('O' . $row, $lap->rt);
            $sheet->setCellValue('P' . $row, $lap->rw);
            $row++;
        }

        // Penerapan border untuk semua sel yang berisi data
        $sheet->getStyle('A3:P' . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Pengaturan lebar kolom otomatis
        foreach (range('A', $sheet->getHighestDataColumn()) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Simpan dan kirim file Excel
        $excelFileName = 'laporan_data_penduduk.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save(public_path($excelFileName));

        return response()->download(public_path($excelFileName));
    }
}
