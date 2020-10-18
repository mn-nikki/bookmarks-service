<?php

namespace App\Controller;

use App\Service\XlsxService;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Annotation\Route;

class XlsxController extends AbstractController
{
    /**
     * @Route("/get/xlsx", name="download_xlsx")
     *
     * @param XlsxService $xlsxService
     * @return BinaryFileResponse
     * @throws Exception
     */
    public function download(XlsxService $xlsxService): BinaryFileResponse
    {
        return $xlsxService->generate();
    }
}
