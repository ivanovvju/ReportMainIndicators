<?php

/**
 * Базовый класс формирования данных показателя.
 */
abstract class BaseIndicator
{
    /**
     * @var string Значение столбца "Факт прош год".
     */
    private $factLastYear;

    /**
     * @var string Значение столбца "План".
     */
    private $plan;

    /**
     * @var string Значение столбца "Факт тек год".
     */
    private $fact;

    /**
     * @var string Значение столбца "К плану %".
     */
    private $toPlanPercent;

    /**
     * @var string Значение столбца "К плану +/-".
     */
    private $toPlan;

    /**
     * @var string Значение столбца "К пр году %".
     */
    private $toLastYearPercent;

    /**
     * @var string Значение столбца "К пр году +/-".
     */
    private $toLastYear;

    /**
     * @var string Значение столбца "Критерий".
     */
    private $benchmark;

    /**
     * @var AllDatabase Подключение к БД DOCLAD.
     */
    protected $connectDoclad;

    /**
     * @var AllDatabase Подключение к БД NODUDB.
     */
    protected $connectNodudb;

    /**
     * @var int Код показателя, который должен присвоиться только при создании его объекта.
     */
    private $codeIndicator;

    public function __construct($codeIndicator)
    {
        $this->setCodeIndicator($codeIndicator);

        $this->connectDoclad = new AlLDatabase('DOCLAD');
        $this->connectDoclad->connect();

        $this->connectNodudb = new AlLDatabase('NODUDB');
        $this->connectNodudb->connect();
    }

    /**
     * @param int $codeIndicator
     */
    public function setCodeIndicator($codeIndicator)
    {
        $this->codeIndicator = $codeIndicator;
    }

    /**
     * @param mixed $factLastYear
     */
    public function setFactLastYear($factLastYear)
    {
        $this->factLastYear = $factLastYear;
    }

    /**
     * @param mixed $plan
     */
    public function setPlan($plan)
    {
        $this->plan = $plan;
    }

    /**
     * @param mixed $fact
     */
    public function setFact($fact)
    {
        $this->fact = $fact;
    }

    /**
     * @param mixed $toPlanPercent
     */
    public function setToPlanPercent($toPlanPercent)
    {
        $this->toPlanPercent = $toPlanPercent;
    }

    /**
     * @param mixed $toPlan
     */
    public function setToPlan($toPlan)
    {
        $this->toPlan = $toPlan;
    }

    /**
     * @param mixed $toLastYearPercent
     */
    public function setToLastYearPercent($toLastYearPercent)
    {
        $this->toLastYearPercent = $toLastYearPercent;
    }

    /**
     * @param mixed $toLastYear
     */
    public function setToLastYear($toLastYear)
    {
        $this->toLastYear = $toLastYear;
    }

    /**
     * @param mixed $benchmark
     */
    public function setBenchmark($benchmark)
    {
        $this->benchmark = $benchmark;
    }

    /**
     * @return int
     */
    public function getCodeIndicator()
    {
        return $this->codeIndicator;
    }

    /**
     * @return mixed
     */
    public function getFactLastYear()
    {
        return $this->factLastYear;
    }

    /**
     * @return mixed
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * @return mixed
     */
    public function getFact()
    {
        return $this->fact;
    }

    /**
     * @return mixed
     */
    public function getToPlanPercent()
    {
        return $this->toPlanPercent;
    }

    /**
     * @return mixed
     */
    public function getToPlan()
    {
        return $this->toPlan;
    }

    /**
     * @return mixed
     */
    public function getToLastYearPercent()
    {
        return $this->toLastYearPercent;
    }

    /**
     * @return mixed
     */
    public function getToLastYear()
    {
        return $this->toLastYear;
    }

    /**
     * @return mixed
     */
    public function getBenchmark()
    {
        return $this->benchmark;
    }

    /**
     * Инициализация столбца "к плану %".
     * @return void
     */
    public function iniToPlanPercent()
    {
        $fact = $this->getFact();
        $plan = $this->getPlan();
        $toPlanPercent = $plan != 0 ? round($fact / $plan * 100, 1) : 0.0;
        $this->setToPlanPercent($toPlanPercent);
    }

    /**
     * Инициализация столбца "к плану +/-".
     * @return void
     */
    public function iniToPlan()
    {
        $fact = $this->getFact();
        $plan = $this->getPlan();
        $toPlan = round($fact - $plan, 2);
        $this->setToPlan($toPlan);
    }

    /**
     * Инициализация столбца "к пр году %".
     * @return void
     */
    public function iniToLastYearPercent()
    {
        $fact = $this->getFact();
        $factLastYear = $this->getFactLastYear();
        $toLastYear = $factLastYear != 0 ? round($fact / $factLastYear * 100, 1) : 0.0;
        $this->setToLastYearPercent($toLastYear);
    }

    /**
     * Инициализация столбца "к пр году +/-".
     * @return void
     */
    public function iniToLastYear()
    {
        $fact = $this->getFact();
        $factLastYear = $this->getFactLastYear();
        $toLastYear = round($fact - $factLastYear, 2);
        $this->setToLastYear($toLastYear);
    }

    /**
     * Инициализация столбца "Факт пр год".
     * @return mixed
     */
    abstract public function iniFactLastYear();

    /**
     * Инициализация столбца "План".
     * @return mixed
     */
    abstract public function iniPlan();

    /**
     * Инициализация столбца "Факт тек год".
     * @return mixed
     */
    abstract public function iniFact();

    /**
     * Инициализация столбца "Критерий".
     * @return mixed
     */
    abstract public function iniBenchmark();

}