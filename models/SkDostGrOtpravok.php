<?php

require_once "D://wwwnew/libPHP/PHPExcel.php";

/**
 * Показатель "Средняя скорость доставки грузовых отправок в груженых вагонах (к сети по разделу 2 ЦО-31)".
 */
class SkDostGrOtpravok extends BaseIndicator
{
    /**
     * @var UtilsDate Объект для работы с датами.
     */
    private $utilsDate;

    public function __construct($codeIndicator, UtilsDate $utilsDate)
    {
        Log::Info("Формирование показателя: Средняя скорость доставки грузовых отправок в груженых вагонах (к сети по разделу 2 ЦО-31)");

        parent::__construct($codeIndicator);

        $this->utilsDate = $utilsDate;

        $this->iniFactLastYear();
        $this->iniPlan();
        $this->iniFact();
        $this->iniToPlanPercent();
        $this->iniToPlan();
        $this->iniToLastYearPercent();
        $this->iniToLastYear();
        $this->iniBenchmark();
    }

    /**
     * @inheritDoc
     */
    public function iniFactLastYear()
    {
        // TODO: Implement iniFactLastYear() method.
        $fact = 0;
        $fileName = "E://Diskor_new/IH/GP/CO-31/srok_dost_{$this->utilsDate->lastYear}{$this->utilsDate->monthLastYear}{$this->utilsDate->dayLastYear}.xlsx";

        if (file_exists($fileName)) {
            $xls = PHPExcel_IOFactory::load($fileName);
            $xls->setActiveSheetIndex(0);
            $sheet = $xls->getActiveSheet();
            $data = $sheet->toArray();
            for ($row = 0; $row < 15; $row++) {
                if (isset($data[$row][1])) {
                    $pos2 = stripos($data[$row][1], 'Итого');
                    $pos3 = stripos($data[$row][1], 'Всего');
                    if ($pos2 !== false || $pos3 !== false) {
                        $fact = isset($data[$row][6]) ? str_replace(',', '.', $data[$row][6]) : 0;
                    }
                }
            }
        } else {
            Log::Error("Нет файла CO-31 (из ИХГП): {$fileName}");
        }

        if ($fact == 0) {
            Log::Warn("Получили 0 по факту пр года, возможно есть ошибки. Проверьте файл: {$fileName}.");
        }

        Log::Info("Факт пр года: {$fact}");

        $this->setFactLastYear($fact);
    }

    /**
     * @inheritDoc
     */
    public function iniPlan()
    {
        // TODO: Implement iniPlan() method.
        $this->setPlan("-");
    }

    /**
     * @inheritDoc
     */
    public function iniFact()
    {
        // TODO: Implement iniFact() method.
        $fact = 0;
        $fileName = "E://Diskor_new/IH/GP/CO-31/srok_dost_{$this->utilsDate->year}{$this->utilsDate->month}{$this->utilsDate->day}.xlsx";

        if (file_exists($fileName)) {
            $xls = PHPExcel_IOFactory::load($fileName);
            $xls->setActiveSheetIndex(0);
            $sheet = $xls->getActiveSheet();
            $data = $sheet->toArray();
            for ($row = 0; $row < 15; $row++) {
                if (isset($data[$row][1])) {
                    $pos2 = stripos($data[$row][1], 'Итого');
                    $pos3 = stripos($data[$row][1], 'Всего');
                    if ($pos2 !== false || $pos3 !== false) {
                        $fact = isset($data[$row][6]) ? str_replace(',', '.', $data[$row][6]) : 0;
                    }
                }
            }
        } else {
            Log::Error("Нет файла CO-31 (из ИХГП): {$fileName}");
        }

        if ($fact == 0) {
            Log::Warn("Получили 0 по факту, возможно есть ошибки. Проверьте файл: {$fileName}.");
        }

        Log::Info("Факт: {$fact}");

        $this->setFact($fact);
    }

    /**
     * @inheritDoc
     */
    public function iniBenchmark()
    {
        // TODO: Implement iniBenchmark() method.
//        $this->setBenchmark($this->getFactLastYear());
        $this->setBenchmark("пр.год");
    }

    /**
     * @inheritDoc
     */
    public function iniToPlanPercent()
    {
        // TODO: Change the autogenerated stub
        $this->setToPlanPercent("-");
    }

    /**
     * @inheritDoc
     */
    public function iniToPlan()
    {
        // TODO: Change the autogenerated stub
        $this->setToPlan("-");
    }
}