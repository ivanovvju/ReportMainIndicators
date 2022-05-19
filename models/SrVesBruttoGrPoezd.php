<?php

/**
 * Показатель "Средний вес брутто грузового поезда".
 */
class SrVesBruttoGrPoezd extends BaseIndicator
{
    /**
     * @var UtilsDate Объект для работы с датами.
     */
    private $utilsDate;

    public function __construct($codeIndicator, UtilsDate $utilsDate)
    {
        Log::Info("Формирование показателя: Средний вес брутто грузового поезда");

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
        if (file_exists("tmp/FMRGD09_{$this->utilsDate->year}.xml")) {
            $xml = file_get_contents("tmp/FMRGD09_{$this->utilsDate->year}.xml");
            libxml_use_internal_errors(true);
            $sx = new SimpleXMLElement($xml);

            $data = (array)$sx->xpath("//ss:Row[28]/ss:Cell[2]/ss:Data");
            $fact = isset($data[0][0]) ? round($data[0][0]) : 0;
        } else {
            Log::Error("Не скачался файл из СИС-Эффект: tmp/FMRGD09_{$this->utilsDate->year}.xml");
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
        $plan = 0;
        if (file_exists("tmp/FMRGD09_{$this->utilsDate->year}.xml")) {
            $xml = file_get_contents("tmp/FMRGD09_{$this->utilsDate->year}.xml");
            libxml_use_internal_errors(true);
            $sx = new SimpleXMLElement($xml);

            $data = (array)$sx->xpath("//ss:Row[28]/ss:Cell[3]/ss:Data");
            $plan = isset($data[0][0]) ? round($data[0][0]) : 0;
        } else {
            Log::Error("Не скачался файл из СИС-Эффект: tmp/FMRGD09_{$this->utilsDate->year}.xml");
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
        $fact = 0;
        if (file_exists("tmp/FMRGD09_{$this->utilsDate->year}.xml")) {
            $xml = file_get_contents("tmp/FMRGD09_{$this->utilsDate->year}.xml");
            libxml_use_internal_errors(true);
            $sx = new SimpleXMLElement($xml);

            $data = (array)$sx->xpath("//ss:Row[28]/ss:Cell[7]/ss:Data");
            $fact = isset($data[0][0]) ? round($data[0][0]) : 0;
        } else {
            Log::Error("Не скачался файл из СИС-Эффект: tmp/FMRGD09_{$this->utilsDate->year}.xml");
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
        $this->setBenchmark("план");
    }
}