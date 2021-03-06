<?php

/**
 * Показатель "Доля грузовых отправок в груженых вагонах с соблюдением установленного срока доставки".
 */
class GrOtprInGrVag extends BaseIndicator
{
    /**
     * @var UtilsDate Объект для работы с датами.
     */
    private $utilsDate;

    public function __construct($codeIndicator, UtilsDate $utilsDate)
    {
        Log::Info("Формирование показателя: Доля грузовых отправок в груженых вагонах с соблюдением установленного срока доставки");

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
        $val1 = 0;
        $val2 = 0;
        $fact = 0;
        $fileName = "E://Diskor_new/IH/GP/CO-31/srok_dost_{$this->utilsDate->lastYear}{$this->utilsDate->monthLastYear}{$this->utilsDate->dayLastYear}.xlsx";

        if (file_exists($fileName)) {
            $xls = PHPExcel_IOFactory::load($fileName);
            $xls->setActiveSheetIndex(0);
            $sheet = $xls->getActiveSheet();
            $data = $sheet->toArray();

            for ($row = 0; $row < 15; $row++) {
                if (isset($data[$row][1])) {
                    $pos1 = stripos($data[$row][1], 'нет нарушений');
                    if ($pos1 !== false) {
                        $val1 = isset($data[$row][2]) ? str_replace(',', '.', $data[$row][2]) : 0; // "Нет нарушений"
                    }

                    $pos2 = stripos($data[$row][1], 'Итого');
                    $pos3 = stripos($data[$row][1], 'Всего');
                    if ($pos2 !== false || $pos3 !== false) {
                        $val2 = isset($data[$row][2]) ? str_replace(',', '.', $data[$row][2]) : 0; // "Всего"
                    }
                }
            }
            $fact = $val2 != 0 ? round($val1 / $val2 * 100, 2) : 0;
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
        $sql = "
            SELECT plan
            FROM plan.dolya_otpravok_v_gr_vag
            WHERE date = '{$this->utilsDate->firstDate}';
        ";
        $result = $this->connectDoclad->select($sql);
        $plan = 0;

        foreach ($result as $item) {
            $plan = round(str_replace(',', '.', $item['PLAN']), 2);
        }

        Log::Info("План: {$plan}");
        $this->setPlan($plan);
    }

    /**
     * @inheritDoc
     */
    public function iniFact()
    {
        // TODO: Implement iniFact() method.
        $val1 = 0;
        $val2 = 0;
        $fact = 0;
        $fileName = "E://Diskor_new/IH/GP/CO-31/srok_dost_{$this->utilsDate->year}{$this->utilsDate->month}{$this->utilsDate->day}.xlsx";

        if (file_exists($fileName)) {
            $xls = PHPExcel_IOFactory::load($fileName);
            $xls->setActiveSheetIndex(0);
            $sheet = $xls->getActiveSheet();
            $data = $sheet->toArray();

            for ($row = 0; $row < 15; $row++) {
                if (isset($data[$row][1])) {
                    $pos1 = stripos($data[$row][1], 'нет нарушений');
                    if ($pos1 !== false) {
                        $val1 = isset($data[$row][2]) ? str_replace(',', '.', $data[$row][2]) : 0; // "Нет нарушений"
                    }

                    $pos2 = stripos($data[$row][1], 'Итого');
                    $pos3 = stripos($data[$row][1], 'Всего');
                    if ($pos2 !== false || $pos3 !== false) {
                        $val2 = isset($data[$row][2]) ? str_replace(',', '.', $data[$row][2]) : 0; // "Всего"
                    }
                }
            }
            $fact = $val2 != 0 ? round($val1 / $val2 * 100, 2) : 0;
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
//        $this->setBenchmark($this->getPlan());
        $this->setBenchmark("пр.год/ЦД, дорога план");
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
    public function iniToLastYearPercent()
    {
        // TODO: Change the autogenerated stub
        $this->setToLastYearPercent("-");
    }
}