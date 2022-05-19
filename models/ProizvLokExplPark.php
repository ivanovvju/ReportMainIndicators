<?php

/**
 * Показатель "Среднесуточная производительность локомотива эксплуатируемого парка грузового движения".
 */
class ProizvLokExplPark extends BaseIndicator
{
    /**
     * @var UtilsDate Объект для работы с датами.
     */
    private $utilsDate;

    public function __construct($codeIndicator, UtilsDate $utilsDate)
    {
        Log::Info("Формирование показателя: Среднесуточная производительность локомотива эксплуатируемого парка грузового движения");

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
            SELECT capacity_m
            FROM muratov.park_capacity
            WHERE report_date = '{$this->utilsDate->endDateMonthLastYear}' AND type_loko = 0
        ";
        $result = $this->connectDoclad->select($sql);
        $fact = 0;

        foreach ($result as $item) {
            $fact = round($item['CAPACITY_M']);
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
            FROM plan.pp_lv_101
            WHERE date = '{$this->utilsDate->firstDate}' AND kod_otdel = '00'
        ";
        $result = $this->connectDoclad->select($sql);
        $plan = 0;

        foreach ($result as $item) {
            $plan = round($item['VALUE']);
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
            SELECT capacity_m
            FROM muratov.park_capacity
            WHERE report_date = '{$this->utilsDate->date}' AND type_loko = 0
        ";
        $result = $this->connectDoclad->select($sql);
        $fact = 0;

        foreach ($result as $item) {
            $fact = round($item['CAPACITY_M']);
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