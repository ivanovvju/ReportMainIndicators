<?php

/**
 * Показатель "Погрузка грузов".
 */
class CargoLoading extends BaseIndicator
{
    /**
     * @var UtilsDate Объект для работы с датами.
     */
    private $utilsDate;

    public function __construct($codeIndicator, UtilsDate $utilsDate)
    {
        Log::Info("Формирование показателя: Погрузка грузов");
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
            SELECT SUM(val)/{$this->utilsDate->daysMonthLastYear} AS val
            FROM (
                SELECT nod, alltons AS val, datemodify
                FROM disk_105
                WHERE datemodify >= '{$this->utilsDate->firstDateLastYear}' AND datemodify <= '{$this->utilsDate->endDateMonthLastYear}'
                GROUP BY nod, alltons, datemodify
            )
        ";

        $result = $this->connectDoclad->select($sql);
        $fact = 0;

        foreach ($result as $item) {
            $fact = round($item['VAL'] / 1000, 1);
        }

        Log::Info("Факт прошлого года: {$fact}");
        $this->setFactLastYear($fact);
    }

    /**
     * @inheritDoc
     */
    public function iniPlan()
    {
        // TODO: Implement iniPlan() method.
        $sql = "
            SELECT tonn AS value
            FROM plan.p_pogr_main_r
            WHERE date = '{$this->utilsDate->firstDate}' AND rod_gr = '99'
        ";

        $result = $this->connectDoclad->select($sql);
        $plan = 0;

        foreach ($result as $item) {
            $plan = round($item['VALUE'] / 1000, 1);
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
            SELECT SUM(val)/{$this->utilsDate->countDay} AS val
            FROM (
                SELECT nod, alltons AS val, datemodify
                FROM disk_105
                WHERE datemodify >= '{$this->utilsDate->firstDate}' AND datemodify <= '{$this->utilsDate->date}'
                GROUP BY nod, alltons, datemodify
            )
        ";

        $result = $this->connectDoclad->select($sql);
        $fact = 0;

        foreach ($result as $item) {
            $fact = round($item['VAL'] / 1000, 1);
        }

        Log::Info("Факт: {$fact}");
        $this->setFact($fact);
    }

    /**
     * @inheritDoc
     */
    public function iniBenchmark()
    {
//        $this->setBenchmark($this->getPlan());
        $this->setBenchmark("план");
    }
}