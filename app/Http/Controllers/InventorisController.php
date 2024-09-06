<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventorisRequest;
use App\Http\Requests\UpdateInventorisRequest;
use App\Models\Inventoris;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class InventorisController extends BaseController
{
    public function index()
    {
        $module = 'Data Inventoris';
        return view('admin.inventoris.index', compact('module'));
    }

    public function get()
    {
        // Mengambil semua data pengguna
        $dataFull = Inventoris::all();

        // Mengembalikan response berdasarkan data yang sudah disaring
        return $this->sendResponse($dataFull, 'Get data success');
    }

    public function store(StoreInventorisRequest $storeInventorisRequest)
    {
        $numericValue = (int) str_replace(['Rp', ',', ' '], '', $storeInventorisRequest->harga);
        $data = array();
        try {
            $data = new Inventoris();
            $data->nama_barang = $storeInventorisRequest->nama_barang;
            $data->kondisi = $storeInventorisRequest->kondisi;
            $data->jumlah = $storeInventorisRequest->jumlah;
            $data->tanggal_pembelian = $storeInventorisRequest->tanggal_pembelian;
            $data->lokasi = $storeInventorisRequest->lokasi;
            $data->harga = $numericValue;
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
            $data = Inventoris::where('uuid', $params)->first();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 400);
        }
        return $this->sendResponse($data, 'Show data success');
    }

    public function update(StoreInventorisRequest $storeInventorisRequest, $params)
    {
        $numericValue = (int) str_replace(['Rp', ',', ' '], '', $storeInventorisRequest->harga);
        try {
            $data = Inventoris::where('uuid', $params)->first();
            $data->nama_barang = $storeInventorisRequest->nama_barang;
            $data->kondisi = $storeInventorisRequest->kondisi;
            $data->jumlah = $storeInventorisRequest->jumlah;
            $data->tanggal_pembelian = $storeInventorisRequest->tanggal_pembelian;
            $data->lokasi = $storeInventorisRequest->lokasi;
            $data->harga = $numericValue;
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
            $data = Inventoris::where('uuid', $params)->first();
            $data->delete();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getMessage(), 400);
        }
        return $this->sendResponse($data, 'Delete data success');
    }

    public function export()
    {
        // Mengambil semua data pengguna
        $dataFull = Inventoris::all();

        // Buat objek Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Setup halaman dan gaya default
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_FOLIO);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman')->setSize(12);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Judul dan pengaturan lebar kolom otomatis
        $sheet->setCellValue('A1', 'DATA INVENTARIS')->mergeCells('A1:G1');
        $sheet->getStyle('A1')->applyFromArray([
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Header kolom
        $headers = ['NO', 'NAMA BARANG', 'KONDISI', 'JUMLAH', 'TANGGAL PEMBELIAN', 'HARGA', 'LOKASI PENYIMPANAN'];
        $sheet->fromArray($headers, NULL, 'A3');

        // Gaya header
        $sheet->getStyle('A3:G3')->applyFromArray([
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
        $totalHarga = 0; // Variabel untuk menyimpan total harga

        foreach ($dataFull as $index => $lap) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $lap->nama_barang);
            $sheet->setCellValue('C' . $row, $lap->kondisi);
            $sheet->setCellValue('D' . $row, $lap->jumlah);
            $sheet->setCellValue('E' . $row, $lap->tanggal_pembelian);
            $sheet->setCellValue('F' . $row, $lap->harga);
            $sheet->setCellValue('G' . $row, $lap->lokasi);

            // Menambahkan harga ke total
            $totalHarga += $lap->harga;

            $row++;
        }

        // Menambahkan baris total harga
        $sheet->setCellValue('E' . $row, 'Total Harga:');
        $sheet->setCellValue('F' . $row, $totalHarga);

        // Penerapan gaya untuk baris total
        $sheet->getStyle('E' . $row . ':F' . $row)->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
        ]);

        // Penerapan border untuk semua sel yang berisi data
        $sheet->getStyle('A3:G' . $row)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Pengaturan lebar kolom otomatis setelah semua data terisi
        foreach (range('A', $sheet->getHighestDataColumn()) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Simpan dan kirim file Excel
        $excelFileName = 'laporan_data_inventaris.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save(public_path($excelFileName));

        return response()->download(public_path($excelFileName));
    }
}
