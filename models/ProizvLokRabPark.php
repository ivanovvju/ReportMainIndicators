<?php

/**
 * Показатель "Среднесуточная производительность локомотива рабочего парка".
 */
class ProizvLokRabPark extends BaseIndicator
{
    /**
     * @var UtilsDate Объект для работы с датами.
     */
    private $utilsDate;

    public function __construct($codeIndicator, UtilsDate $utilsDate)
    {
        Log::Info("Формирование показателя: Среднесуточная производительность локомотива рабочего парка");

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
        $sql = "
            SELECT SUM(proizv_lok) AS proizv_lok
            FROM diskor.isploc_gvc
            WHERE date = '{$this->utilsDate->endDateMonthLastYear}' AND pr_n = '1' AND pr_g = '1' AND kod_otdel = 99
            GROUP BY kod_otdel
        ";
        $result = $this->connectDoclad->select($sql);
        $fact = 0;

        foreach ($result as $item) {
            $fact = round($item['PROIZV_LOK']);
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
            SELECT value
            FROM plan.pp_lv_65
            WHERE date = '{$this->utilsDate->firstDate}' AND kod_otdel = '00'
        ";
        $result = $this->connectDoclad->select($sql);
        $plan = 0;

        foreach ($result as $item) {
            $plan = $item['VALUE'];
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
        $sql = "
            SELECT SUM(proizv_lok) AS proizv_lok
            FROM diskor.isploc_gvc
            WHERE date = '{$this->utilsDate->date}' AND pr_n = '1' AND pr_g = '1' AND kod_otdel = 99
            GROUP BY kod_otdel
        ";
        $result = $this->connectDoclad->select($sql);
        $fact = 0;

        foreach ($result as $item) {
            $fact = round($item['PROIZV_LOK']);
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