<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="mysli">
        <meta name="date" content="2017-07-19">
        <meta name="robots" content="noindex,nofollow">

        <title>Dateneingabe</title>
    </head>
    <body>
        <?php
        require_once ( 'capturing.php' );
        $acq = new capturing();
        ?>

        <h1>Zählerstand eintragen</h1>
        <form action="<?php $acq->printInput() ?>" method="post">
            <div id="epower">
                <h4 id="epower">Elektroenergie</h4>
                <!--form name="form1" method="post" action="<!--?php printInput() ?>"-->  
                <p>Erfasst am: <input type="text" name="eablesedatum" id="date" value="<?php print("$acq->today") ?>"/></p>
                <p>Zählerstand Elektro: <input type="text" name="epower" id="epower" value="<?php print($acq->getEPowerRecord()->value) ?>"/>kWh</p>
                <p>Bemerkung: <input type="text" name="enote" /></p>
            </div>
            <div>
            &Delta; letzter Eintrag: <?php print($acq->getEPowerRecord()->report->getDelta()) ?> kWh.
            </div>
            <div id="gas">
                <h4 id="gas">Gas</h4>
                <p>Erfasst am: <input type="text" name="gasablesedatum" id="date" value="<?php print("$acq->today") ?>"/></p>
                <p>Zählerstand Gas: <input type="text" name="gas" id="gas" value="<?php print($acq->getGasRecord()->value) ?>"/> m<sup>3</sup></p>
                <p>Bemerkung: <input type="text" name="gasnote" /></p>
            </div>
            &Delta; letzter Eintrag: <?php print($acq->getGasRecord()->report->getDelta()) ?> m<sup>3</sup>.
            <div id="water">
                <h4 id="water">Wasser</h4>
                <p>Erfasst am: <input type="text" name="waterablesedatum" id="date" value="<?php print("$acq->today") ?>"/></p>
                <p>Zählerstand Wasser: <input type="text" name="water" id="water" value="<?php print($acq->getWaterRecord()->value) ?>"/>m<sup>3</sup></p>
                <p>Bemerkung: <input type="text" name="waternote" /></p>
            </div>
            <div>
            &Delta; letzter Eintrag: <?php print($acq->getWaterRecord()->report->getDelta()) ?> m<sup>3</sup>.
            </div>
            <p><input type="submit" /></p>
        </form>


    </body>
</html>
