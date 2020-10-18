<?php

namespace App\Service;

use App\Repository\BookmarkRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class XlsxService extends AbstractController
{
    private BookmarkRepository $bookmarkRepository;

    public function __construct(BookmarkRepository $bookmarkRepository)
    {
        $this->bookmarkRepository = $bookmarkRepository;
    }

    /**
     * @throws Exception
     */
    public function generate(): BinaryFileResponse
    {
        $spreadsheet = new Spreadsheet();
        $bookmarks = $this->bookmarkRepository->findAll();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Закладки');

        $sheet->setCellValue('A1', 'id');
        $sheet->setCellValue('B1', 'url');
        $sheet->setCellValue('C1', 'title');
        $sheet->setCellValue('D1', 'metaDescription');
        $sheet->setCellValue('E1', 'metaKeywords');
        $sheet->setCellValue('F1', 'favicon(base64)');

        if (!empty($bookmarks)) {
            $i = 2;
            foreach ($bookmarks as $bookmark) {
                $sheet->setCellValue("A{$i}", $bookmark->getId());
                $sheet->setCellValue("B{$i}", $bookmark->getUrl());
                $sheet->setCellValue("C{$i}", $bookmark->getPageTitle());
                $sheet->setCellValue("D{$i}", $bookmark->getMetaDescription());
                $sheet->setCellValue("E{$i}", $bookmark->getMetaKeywords());
                $sheet->setCellValue("F{$i}", $bookmark->getFavicon());
                ++$i;
            }
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'excel_bookmarks.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
