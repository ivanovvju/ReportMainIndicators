<?php

class App
{
    /**
     * @var array Массив с конфигурациями.
     */
    private $config;

    /**
     * @var UtilsDate Объект библиотеки для работы с датами.
     */
    private $dateUtils;

    public function __construct()
    {
        $this->config = include('config.php');
        $dateObj = new DateTime();
        $dateObjPr = $dateObj->modify('-1 day');

        // Раскомментируй для выработки отчета за конкретную дату.
//        $dateObj = new DateTime('2022-05-12');
//        $dateObjPr = $dateObj->modify('0 day');

        $date = $dateObjPr->format('Y-m-d');
        $this->dateUtils = new UtilsDate($date);

        Log::Info("Отчетная дата: {$this->dateUtils->date}");

        Log::Info("Очистим директорию tmp");
        $this->cleanDirTmp();
    }

    /**
     * Очищаем директорию с файлами из СИС-Эффект.
     * Очищаем эту директорию перед запуском программы для того, чтобы можно было успеть в каком виде скачивались файлы вчера.
     * @return void
     */
    private function cleanDirTmp()
    {
        foreach (glob('tmp/') as $file) {
            unlink($file);
        }
    }

    public function run()
    {
        $this->saveFileOsnPok($this->dateUtils);

        $listIndicators = array(
            1  => new CargoLoading(1, $this->dateUtils),
            2  => new TimePassengerTrainByStations(2, $this->dateUtils),
            3  => new TimeCitiesTrainByStation(3, $this->dateUtils),
            4  => new TimeCargoTrain(4, $this->dateUtils),
            5  => new GrOtprInGrVag(5, $this->dateUtils),
            6  => new SkDostGrOtpravok(6, $this->dateUtils),
            7  => new GruzooborotProbegVag(7, $this->dateUtils),
            8  => new ExplToTarifGruzooborot(8, $this->dateUtils),
            9  => new SrVesBruttoGrPoezd(9, $this->dateUtils),
            10 => new UchSpeedDo10(10, $this->dateUtils),
            11 => new UchToTehnSkor(11, $this->dateUtils),
            12 => new ProizvLokRabPark(12, $this->dateUtils),
            13 => new ProizvLokExplPark(13, $this->dateUtils),
            14 => new ColShodovPoezda(14, $this->dateUtils),
            15 => new ColStolknPoezda(15, $this->dateUtils),
        );

        Log::Info("Сформировали данные со всеми показателями. Переходим к работе с шаблоном.");

        $this->loadExcel($listIndicators);
    }

