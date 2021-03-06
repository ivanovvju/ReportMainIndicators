<?php

/**
 * Показатель "Соотношение участковой и технической скоростей грузового поезда".
 */
class UchToTehnSkor extends BaseIndicator
{
    /**
     * @var UtilsDate Объект для работы с датами.
     */
    private $utilsDate;

    public function __construct($codeIndicator, UtilsDate $utilsDate)
    {
        Log::Info("Формирование показателя: Соотношение участковой и технической скоростей грузового поезда");

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
            SELECT CAST(ROUND(CAST(uch_sk AS DECIMAL(4,1)) / CAST(tex_sk AS DECIMAL(4,1)), 3) AS DECIMAL(6,3)) AS uch_to_tehn
            FROM (
                SELECT SUM(uch_sk) AS uch_sk, SUM(tex_sk) AS tex_sk
                FROM diskor.isploc_gvc
                WHERE date = '{$this->utilsDate->endDateMonthLastYear}' AND pr_n = '1' AND pr_g = '1' AND kod_otdel = 99
                GROUP BY kod_otdel
            )
        ";
        $result = $this->connectDoclad->select($sql);
        $fact = 0;

        foreach ($result as $item) {
            $fact = str_replace(',', '.', $item['UCH_TO_TEHN']);
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
//        $sql = "
//            WITH uch_sk AS (
//                Select VALUE from PLAN.PP_LV_70 where date='{$this->utilsDate->firstDate}' AND kod_otdel = '00'
//            ),
//            tehn_sk AS (
//                Select VALUE from PLAN.PP_LV_91 where date='{$this->utilsDate->firstDate}' AND kod_otdel = '00'
//            )
//
//            SELECT CAST(ROUND(uch_sk.value / tehn_sk.value, 3) AS DECIMAL(6,3)) AS value
//            FROM uch_sk, tehn_sk;
//        ";
//        $result = $this->connectDoclad->select($sql);
//        $plan = 0;
//
//        foreach ($result as $item) {
//            $plan = str_replace(',', '.', $item['VALUE']);
//        }

        Log::Info("План: {$this->getFactLastYear()}");

        $this->setPlan($this->getFactLastYear());
    }

    /**
     * @inheritDoc
     */
    public function iniFact()
    {
        // TODO: Implement iniFact() method.
        $sql = "
            SELECT CAST(ROUND(CAST(uch_sk AS DECIMAL(4,1)) / CAST(tex_sk AS DECIMAL(4,1)), 3) AS DECIMAL(6,3)) AS uch_to_tehn
            FROM (
                SELECT SUM(uch_sk) AS uch_sk, SUM(tex_sk) AS tex_sk
                FROM diskor.isploc_gvc
                WHERE date = '{$this->utilsDate->date}' AND pr_n = '1' AND pr_g = '1' AND kod_otdel = 99
                GROUP BY kod_otdel
            )
        ";
        $result = $this->connectDoclad->select($sql);
        $fact = 0;

        foreach ($result as $item) {
            $fact = str_replace(',', '.', $item['UCH_TO_TEHN']);
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

    /**
     * @inheritDoc
     */
    public function iniToPlan()
    {
        // TODO: Change the autogenerated stub
        $fact = $this->getFact();
        $plan = $this->getPlan();
        $toPlan = round($fact - $plan, 3);
        $this->setToPlan($toPlan);
    }

    /**
     * @inheritDoc
     */
    public function iniToLastYear()
    {
        // TODO: Change the autogenerated stub
        $fact = $this->getFact();
        $factLastYear = $this->getFactLastYear();
        $toLastYear = round($fact - $factLastYear, 3);
        $this->setToLastYear($toLastYear);
    }
}