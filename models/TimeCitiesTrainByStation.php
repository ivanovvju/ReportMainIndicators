<?php

/**
 * Показатель "Выполнение расписания движения пригородных поездов по прибытию в пункты назначения".
 */
class TimeCitiesTrainByStation extends BaseIndicator
{
    /**
     * @var UtilsDate Объект для работы с датами.
     */
    private $utilsDate;

    /**
     * @var int Факт из ДО-11 (СИС-Эффект).
     */
    private $factDo11;

    /**
     * @var int Факт из ДО-11 прошлого года (СИС-Эффект).
     */
    private $factDo11LastYear;

    /**
     * @var int Факт из Приложения 2792 (СИС-Эффект).
     */
    private $fact2792;

    /**
     * @var int Факт из Приложения 2792 прошлого года (СИС-Эффект).
     */
    private $fact2792LastYear;

    /**
     * @var int План из ДО-11 (СИС-Эффект). В качестве плана используется итоговая строка в файле.
     */
    private $planDo11;

    /**
     * @var int План из Приложения 2792 (СИС-Эффект). В качестве плана используется итоговая строка в файле.
     */
    private $plan2792;

    public function __construct($codeIndicator, UtilsDate $utilsDate)
    {
        Log::Info("Формирование показателя: Выполнение расписания движения пригородных поездов по прибытию в пункты назначения");

        parent::__construct($codeIndicator);

        $this->utilsDate = $utilsDate;

        $this->getSisEffect();

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
        $result = $this->factDo11LastYear == 0 ? 0 : round(($this->factDo11LastYear - $this->fact2792LastYear) / $this->factDo11LastYear * 100, 2);
        Log::Info("Факт прошлого года: {$result}");
        $this->setFactLastYear($result);
    }

    /**
     * @inheritDoc
     */
    public function iniPlan()
    {
        // TODO: Implement iniPlan() method.
        $result = $this->planDo11 == 0 ? 0 : round(($this->planDo11 - $this->plan2792) / $this->planDo11 * 100, 2);
        Log::Info("План: {$result}");
        $this->setPlan($result);
    }

    /**
     * @inheritDoc
     */
    public function iniFact()
    {
        // TODO: Implement iniFact() method.
        $result = $this->factDo11 == 0 ? 0 : round(($this->factDo11 - $this->fact2792) / $this->factDo11 * 100, 2);
        Log::Info("Факт: {$result}");
        $this->setFact($result);
    }

    /**
     * @inheritDoc
     */
    public function iniBenchmark()
    {
        // TODO: Implement iniBenchmark() method.
//        $this->setBenchmark($this->getPlan());
        $this->setBenchmark("план = факт по ЦД");
    }

    public function iniToPlanPercent()
    {
        // TODO: Implement iniToPlanPercent() method.
        $this->setToPlanPercent("-");
    }

    public function iniToLastYearPercent()
    {
        // TODO: Implement iniToLastYearPercent() method.
        $this->setToLastYearPercent("-");
    }

    /**
     * Выгрузка файлов из СИС-Эффект и их обработка.
     * @return void
     * @throws Exception
     */
    private function getSisEffect()
    {
        /*
         * DO11_F02.xml - СИС-Эффект / Статистическая отчетность / Хозяйство перевозок / ДО-11 / отчет за месяц
         * DO11M26.xml - СИС-Эффект / Статистическая отчетность / Хозяйство перевозок / ДО-11 / Приложения по распоряжению 2792 / за месяц в сравнении с прошлым годом / Приложение 4
         */
        if ($curl = curl_init()) {
            curl_setopt($curl, CURLOPT_URL, "http://effect.gvc.oao.rzd/effect/table/DO11_F02.xml?DAT={$this->utilsDate->year}.{$this->utilsDate->month}.{$this->utilsDate->day}");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_COOKIE, "login=smenareq;password=smenareq");
            $out = curl_exec($curl);
            file_put_contents("tmp/DO11_F02_{$this->utilsDate->year}.xml", $out);
            if ($out) {
                libxml_use_internal_errors(true);
                $sx = new SimpleXMLElement($out);

                $data = (array)$sx->xpath("//ss:Row");
                $rows = count($data);
                $this->factDo11 = 0;
                $this->planDo11 = 0;

                for ($row = 1; $row <= $rows; $row++) {
                    $data = (array)$sx->xpath("//ss:Row[$row]/ss:Cell[1]/ss:Data");
                    $str = $data[0][0];
//                    $nameRow = iconv("UTF-8", "windows-1251", $str);
                    $nameRow = $str;

                    if ($nameRow == "КБШ") {
                        $data = (array)$sx->xpath("//ss:Row[$row]/ss:Cell[16]/ss:Data");
                        $this->factDo11 = isset($data[0][0]) ? round($data[0][0]) : 0;
                    } elseif ($nameRow == "РЖД") {
                        $data = (array)$sx->xpath("//ss:Row[$row]/ss:Cell[16]/ss:Data");
                        $this->planDo11 = isset($data[0][0]) ? round($data[0][0]) : 0;
                    }
                }
            } else {
                Log::Error("Выгрузка из СИС-Эффект не произошла.");
            }
            curl_close($curl);

            if ($this->factDo11 == 0 && $this->planDo11 == 0) {
                Log::Warn("Получили 0 по плану и факту, возможно есть ошибки. Проверьте файл из СИС-Эффект (tmp/DO11_F02_{$this->utilsDate->year}.xml).");
            }
        }

