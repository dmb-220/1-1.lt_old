<div class="wrapper wrapper-content animated fadeInRight">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>Pasirinkite</h5>
            <div class="ibox-tools">
                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                <a class="close-link"><i class="fa fa-times"></i></a>
            </div>
        </div>
        <div class="ibox-content">
            <form class="form-horizontal form-bordered" action="<?= base_url(); ?>pasarai/apskaiciuoti_pasarus" method="POST">
                <?php
                $dt = $this->session->userdata();
                ?>
                <fieldset>
                    <?php
                    if($error['laikas']){
                        echo'<div class="alert alert-danger">';
                        echo $error['laikas'];
                        echo '</div>';
                    }
                    if($error['laikas2']) {
                        echo '<div class="alert alert-danger">';
                        echo $error['laikas2'];
                        echo '</div>';
                    }
                    ?>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Ūkininkas</label>
                        <div class="col-md-6">
                            <?php echo form_error('ukininko_vardas');
                            if($dt['vardas'] == "" AND $dt['pavarde'] == "") { ?>
                                <select name="ukininko_vardas" class="form-control">
                                    <option value="">Pasirinkite...</option>
                                    <?php
                                    foreach ($data as $row) {
                                        echo "<option value='$row[valdos_nr]'>";
                                        echo $row[vardas];
                                        echo " ";
                                        echo $row[pavarde];
                                        echo "</option>";
                                    }
                                    ?>
                                </select>
                                <?php
                            }else{
                                echo '<select name="ukininko_vardas" class="form-control" disabled>';
                                echo'<option value='.$dt['nr'].' selected="selected">'.$dt['vardas'].' '.$dt['pavarde'].'</option>';
                                echo'</select>';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Metai</label>
                        <div class="col-md-6">
                            <?php echo form_error('metai'); ?>
                            <select name="metai" class="form-control">
                                <option value="2016">2016</option>
                                <option value="2017" selected="selected">2017</option>
                                <option value="2018">2018</option>
                                <option value="2019">2019</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Menesis</label>
                        <div class="col-md-6">
                            <?php
                            $men = array("Sausis", "Vasaris", "Kovas", "Balandis", "Gegužė", "Birželis", "Liepa",
                                "Rugpjūtis", "Rugsejis", "Spalis","Lapkritis", "Gruodis");
                            ?>
                            <?php echo form_error('menesis'); ?>
                            <select name="menesis" class="form-control">
                                <option value="">Pasirinkite...</option>
                                <?php
                                for($i=0; $i<count($men); $i++) {
                                    $mm = $i+1;
                                    echo"<option value=".$mm.">";
                                    echo $men[$i];
                                    echo"</option>";
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Laikotarpis</label>
                        <div class="col-md-6">
                            <?php
                            $lai = array("I pusmetis", "II pusmetis", "I ketvirtis", "II ketvirtis", "III ketvirtis", "IV ketvirtis");
                            ?>
                            <?php echo form_error('laikotarpis'); ?>
                            <select name="laikotarpis" class="form-control">
                                <option value="">Pasirinkite...</option>
                                <?php
                                for($i=0; $i<count($lai); $i++) {
                                    $mm = $i+1;
                                    echo"<option value=".$mm.">";
                                    echo $lai[$i];
                                    echo"</option>";
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4"></label>
                        <div class="col-md-6 col-sm-6">
                            <button class="btn btn-block btn-outline btn-primary" type="submit">
                                <i class="fa fa-check-circle-o fa-lg"> SKAITČIUOTI</i>
                            </button>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

<?php
//var_dump($gyvuliai);
if($error['action']){ ?>
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Informacija</h5>
                <div class="ibox-tools">
                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    <a class="close-link"><i class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="ibox-content">
    <div class="table-responsive">
        <h4><strong>
                <p class="text-center">GYVULIŲ PAŠARŲ LENTELĖ</p>
            </strong></h4></br></br>
        <div class="pull-left">
            <?php echo $this->linksniai->getName($inf['vardas'], 'kil')." ".$this->linksniai->getName($inf['pavarde'],'kil')." ūkis"; ?>
        </div>
        <div class="pull-right">
            <?php
            if($inf['menesis']){
            $num_day = cal_days_in_month(CAL_GREGORIAN, $inf['menesis'], $inf['metai']);
            echo $inf['metai']." ".$men[$inf['menesis']-1]." 1 - ".$num_day;
            }
            if($inf['laikotarpis']){
                echo $inf['metai']." <b>".$inf['laikotarpis']."</b>";
            }
            ?>
        </div>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Gyvuliai</th>
                <th>Kiek</th>
                <th>Šienas</th>
                <th>Šiaudai</th>
                <th>Grudai</th>
                <th>Šakniavaisiai</th>
                <th>Šienainis</th>
                <th>Bulves</th>
                <th>Silosas</th>
            </tr>
            </thead>
            <tbody>
            <?php

            foreach($gyvu as $col){
                $ke = array_keys($col);
                echo"<tr>";
            for($i = 0; $i < count($ke); $i++) {
                echo "<td>";
                    if ($col[$ke[$i]] != 0 OR $col[$ke[$i]] != '') {
                        if ($ke[$i] == 'kiek') {
                            echo $col[$ke[$i]];
                            echo " vnt.";
                        } else if ($ke[$i] == 'pavadinimas') {
                            echo $col[$ke[$i]];
                        } else {
                            if ($col[$ke[$i]]['vid'] != 0 OR $col[$ke[$i]]['vid'] != '') {
                                if( $col[$ke[$i]]['vid'] ==  $col[$ke[$i]]['min'] AND $col[$ke[$i]]['vid'] == $col[$ke[$i]]['max']){
                                    echo round($col[$ke[$i]]['vid'] / 1000, 2)." T.<br>";
                                }else{
                                    echo "MIN: ".round($col[$ke[$i]]['min'] / 1000, 2)." T.<br>";
                                    echo "VID: ".round($col[$ke[$i]]['vid'] / 1000, 2)." T.<br>";
                                    echo "MAX: ".round($col[$ke[$i]]['max'] / 1000, 2)." T.";
                                }
                            }
                        }
                    }
                echo "</td>";
            }
                echo"</tr>";
            }

            ?>
            </tbody>
        </table>
    </div>
                <div class="form-group">
                    <button class="btn btn-block btn-outline btn-primary" type="button" onclick="PrintElem('.table-responsive')">
                        <i class="fa fa-check-circle-o fa-lg"> SPAUSDINTI</i>
                    </button>
                </div>
    </div>
    </div>
<?php }
?>
</div>
