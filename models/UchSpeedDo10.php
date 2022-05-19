<?php

/**
 * Показатель "Средняя участковая скорость движения грузового поезда по данным ДО-10ВЦ".
 */
class UchSpeedDo10 extends BaseIndicator
{
    /**
     * @var UtilsDate Объект для работы с датами.
     */
    private $utilsDate;

    public function __construct($codeIndicator, UtilsDate $utilsDate)
    {
        Log::Info("Формирование показателя: Средняя участковая скорость движения грузового поезда по данным ДО-10ВЦ");

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
            SELECT id, nodcode, SUM(value) AS value
            FROM asoup2.diskor_pok
            WHERE report_date = '{$this->utilsDate->endDateMonthLastYear}' AND pr_n = 1 AND id = 46 AND nodcode = 0
            GROUP BY id, nodcode
        ";

        $result = $this->connectNodudb->select($sql);
        $fact = 0;

        foreach ($result as $item) {
            $fact = round(str_replace(',', '.', $item['VALUE']), 2);
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
            SELECT VALUE
            FROM plan.pp_lv_70
            WHERE date = '{$this->utilsDate->firstDate}' AND kod_otdel = '00'
        ";

        $result = $this->connectDoclad->select($sql);
        $plan = 0;

        foreach ($result as $item) {
            $plan = round(str_replace(',', '.', $item['VALUE']), 2);
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
            SELECT id, nodcode, SUM(value) AS value
            FROM asoup2.diskor_pok
            WHERE report_date = '{$this->utilsDate->date}' AND pr_n = 1 AND id = 46 AND nodcode = 0
            GROUP BY id, nodcode
        ";

        $result = $this->connectNodudb->select($sql);
        $fact = 0;

        foreach ($result as $item) {
            $fact = round(str_replace(',', '.', $item['VALUE']), 2);
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