        if ($curl = curl_init()) {
            curl_setopt($curl, CURLOPT_URL, "http://effect.gvc.oao.rzd/effect/table/DO11_F02.xml?DAT={$this->utilsDate->lastYear}.{$this->utilsDate->monthLastYear}.{$this->utilsDate->dayLastYear}");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_COOKIE, "login=smenareq;password=smenareq");
            $out = curl_exec($curl);
            file_put_contents("tmp/DO11_F02_{$this->utilsDate->lastYear}.xml", $out);
            $this->factDo11LastYear = 0;
            if ($out) {
                libxml_use_internal_errors(true);
                $sx = new SimpleXMLElement($out);

                $data = (array)$sx->xpath("//ss:Row");
                $rows = count($data);

                for ($row = 1; $row <= $rows; $row++) {
                    $data = (array)$sx->xpath("//ss:Row[$row]/ss:Cell[1]/ss:Data");
                    $str = $data[0][0];
//                    $nameRow = iconv("UTF-8", "windows-1251", $str);
                    $nameRow = $str;

                    if ($nameRow == "КБШ") {
                        $data = (array)$sx->xpath("//ss:Row[$row]/ss:Cell[16]/ss:Data");
                        $this->factDo11LastYear = isset($data[0][0]) ? round($data[0][0]) : 0;
                    }
                }
            } else {
                Log::Error("Выгрузка из СИС-Эффект не произошла.");
            }
            curl_close($curl);

            if ($this->factDo11LastYear == 0) {
                Log::Warn("Получили 0 по факту пр года, возможно есть ошибки. Проверьте файл из СИС-Эффект (tmp/DO11_F02_{$this->utilsDate->lastYear}.xml).");
            }
        }

        if ($curl = curl_init()) {
            curl_setopt($curl, CURLOPT_URL, "http://effect.gvc.oao.rzd/effect/table/DO11M26.xml?DAT={$this->utilsDate->year}.{$this->utilsDate->month}.{$this->utilsDate->day}");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_COOKIE, "login=smenareq;password=smenareq");
            $out = curl_exec($curl);
            file_put_contents("tmp/DO11M26_{$this->utilsDate->year}.xml", $out);
            $this->fact2792 = 0;
            $this->fact2792LastYear = 0;
            $this->plan2792 = 0;
            if ($out) {
                libxml_use_internal_errors(true);
                $sx = new SimpleXMLElement($out);

                $data = (array)$sx->xpath("//ss:Row");
                $rows = count($data);

                for ($row = 1; $row <= $rows; $row++) {
                    $data = (array)$sx->xpath("//ss:Row[$row]/ss:Cell[1]/ss:Data");
                    $str = $data[0][0];
//                    $nameRow = iconv("UTF-8", "windows-1251", $str);
                    $nameRow = $str;

                    if ($nameRow == "КБШ") {
                        $data = (array)$sx->xpath("//ss:Row[$row]/ss:Cell[3]/ss:Data");
                        $this->fact2792LastYear = isset($data[0][0]) ? round($data[0][0]) : 0;

                        $data = (array)$sx->xpath("//ss:Row[" . ($row + 1) . "]/ss:Cell[3]/ss:Data");
                        $this->fact2792 = isset($data[0][0]) ? round($data[0][0]) : 0;
                    } elseif ($nameRow == "РЖД") {
                        $data = (array)$sx->xpath("//ss:Row[" . ($row + 1) . "]/ss:Cell[3]/ss:Data");
                        $this->plan2792 = isset($data[0][0]) ? round($data[0][0]) : 0;
                    }
                }
            } else {
                Log::Error("Выгрузка из СИС-Эффект не произошла.");
            }
            curl_close($curl);

            if ($this->fact2792 == 0 && $this->fact2792LastYear == 0 && $this->plan2792 == 0) {
                Log::Warn("Получили 0 по данным из приложения 2792, возможно есть ошибки. Проверьте файл из СИС-Эффект (tmp/DO11M26_{$this->utilsDate->year}.xml).");
            }
        }
    }
}