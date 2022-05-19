<?php

/**
 * Показатель "Грузооборот с учетом пробега вагонов в порожнем состоянии".
 */
class GruzooborotProbegVag extends BaseIndicator
{
    /**
     * @var UtilsDate Объект для работы с датами.
     */
    private $utilsDate;

    public function __construct($codeIndicator, UtilsDate $utilsDate)
    {
        Log::Info("Формирование показателя: Грузооборот с учетом пробега вагонов в порожнем состоянии");

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
            WHERE report_date = '{$this->utilsDate->endDateMonthLastYear}' AND pr_n = 1 AND id = 44
            GROUP BY id, nodcode
        ";

        $result = $this->connectNodudb->select($sql);
        $fact = 0;

        foreach ($result as $item) {
            $fact = round($item['VALUE']);
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
            SELECT ROUND(gr_uch_por * 1000 / {$this->utilsDate->daysMonth}, 0) AS value
            FROM plan.gruzooborot
            WHERE date = '{$this->utilsDate->firstDate}'
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
            SELECT id, nodcode, SUM(value) AS value
            FROM asoup2.diskor_pok
            WHERE report_date = '{$this->utilsDate->date}' AND pr_n = 1 AND id = 44
            GROUP BY id, nodcode
        ";

        $result = $this->connectNodudb->select($sql);
        $fact = 0;

        foreach ($result as $item) {
            $fact = round($item['VALUE']);
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