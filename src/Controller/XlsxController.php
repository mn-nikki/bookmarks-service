<?php

namespace App\Controller;

use App\Service\XlsxServiceInterface;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Annotation\Route;

class XlsxController extends AbstractController
{
    /**
     * @Route("/get/xlsx", name="download_xlsx")
     *
     * @param XlsxServiceInterface $xlsxService
     *
     * @return BinaryFileResponse
     */
    public function download(XlsxServiceInterface $xlsxService): BinaryFileResponse
    {
        return $xlsxService->generate();
    }
}
