<?php

/**
 * Показатель "Соотношение эксплуатационного и тарифного грузооборота".
 */
class ExplToTarifGruzooborot extends BaseIndicator
{
    /**
     * @var UtilsDate Объект для работы с датами.
     */
    private $utilsDate;

    public function __construct($codeIndicator, UtilsDate $utilsDate)
    {
        Log::Info("Формирование показателя: Соотношение эксплуатационного и тарифного грузооборота");

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
        // Эксплуатационный грузооборот.
        $sql = "
            SELECT tkm_netto
            FROM diskor.isploc_gvc
            WHERE date = '{$this->utilsDate->endDateMonthLastYear}' AND kod_otdel = 99 AND pr_n = 1 AND PR_G = 1
        ";

        $result = $this->connectDoclad->select($sql);
        $factExpGr = 0;

        foreach ($result as $item) {
            $factExpGr = $item['TKM_NETTO'];
        }

        // Тарифный грузооборот.
        $sql = "
            SELECT id, nodcode, SUM(value) AS value
            FROM asoup2.diskor_pok
            WHERE report_date = '{$this->utilsDate->endDateMonthLastYear}' AND pr_n = 1 AND id = 42
            GROUP BY id, nodcode
        ";

        $result = $this->connectNodudb->select($sql);
        $factTarifGr = 0;

        foreach ($result as $item) {
            $factTarifGr = $item['VALUE'];
        }

        $fact = $factTarifGr == 0 ? 0 : round($factExpGr / $factTarifGr * 100 - 100, 2);

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

            $data = (array)$sx->xpath("//ss:Row[40]/ss:Cell[3]/ss:Data");
            $planExpl = isset($data[0][0]) ? round($data[0][0]) : 0;

            $data = (array)$sx->xpath("//ss:Row[38]/ss:Cell[3]/ss:Data");
            $planTarif = isset($data[0][0]) ? round($data[0][0]) : 0;

            $plan = round($planExpl / $planTarif * 100 - 100, 2);
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
        // Эксплуатационный грузооборот.
        $sql = "
            SELECT tkm_netto
            FROM diskor.isploc_gvc
            WHERE date = '{$this->utilsDate->date}' AND kod_otdel = 99 AND pr_n = 1 AND PR_G = 1
        ";

        $result = $this->connectDoclad->select($sql);
        $factExpGr = 0;

        foreach ($result as $item) {
            $factExpGr = $item['TKM_NETTO'];
        }

        // Тарифный грузооборот.
        $sql = "
            SELECT id, nodcode, SUM(value) AS value
            FROM asoup2.diskor_pok
            WHERE report_date = '{$this->utilsDate->date}' AND pr_n = 1 AND id = 42
            GROUP BY id, nodcode
        ";

        $result = $this->connectNodudb->select($sql);
        $factTarifGr = 0;

        foreach ($result as $item) {
            $factTarifGr = $item['VALUE'];
        }

        $fact = $factTarifGr == 0 ? 0 : round($factExpGr / $factTarifGr * 100 - 100, 2);

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