    /**
     * Формирование excel-справки и ее сохранение на ресурс.
     * @param $listIndicators array Массив с объектами показателей.
     * @return void
     */
    private function loadExcel($listIndicators)
    {
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objReader->setReadDataOnly(false);
        try {
            $objPHPExcel = $objReader->load('template.xlsx');
            Log::Info("Открыли шаблон, переходим к заполнению.");
        } catch (PHPExcel_Reader_Exception $e) {
            Log::Error("Произошла ошибка во время открытия шаблона: {$e->getMessage()}");
            return;
        }
        $activeSheet = $objPHPExcel->getActiveSheet();
        $startRow = 9;

        $sVCenterHLeft = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => 000000),
                ),
            ),
        );
        $sVCenterHCenter = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => 000000),
                ),
            ),
        );
        $sVCenterHRight = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => 000000),
                ),
            ),
        );
        $sVCenterHRightColorBlue = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => 000000),
                ),
            ),
            'font' => array(
                'color' => array('rgb' => '0070C0'),
            ),
        );
        $sVCenterHRightColorRed = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => 000000),
                ),
            ),
            'font' => array(
                'color' => array('rgb' => 'FF0000'),
            ),
        );
        $sVCenterHRightColorGreen = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => 000000),
                ),
            ),
            'font' => array(
                'color' => array('rgb' => '00B050'),
            ),
        );
        $sBgGrayVLeft = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'F2F2F2'),
            ),
            'font' => array(
                'bold' => true,
                'size' => 14,
            ),
        );
        $sBgGrayBorderRightVRight = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => 000000),
                ),
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'F2F2F2'),
            ),
            'font' => array(
                'bold' => true,
                'size' => 14,
            ),
        );
        $sBgBlueBorderTopBottomVLeft = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => 000000),
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => 000000),
                ),
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'DCE6F1'),
            ),
            'font' => array(
                'bold' => true,
                'size' => 15,
            ),
        );
        $sBgBlueBorderTopRightBottomVRight = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => 000000),
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => 000000),
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => 000000),
                ),
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'DCE6F1'),
            ),
            'font' => array(
                'bold' => true,
                'size' => 15,
            ),
        );

        $activeSheet->getCell("A4")->setValue("за {$this->dateUtils->namesMonthRus[(int)$this->dateUtils->month]} {$this->dateUtils->year}г.");
        $activeSheet->getCell("E7")->setValue("{$this->dateUtils->lastYear}г.");
        $activeSheet->getCell("F7")->setValue("План {$this->dateUtils->year}г.");
        $activeSheet->getCell("G7")->setValue("{$this->dateUtils->year}г.");
        $activeSheet->getCell("L6")->setValue("{$this->dateUtils->day}.{$this->dateUtils->month}.{$this->dateUtils->year}г.");

        $countSuccessAllIndicators = 0;
        $countAllIndicators = 0;
        $countSuccessWithout6Indicators = 0;
        foreach ($this->config['indicators'] as $groupIndicators) {
            $countSuccessGroupIndicators = 0;
            $countGroupIndicators = 0;
            foreach ($groupIndicators['list_indicators'] as $codeIndicator => $nameIndicator) {
                $inscription = $this->config['inscriptions'][$codeIndicator];
                $indicator = $listIndicators[$codeIndicator];
                $factLastYear = number_format(
                    $indicator->getFactLastYear(),
                    $this->getRound($codeIndicator, 4),
                    '.',
                    ''
                );
                $plan = $indicator->getPlan() == "-" ? $indicator->getPlan() : number_format(
                    $indicator->getPlan(),
                    $this->getRound($codeIndicator, 5),
                    '.',
                    ''
                );
                $fact = number_format(
                    $indicator->getFact(),
                    $this->getRound($codeIndicator, 6),
                    '.',
                    ''
                );
                $toPlanPercent = $indicator->getToPlanPercent() == "-" ? $indicator->getToPlanPercent() : number_format(
                    $indicator->getToPlanPercent(),
                    $this->getRound($codeIndicator, 7),
                    '.',
                    ''
                );
                $toPlan = $indicator->getToPlan() == "-" ? $indicator->getToPlan() : number_format(
                    $indicator->getToPlan(),
                    $this->getRound($codeIndicator, 8),
                    '.',
                    ''
                );
                $toLastYearPercent = $indicator->getToLastYearPercent() == "-" ? $indicator->getToLastYearPercent() : number_format(
                    $indicator->getToLastYearPercent(),
                    $this->getRound($codeIndicator, 9),
                    '.',
                    ''
                );
                $toLastYear = number_format(
                    $indicator->getToLastYear(),
                    $this->getRound($codeIndicator, 10),
                    '.',
                    ''
                );
                $benchmark = $indicator->getBenchmark();

                $checkImage = $this->checkIndicator($indicator) == 1 ? 'check.png' : 'no-check.png';
                $countSuccessGroupIndicators += $this->checkIndicator($indicator) == 1 ? 1 : 0;
                $countSuccessAllIndicators += $this->checkIndicator($indicator) == 1 ? 1 : 0;
                $countGroupIndicators++;
                $countAllIndicators++;

                // Заказчик хочет видеть кол-во выполненных показателей без шестого.
                if ($codeIndicator != 6) {
                    $countSuccessWithout6Indicators += $this->checkIndicator($indicator) == 1 ? 1 : 0;
                }

                $activeSheet->getCell("A{$startRow}")->setValue($codeIndicator);
                $activeSheet->getCell("C{$startRow}")->setValue($nameIndicator);
                $activeSheet->getCell("D{$startRow}")->setValue($inscription);
                $activeSheet->getCell("E{$startRow}")->setValueExplicit($factLastYear, PHPExcel_Cell_DataType::TYPE_STRING);
                $activeSheet->getCell("F{$startRow}")->setValueExplicit($plan, PHPExcel_Cell_DataType::TYPE_STRING);
                $activeSheet->getCell("G{$startRow}")->setValueExplicit($fact, PHPExcel_Cell_DataType::TYPE_STRING);
                $activeSheet->getCell("H{$startRow}")->setValueExplicit($toPlanPercent, PHPExcel_Cell_DataType::TYPE_STRING);
                $activeSheet->getCell("I{$startRow}")->setValueExplicit($toPlan, PHPExcel_Cell_DataType::TYPE_STRING);
                $activeSheet->getCell("J{$startRow}")->setValueExplicit($toLastYearPercent, PHPExcel_Cell_DataType::TYPE_STRING);
                $activeSheet->getCell("K{$startRow}")->setValueExplicit($toLastYear, PHPExcel_Cell_DataType::TYPE_STRING);
                $activeSheet->getCell("L{$startRow}")->setValueExplicit($benchmark, PHPExcel_Cell_DataType::TYPE_STRING);

                $objDrawing = new PHPExcel_Worksheet_Drawing();
                $objDrawing->setResizeProportional(false);
                $objDrawing->setPath($checkImage);
                $objDrawing->setCoordinates("B{$startRow}");
                $objDrawing->setOffsetX(12);
                $objDrawing->setOffsetY(20);
                $objDrawing->setWorksheet($activeSheet);

                $activeSheet->getStyle("C{$startRow}")->getAlignment()->setWrapText(true);
                $activeSheet->getStyle("D{$startRow}")->getAlignment()->setWrapText(true);
                $activeSheet->getStyle("L{$startRow}")->getAlignment()->setWrapText(true);

                $activeSheet->getRowDimension($startRow)->setRowHeight(40);

                $activeSheet->getStyle("A{$startRow}:B{$startRow}")->applyFromArray($sVCenterHCenter);
                $activeSheet->getStyle("C{$startRow}")->applyFromArray($sVCenterHLeft);
                $activeSheet->getStyle("D{$startRow}")->applyFromArray($sVCenterHCenter);
                $activeSheet->getStyle("E{$startRow}:F{$startRow}")->applyFromArray($sVCenterHRight);
                $activeSheet->getStyle("G{$startRow}")->applyFromArray($sVCenterHRightColorBlue);
                $activeSheet->getStyle("H{$startRow}")->applyFromArray(
                    $indicator->getToPlanPercent() == '-' ? $sVCenterHRight :
                        ($indicator->getToPlanPercent() >= 100 ? $sVCenterHRightColorGreen : $sVCenterHRightColorRed)
                );
                $activeSheet->getStyle("J{$startRow}")->applyFromArray(
                    $indicator->getToLastYearPercent() == '-' ? $sVCenterHRight :
                        ($indicator->getToLastYearPercent() >= 100 ? $sVCenterHRightColorGreen : $sVCenterHRightColorRed)
                );
                $activeSheet->getStyle("L{$startRow}")->applyFromArray($sVCenterHCenter);



                if ($codeIndicator == 8) {
                    $activeSheet->getStyle("I{$startRow}")->applyFromArray(
                        $indicator->getToPlan() == '-' ? $sVCenterHRight :
                            ($indicator->getToPlan() <= 0 ? $sVCenterHRightColorGreen : $sVCenterHRightColorRed)
                    );
                    $activeSheet->getStyle("K{$startRow}")->applyFromArray(
                        $indicator->getToLastYear() == '-' ? $sVCenterHRight :
                            ($indicator->getToLastYear() <= 0 ? $sVCenterHRightColorGreen : $sVCenterHRightColorRed)
                    );
                } else {
                    $activeSheet->getStyle("I{$startRow}")->applyFromArray(
                        $indicator->getToPlan() == '-' ? $sVCenterHRight :
                            ($indicator->getToPlan() >= 0 ? $sVCenterHRightColorGreen : $sVCenterHRightColorRed)
                    );
                    $activeSheet->getStyle("K{$startRow}")->applyFromArray(
                        $indicator->getToLastYear() == '-' ? $sVCenterHRight :
                            ($indicator->getToLastYear() >= 0 ? $sVCenterHRightColorGreen : $sVCenterHRightColorRed)
                    );
                }

                Log::Info("Заполнили показатель: $nameIndicator");

                $startRow++;
            }

            $activeSheet->getCell("A{$startRow}")->setValue("Выполнено показателей по перспективе оценки '{$groupIndicators['name_group']}'");
            $activeSheet->getRowDimension($startRow)->setRowHeight(25);
            $activeSheet->mergeCells("A{$startRow}:K{$startRow}");
            $activeSheet->getStyle("A{$startRow}:K{$startRow}")->applyFromArray($sBgGrayVLeft);

            $activeSheet->getCell("L{$startRow}")->setValue("{$countSuccessGroupIndicators} из {$countGroupIndicators}");
            $activeSheet->getStyle("L{$startRow}")->applyFromArray($sBgGrayBorderRightVRight);
            $startRow++;
        }

        $activeSheet->getCell("A{$startRow}")->setValue("Всего выполнено");
        $activeSheet->getRowDimension($startRow)->setRowHeight(25);
        $activeSheet->mergeCells("A{$startRow}:K{$startRow}");
        $activeSheet->getStyle("A{$startRow}:K{$startRow}")->applyFromArray($sBgBlueBorderTopBottomVLeft);

        $activeSheet->getCell("L{$startRow}")->setValue("{$countSuccessAllIndicators} из {$countAllIndicators}");
        $activeSheet->getStyle("L{$startRow}")->applyFromArray($sBgBlueBorderTopRightBottomVRight);

        $startRow++;

        $activeSheet->getCell("A{$startRow}")->setValue("Всего выполнено без учета показателя №6");
        $activeSheet->getRowDimension($startRow)->setRowHeight(25);
        $activeSheet->mergeCells("A{$startRow}:K{$startRow}");
        $activeSheet->getStyle("A{$startRow}:K{$startRow}")->applyFromArray($sBgBlueBorderTopBottomVLeft);

        $activeSheet->getCell("L{$startRow}")->setValue("{$countSuccessWithout6Indicators} из " . ($countAllIndicators - 1));
        $activeSheet->getStyle("L{$startRow}")->applyFromArray($sBgBlueBorderTopRightBottomVRight);

        Log::Info("Заполнили все данные. Сохранение справки...");

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        // Сохранение в gvc.
//        try {
//            $objWriter->save($this->config['path_save'][1] . $this->config['excel_output']);
//            Log::Info("Сохранили справку в {$this->config['path_save'][1]}{$this->config['excel_output']}");
//        } catch (PHPExcel_Writer_Exception $e) {
//            Log::Error("Произошла ошибка во время сохранения файла в {$this->config['path_save'][1]}{$this->config['excel_output']}: {$e->getMessage()}");
//        }

        $this->config['path_save'][2] = str_replace(
            'YYYY\\MM\\DD',
            "{$this->dateUtils->year}\\{$this->dateUtils->month}\\{$this->dateUtils->day}",
            $this->config['path_save'][2]
        );

        // Сохранение в archiv.
        try {
            $objWriter->save($this->config['path_save'][2] . $this->config['excel_output']);
            Log::Info("Сохранили справку в {$this->config['path_save'][2]}{$this->config['excel_output']}");
        } catch (PHPExcel_Writer_Exception $e) {
            Log::Error("Произошла ошибка во время сохранения файла в {$this->config['path_save'][2]}{$this->config['excel_output']}: {$e->getMessage()}");
        }

    }

    /**
     * Проверка показателя на выполнение.
     * @param BaseIndicator $indicator Объект показателя.
     * @return int 1 - Показатель выполняется, 0 - Не выполняется.
     */
    private function checkIndicator(BaseIndicator $indicator)
    {
        switch ($indicator->getCodeIndicator()) {
            case 1:
            case 2:
            case 3:
            case 7:
            case 9:
            case 10:
            case 11:
            case 12:
            case 13:
                return $indicator->getToPlan() >= 0 ? 1 : 0;

            case 4:
            case 5:
            case 6:
                return $indicator->getToLastYear() >= 0 ? 1 : 0;

            case 8:
                return $indicator->getToPlan() >= 0 ? 0 : 1;

            case 14:
            case 15:
                return $indicator->getToLastYear() >= 1 ? 0 : 1;
        }
    }

    /**
     * Количество знаков после запятой.
     * @param $idPokaz int - ID показателя (по шаблону Excel).
     * @param $idColumn int - ID колонки (по шаблону от 0).
     * @return int - Количество знаков после запятой.
     */
    private function getRound($idPokaz, $idColumn)
    {
        $round = 0;

        switch ($idPokaz) {
            case 1:
            case 6:
            case 10:
                $round = 1;
                break;

            case 2:
            case 3:
            case 4:
            case 5:
            case 8:
                $round = 2;
                break;

            case 7:
            case 9:
            case 12:
            case 13:
                $round = $idColumn == 7 || $idColumn == 9 ? 1 : 0;
                break;

            case 11:
                $round = $idColumn == 7 || $idColumn == 9 ? 1 : 3;
        }

        return $round;
    }

    /**
     * Сохранение файла из СИС-Эффект FMRGD09.xml.
     * @param UtilsDate $utilsDate
     * @return void
     */
    private function saveFileOsnPok(UtilsDate $utilsDate)
    {
        if ($curl = curl_init()) {
            curl_setopt($curl, CURLOPT_URL, "http://effect.gvc.oao.rzd/effect/table/FMRGD09.xml?DAT={$utilsDate->year}.{$utilsDate->month}.{$utilsDate->day}");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_COOKIE, "login=smenareq;password=smenareq");
            $out = curl_exec($curl);
            file_put_contents("tmp/FMRGD09_{$utilsDate->year}.xml", $out);
            curl_close($curl);

            Log::Info("Сохранили файл из СИС с осн показ-ми tmp/FMRGD09_{$utilsDate->year}.xml");
        }
    }

}