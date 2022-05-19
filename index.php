<?php

require_once "D:/wwwnew/libPHP/UtilsDate.php";
require_once "D:/wwwnew/libPHP/Database.php";
require_once "D:/wwwnew/libPHP/AllDatabase.php";

require_once 'Log.php';
require_once 'App.php';

require_once "models/BaseIndicator.php";
require_once "models/CargoLoading.php";
require_once "models/TimePassengerTrainByStations.php";
require_once "models/TimeCitiesTrainByStation.php";
require_once "models/TimeCargoTrain.php";
require_once "models/GrOtprInGrVag.php";
require_once "models/SkDostGrOtpravok.php";
require_once "models/GruzooborotProbegVag.php";
require_once "models/ExplToTarifGruzooborot.php";
require_once "models/SrVesBruttoGrPoezd.php";
require_once "models/UchSpeedDo10.php";
require_once "models/UchToTehnSkor.php";
require_once "models/ProizvLokRabPark.php";
require_once "models/ProizvLokExplPark.php";
require_once "models/ColShodovPoezda.php";
require_once "models/ColStolknPoezda.php";

Log::Info("--Start program--");

try {
    $app = new App();
    $app->run();
} catch (Exception $ex) {
    Log::Error("Произошла ошибка во время работы программы.\nСообщение: {$ex->getMessage()}\nВесь трейс: {$ex->getTraceAsString()}");
}

Log::Info("--End program--");
