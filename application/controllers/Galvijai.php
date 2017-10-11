<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ************************ CONTROLLERS ************************
 * @property Pasarai            $pasarai            Pasarai controller
 * @property Paseliai           $paseliai           Paseliai controller
 * @property Ukininkai          $ukininkai          Ukininkai controller
 * @property Galvijai           $galvijai           Galvijai controller
 * @property Auth               $auth               Auth controller
 * @property Main               $main               Main controller
 * @property Admin              $admin              Admin controller
 * ************************ MODELS *****************************
 * @property Pasarai_model      $pasarai_model      Pasarai models
 * @property Paseliai_model     $paseliai_model     Paseliai models
 * @property Ukininkai_model    $ukininkai_model    Ukininkai models
 * @property Galvijai_model     $galvijai_model     Galvijai models
 * @property Ion_auth_model     $ion_auth_model     Ion_Auth models
 * @property Main_model         $main_model         Main models
 * @property Admin_model        $admin_model        Admin models
 * ************************* LIBRARY ****************************
 * @property Ion_auth           $ion_auth           Ion_auth library
 */
class Galvijai extends CI_Controller {


    public function __construct(){
        parent::__construct();
        error_reporting(E_ERROR | E_WARNING | E_PARSE);

        //uzkraunam MODEL
        $this->load->model('ukininkai_model');
        $this->load->model('galvijai_model');
        $this->load->model('main_model');

        $this->load->library('linksniai');

        if (!$this->ion_auth->logged_in()) {
            redirect('main/auth_error');
        }
    }

    //jei kas bandys atidaryti index puslapi bus nukreiptas i pagrindini
    public function index(){
        redirect('main');
    }
    ///////////////////////////////////////////// RODOMAS GYVULIU SARASAS //////////////////////////////////////////////
    public function gyvuliu_sarasas(){
        $gyvu = array();
        $dt = $this->session->userdata();

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

        if ($dt['vardas'] == "" AND $dt['pavarde'] == "") {
            $this->form_validation->set_rules('ukininko_vardas', 'Vardas Pavardė', 'required', array('required' => 'Pasirinkite ūkininką.'));
            $ukininkas = $this->input->post('ukininko_vardas');
            $uk = $this->ukininkai_model->ukininkas($ukininkas);
            $this->main_model->info['txt']['vardas'] = $uk[0]['vardas'];
            $this->main_model->info['txt']['pavarde'] = $uk[0]['pavarde'];
            $new = array('vardas' => $uk[0]['vardas'], 'pavarde' => $uk[0]['pavarde'], 'nr' => $ukininkas);
            $this->session->set_userdata($new);
        } else {
            $ukininkas = $dt['nr'];
            $this->main_model->info['txt']['vardas'] = $dt['vardas'];
            $this->main_model->info['txt']['pavarde'] = $dt['pavarde'];
        }

            $this->form_validation->set_rules('metai', 'Metai', 'required', array('required' => 'Pasirinkite metus.'));
            $this->form_validation->set_rules('menesis', 'Menesis', 'required', array('required' => 'Pasirinkite menesį.'));

            if ($this->form_validation->run()) {
                $metai = $this->input->post('metai');
                $menesis = $this->input->post('menesis');

                $this->main_model->info['txt']['metai'] = $metai;
                $this->main_model->info['txt']['menesis'] = $menesis;

                $dat = array('ukininkas' => $ukininkas, 'metai' => $metai, 'menesis' => $menesis);
                $psl = $this->galvijai_model->nuskaityti_gyvulius($dat);
                for($i = 0; $i < count($psl); $i++){
                    $gyvu[$i]['numeris'] = $psl[$i]['numeris'];
                    $gyvu[$i]['lytis'] = $psl[$i]['lytis'];
                    $gyvu[$i]['veisle'] = $psl[$i]['veisle'];
                    $gyvu[$i]['gimimo_data'] = $psl[$i]['gimimo_data'];
                    $gyvu[$i]['laikymo_pradzia'] = $psl[$i]['laikymo_pradzia'];
                    $gyvu[$i]['laikymo_pabaiga'] = $psl[$i]['laikymo_pabaiga'];
                    $gyvu[$i]['amzius'] = $psl[$i]['amzius'];
                    $gyvu[$i]['informacija'] = $psl[$i]['informacija'];
                }

                $this->main_model->info['error']['action'] = true;
            }
        //sukeliam info, informaciniam meniu
        $this->main_model->info['txt']['meniu'] = "Galvijai";
        $this->main_model->info['txt']['info'] = "Galvijų sąrašas";

        $this->main_model->info['ukininkai'] = $this->ukininkai_model->ukininku_sarasas(TRUE);
        $this->load->view("main_view", array('data' => $data, 'gyvu' => $gyvu));
    }

    ///////////////////////////////////////////// IKELIAMI DUOMENYS IS VIC.LT  //////////////////////////////////////////////
    public function nuskaityti_vic(){
        $dt = $this->session->userdata();

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
        if($dt['vardas'] == "" AND $dt['pavarde'] == "") {
            $this->form_validation->set_rules('ukininko_vardas', 'Vardas Pavardė', 'required',  array('required' => 'Pasirinkite ūkininką.'));
            $ukininkas = $this->input->post('ukininko_vardas');
            $uk = $this->ukininkai_model->ukininkas($ukininkas);
            $this->main_model->info['txt']['vardas'] = $uk[0]['vardas'];
            $this->main_model->info['txt']['pavarde'] = $uk[0]['pavarde'];
            $new = array('vardas' => $uk[0]['vardas'], 'pavarde' => $uk[0]['pavarde'], 'nr' => $ukininkas);
            $this->session->set_userdata($new);
        }else{
            $ukininkas = $dt['nr'];
            $this->main_model->info['txt']['vardas'] = $dt['vardas'];
            $this->main_model->info['txt']['pavarde'] = $dt['pavarde'];
        }

        $this->form_validation->set_rules('data1', 'Data-1', 'required', array('required' => 'Pasirinkite data.'));
        $this->form_validation->set_rules('data2', 'Data-2', 'required', array('required' => 'Pasirinkite data.'));

        if ($this->form_validation->run()) {
            $data1 = $this->input->post('data1');
            $data2 = $this->input->post('data2');

            $this->main_model->info['txt']['data1'] = $data1;
            $this->main_model->info['txt']['data2'] = $data2;

            $menesis = explode("-", $data2);
            $menesis = $menesis[1];

            $metai = explode("-", $data2);
            $metai = $metai[0];

            $gyvi_url = "https://www.vic.lt:8102/pls/gris/private.gyvuliu_sarasas";
            $visi_url = "https://www.vic.lt:8102/pls/gris/private.laikytojo_gyvuliai_frame";

            $ukis = $this->ukininkai_model->ukininkas($ukininkas);
            $auth = $ukis[0]['VIC_vartotojo_vardas'].":".$ukis[0]['VIC_slaptazodis'];

            $post1 = ['v_data' => $data2, 'v_rus' => 1];
            $post2 = ['v_nuo' => $data1,'v_iki' => $data2, 'v_rus' => 1];

            $page = $this->galvijai_model->get_VIC($gyvi_url, $post1, $auth);
            $page2 = $this->galvijai_model->get_VIC($visi_url, $post2, $auth);

            $data_gyvi = $this->galvijai_model->Gyvi_gyvunai($page['content']);
            $data_visi = $this->galvijai_model->Visi_gyvunai($page2['content']);

            //apdoroti duomenis prie irasant i duomenu baze.
            //kiekviena irasa reikia patikrinti, artoks nera, nes prie visi galvijai dubliuojasi
            $kiek = $this->galvijai_model->tikinti_gyvulius_ikelti($metai, $menesis, $ukininkas);
            $men = array("Sausis", "Vasaris", "Kovas", "Balandis", "Gegužė", "Birželis", "Liepa",
                "Rugpjūtis", "Rugsejis", "Spalis","Lapkritis", "Gruodis");
            //reik patikrinti ar antra karta neitraukia gyvulio ta pati menesi
            //buna kad prie visu gyvuliu pagal nr dubliuojasi
            if($kiek>0){
                $this->main_model->info['error']['jau_yra'] = $metai.' '.$men[$menesis-1].', jau esate pridejes gyvulius!';
            }else{
                //ikelia duomenis i duomenu baze
                $this->galvijai_model->Irasyti_visus($data_visi, $ukininkas, $metai, $menesis);
                $this->galvijai_model->Atnaujinti_visus($data_gyvi, $ukininkas, $metai, $menesis);
                $this->main_model->info['error']['OK'] = $metai.' '.$men[$menesis-1].' galvijai įtraukti į duomenų bazę!';
            }
        }
        //sukeliam info, informaciniam meniu
        $this->main_model->info['txt']['meniu'] = "Galvijai";
        $this->main_model->info['txt']['info'] = "Naujų galvijų įtraukimas";

        $this->main_model->info['ukininkai'] = $this->ukininkai_model->ukininku_sarasas();
        $this->load->view("main_view");
    }

    ///////////////////////////////////////////// SKAICIUOJAMI GALVIJAI //////////////////////////////////////////////
    public function skaiciuoti_gyvulius(){
        $dt = $this->session->userdata();

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
        if($dt['vardas'] == "" AND $dt['pavarde'] == "") {
            $this->form_validation->set_rules('ukininko_vardas', 'Vardas Pavardė', 'required',  array('required' => 'Pasirinkite ūkininką.'));
            $ukininkas = $this->input->post('ukininko_vardas');
            $uk = $this->ukininkai_model->ukininkas($ukininkas);
            $this->main_model->info['txt']['vardas'] = $uk[0]['vardas'];
            $this->main_model->info['txt']['pavarde'] = $uk[0]['pavarde'];
            $new = array('vardas' => $uk[0]['vardas'], 'pavarde' => $uk[0]['pavarde'], 'nr' => $ukininkas);
            $this->session->set_userdata($new);
        }else{
            $ukininkas = $dt['nr'];
            $this->main_model->info['txt']['vardas'] = $dt['vardas'];
            $this->main_model->info['txt']['pavarde'] = $dt['pavarde'];
        }
        $this->form_validation->set_rules('metai', 'Metai', 'required', array('required' => 'Pasirinkite metus.'));
        $this->form_validation->set_rules('menesis', 'Menesis', 'required', array('required' => 'Pasirinkite menesį.'));

        if ($this->form_validation->run()) {
            //gaunami ukininko nustatymai
            $set = $this->galvijai_model->nustatymai($dt['nr']);
            $metai = $this->input->post('metai');
            $menesis = $this->input->post('menesis');

            $this->main_model->info['txt']['metai'] = $metai;
            $this->main_model->info['txt']['menesis'] = $menesis;
            $this->main_model->info['txt']['banda'] = $set[0]['banda'];
            //bandos nustatymas
            //1: pieniniai
            //2: mesiniai
            //3: pieniniai ir mesiniai reikia atskirti
            $banda = $set[0]['banda'];

            //nuskaitom visus gyvulius, pasirinkto menesio
            $dat = array('ukininkas' => $ukininkas, 'metai' => $metai, 'menesis' => $menesis);
            $rezultatai_dabar = $this->galvijai_model->nuskaityti_gyvulius($dat);
            //pakeiciam kintamuju vardus, jei pagrindinius noresim veliau panaudoti kad nesusigadintu
            $met = $metai;  $men = $menesis;
            if($men>1){$men--; }else{$men=12; $met--;}
            //nuskaitom visus gyvulius, pries tai buvusio menesio
            $dat = array('ukininkas' => $ukininkas, 'metai' => $met, 'menesis' => $men, 'amzius !=' => "" );
            $rezultatai_vakar = $this->galvijai_model->nuskaityti_gyvulius($dat);

            //nuskaitom gyvuliu kieki menesio pradzioje, tik kieki, daugiau nieko nereikia
            foreach($rezultatai_vakar as $sk){
                $one = explode(" ", $sk['lytis']);
                if($one[0] == "Karvė"){
                    if($banda == '3'){
                        if($sk['veisle'] == "Limuzinai"){
                        $this->galvijai_model->mesiniai['karves']['pradzia']++;}else{
                            $this->galvijai_model->galvijai['karves']['pradzia']++;
                        }
                    }else{
                        $this->galvijai_model->galvijai['karves']['pradzia']++;
                    }
                }

                if($one[0] == "Buliukas"){
                    if($sk['amzius']>=12 AND $sk['amzius']<24){
                        if($banda == '3'){
                            if($sk['veisle'] == "Limuzinai"){
                                $this->galvijai_model->mesiniai['buliai_12']['pradzia']++;}else{
                                $this->galvijai_model->galvijai['buliai_12']['pradzia']++;
                            }
                        }else{
                            $this->galvijai_model->galvijai['buliai_12']['pradzia']++;
                        }
                    }
                    if($sk['amzius']>=24){
                        if($banda == '3'){
                            if($sk['veisle'] == "Limuzinai"){
                                $this->galvijai_model->mesiniai['buliai_24']['pradzia']++;}else{
                                $this->galvijai_model->galvijai['buliai_24']['pradzia']++;
                            }
                        }else{
                            $this->galvijai_model->galvijai['buliai_24']['pradzia']++;
                        }
                    }
                    if($sk['amzius']<12 AND $sk['amzius']!=""){
                        if($banda == '3'){
                            if($sk['veisle'] == "Limuzinai"){
                                $this->galvijai_model->mesiniai['verseliai']['pradzia']++;}else{
                                $this->galvijai_model->galvijai['verseliai']['pradzia']++;
                            }
                        }else{
                            $this->galvijai_model->galvijai['verseliai']['pradzia']++;
                        }
                    }
            }

                if($one[0] == "Telyčaitė"){
                    if($sk['amzius']>=12 AND $sk['amzius']<24){
                        if($banda == '3'){
                            if($sk['veisle'] == "Limuzinai"){
                                $this->galvijai_model->mesiniai['telycios_12']['pradzia']++;}else{
                                $this->galvijai_model->galvijai['telycios_12']['pradzia']++;
                            }
                        }else{
                            $this->galvijai_model->galvijai['telycios_12']['pradzia']++;
                        }
                    }
                    if($sk['amzius']>=24){
                        if($banda == '3'){
                            if($sk['veisle'] == "Limuzinai"){
                                $this->galvijai_model->mesiniai['telycios_24']['pradzia']++;}else{
                                $this->galvijai_model->galvijai['telycios_24']['pradzia']++;
                            }
                        }else{
                            $this->galvijai_model->galvijai['telycios_24']['pradzia']++;
                        }
                    }
                    if($sk['amzius']<12 AND $sk['amzius']!=""){
                        if($banda == '3'){
                            if($sk['veisle'] == "Limuzinai"){
                                $this->galvijai_model->mesiniai['verseliai']['pradzia']++;}else{
                                $this->galvijai_model->galvijai['verseliai']['pradzia']++;
                            }
                        }else{
                            $this->galvijai_model->galvijai['verseliai']['pradzia']++;
                        }
                    }
                }
            }

            //skaiciuojam kiek gyvuliu menesio gale
            foreach($rezultatai_dabar as $sk){
                $one = explode(" ", $sk['lytis']);
                //Karviu skaiciavimas
                if($one[0] == "Karvė"){
                    //karve vis dar egzistuoja
                    if($sk['amzius'] != ""){
                        //skaiciuojam menesio pabaiga
                        if($banda == '3'){
                            if($sk['veisle'] == "Limuzinai"){
                                $this->galvijai_model->mesiniai['karves']['pabaiga']++;}else{
                                $this->galvijai_model->galvijai['karves']['pabaiga']++;
                            }
                        }else{
                            $this->galvijai_model->galvijai['karves']['pabaiga']++;
                        }
                        //nupirktos karves
                        $laikas = explode(".",$sk['laikymo_pradzia']);
                        if($laikas[0] == $metai AND $laikas[1] == $menesis){
                            if($banda == '3'){
                                if($sk['veisle'] == "Limuzinai"){
                                    $this->galvijai_model->mesiniai['karves']['pirkimai']++;}else{
                                    $this->galvijai_model->galvijai['karves']['pirkimai']++;
                                }
                            }else{
                                $this->galvijai_model->galvijai['karves']['pirkimai']++;
                            }
                        }
                        //karviu judejimas is telyciu
                        $dat = array('ukininkas' => $ukininkas, 'metai' => $met, 'menesis' => $men, 'numeris' => $sk['numeris']);
                        $this->galvijai_model->karviu_judejimas($dat, $banda);
                    }else{
                        //is telyciu pereina i karves ir parduodama, dingsta
                        //karviu judejimas is telyciu
                        $dat = array('ukininkas' => $ukininkas, 'metai' => $met, 'menesis' => $men, 'numeris' => $sk['numeris']);
                        $this->galvijai_model->karviu_judejimas($dat, $banda);

                        //issifiltruojam ivykio koda
                        $pp = $this->galvijai_model->ivykio_kodas($sk['laikymo_pabaiga']);
                        //tikrinsim pagal ivykio koda kas nutiko gyvuliui
                        $this->galvijai_model->ivykio_skaiciavimas($pp, $banda,  "karves");
                    }
                }

                //Buliuku skaiciavimas
                if($one[0] == "Buliukas"){
                    //buliukai nuo 12 iki 24
                    if($sk['amzius']>=12 AND $sk['amzius']<24){
                        if($banda == '3'){
                            if($sk['veisle'] == "Limuzinai"){
                                $this->galvijai_model->mesiniai['buliai_12']['pabaiga']++;}else{
                                $this->galvijai_model->galvijai['buliai_12']['pabaiga']++;
                            }
                        }else{
                            $this->galvijai_model->galvijai['buliai_12']['pabaiga']++;
                        }
                        //$this->galvijai_model->galvijai['buliai_12']['pabaiga']++;
                        if($sk['amzius']>=12 AND $sk['amzius']<14) {
                            $dat = array('ukininkas' => $ukininkas, 'metai' => $met, 'menesis' => $men, 'numeris' => $sk['numeris']);
                            $am = $this->galvijai_model->nuskaityti_gyvulius($dat);
                            if (!empty($am)) {
                                if ($am[0]['amzius'] < 12) {
                                    if($banda == '3'){
                                        if($sk['veisle'] == "Limuzinai"){
                                            $this->galvijai_model->mesiniai['buliai_12']['j_i']++;
                                            $this->galvijai_model->mesiniai['verseliai']['j_is']++;
                                        }else{
                                            $this->galvijai_model->galvijai['buliai_12']['j_i']++;
                                            $this->galvijai_model->galvijai['verseliai']['j_is']++;
                                        }
                                    }else{
                                        $this->galvijai_model->galvijai['buliai_12']['j_i']++;
                                        $this->galvijai_model->galvijai['verseliai']['j_is']++;
                                    }
                                    //$this->galvijai_model->galvijai['buliai_12']['j_i']++;
                                    //$this->galvijai_model->galvijai['verseliai']['j_is']++;
                                }
                            }
                        }
                        //tikrinam ar nera nupirktas
                        $lka = explode(".", $sk['laikymo_pradzia']);
                        $info = explode(" ",$sk['informacija']);
                        if($lka[0] == $metai AND $lka[1] == $menesis AND $info[1] == 'Atvyko'){
                            //$this->galvijai_model->galvijai['buliai_12']['pirkimai']++;
                            if($banda == '3'){
                                if($sk['veisle'] == "Limuzinai"){
                                    $this->galvijai_model->mesiniai['buliai_12']['pirkimai']++;}else{
                                    $this->galvijai_model->galvijai['buliai_12']['pirkimai']++;
                                }
                            }else{
                                $this->galvijai_model->galvijai['buliai_12']['pirkimai']++;
                            }
                        }

                    }

                    //Buliukai virs 24
                    if ($sk['amzius'] >= 24) {
                        //$this->galvijai_model->galvijai['buliai_24']['pabaiga']++;
                        if($banda == '3'){
                            if($sk['veisle'] == "Limuzinai"){
                                $this->galvijai_model->mesiniai['buliai_24']['pabaiga']++;}else{
                                $this->galvijai_model->galvijai['buliai_24']['pabaiga']++;
                            }
                        }else{
                            $this->galvijai_model->galvijai['buliai_24']['pabaiga']++;
                        }
                        $dat = array('ukininkas' => $ukininkas, 'metai' => $met, 'menesis' => $men, 'numeris' => $sk['numeris']);
                        $am = $this->galvijai_model->nuskaityti_gyvulius($dat);
                        if(!empty($am)){
                            if($am[0]['amzius'] < 24){
                                if($banda == '3'){
                                    if($sk['veisle'] == "Limuzinai"){
                                        $this->galvijai_model->mesiniai['buliai_24']['j_i']++;
                                        $this->galvijai_model->mesiniai['buliai_12']['j_is']++;
                                    }else{
                                        $this->galvijai_model->galvijai['buliai_24']['j_i']++;
                                        $this->galvijai_model->galvijai['buliai_12']['j_is']++;
                                    }
                                }else{
                                    $this->galvijai_model->galvijai['buliai_24']['j_i']++;
                                    $this->galvijai_model->galvijai['buliai_12']['j_is']++;
                                }
                                //$this->galvijai_model->galvijai['buliai_24']['j_i']++;
                                //$this->galvijai_model->galvijai['buliai_12']['j_is']++;
                            }
                        }
                        //tikrinam ar nera nupirktas
                        $lk = explode(".", $sk['laikymo_pradzia']);
                        $info = explode(" ",$sk['informacija']);
                        if($lk[0] == $metai AND $lk[1] == $menesis AND $info[1] == 'Atvyko'){
                            //$this->galvijai_model->galvijai['buliai_24']['pirkimai']++;
                            if($banda == '3'){
                                if($sk['veisle'] == "Limuzinai"){
                                    $this->galvijai_model->mesiniai['buliai_24']['pirkimai']++;}else{
                                    $this->galvijai_model->galvijai['buliai_24']['pirkimai']++;
                                }
                            }else{
                                $this->galvijai_model->galvijai['buliai_24']['pirkimai']++;
                            }
                        }
                    }

                    //Buliukai mezesni negu 12
                    if ($sk['amzius']<12 AND $sk['amzius'] != "") {
                        //$this->galvijai_model->galvijai['verseliai']['pabaiga']++;
                        if($banda == '3'){
                            if($sk['veisle'] == "Limuzinai"){
                                $this->galvijai_model->mesiniai['verseliai']['pabaiga']++;}else{
                                $this->galvijai_model->galvijai['verseliai']['pabaiga']++;
                            }
                        }else{
                            $this->galvijai_model->galvijai['verseliai']['pabaiga']++;
                        }

                        //tikrinti gimimus pagal laikymo pradzia, nes jei pagal gimimo data buna kad neatitinka data, buna gimsta sausi, laikymo pradzia vasari
                        //nevisada pagal gimimo data tinka gimimui indentifikuoti
                        $lp = explode(".", $sk['laikymo_pradzia']);
                        $info = explode(" ",$sk['informacija']);
                        if($lp[0] == $metai AND $lp[1] == $menesis AND $info[1] == 'Gimęs'){
                            //$this->galvijai_model->galvijai['verseliai']['gimimai']++;
                            if($banda == '3'){
                                if($sk['veisle'] == "Limuzinai"){
                                    $this->galvijai_model->mesiniai['verseliai']['gimimai']++;}else{
                                    $this->galvijai_model->galvijai['verseliai']['gimimai']++;
                                }
                            }else{
                                $this->galvijai_model->galvijai['verseliai']['gimimai']++;
                            }
                        }
                        //reik del gimimu dar patikrinti ar nera atgaline tvarka irasytas
                        if($lp[0] == $metai AND $lp[1] == $menesis-1 AND $info[1] == 'Gimęs') {
                            $dat = array('ukininkas' => $ukininkas, 'metai' => $met, 'menesis' => $men, 'numeris' => $sk['numeris']);
                            $am = $this->galvijai_model->nuskaityti_gyvulius($dat);
                            if(empty($am)){
                                //$this->galvijai_model->galvijai['verseliai']['gimimai']++;
                                if($banda == '3'){
                                    if($sk['veisle'] == "Limuzinai"){
                                        $this->galvijai_model->mesiniai['verseliai']['gimimai']++;}else{
                                        $this->galvijai_model->galvijai['verseliai']['gimimai']++;
                                    }
                                }else{
                                    $this->galvijai_model->galvijai['verseliai']['gimimai']++;
                                }
                        }
                    }
                        if($lp[0] == $metai AND $lp[1] == $menesis AND $info[1] == 'Atvyko'){
                            //$this->galvijai_model->galvijai['verseliai']['pirkimai']++;
                            if($banda == '3'){
                                if($sk['veisle'] == "Limuzinai"){
                                    $this->galvijai_model->mesiniai['verseliai']['pirkimai']++;}else{
                                    $this->galvijai_model->galvijai['verseliai']['pirkimai']++;
                                }
                            }else{
                                $this->galvijai_model->galvijai['verseliai']['pirkimai']++;
                            }
                        }
                    }

                    //jei yra tuscias, reikia galvijai kazkur iskeliavo, reik issiaiskintu kur
                    if($sk['amzius']==""){
                        $pr = str_replace(".", "-", $sk['gimimo_data']);
                        if(strstr($sk['laikymo_pabaiga'], '*')){
                            $pp = explode("*", $sk['laikymo_pabaiga']);
                            $pp = explode(" ", $pp[1]);
                        }else{
                            $pp = explode(" ", $sk['laikymo_pabaiga']);
                        }

                        $pb = str_replace(".", "-", $pp[0]);
                        $pb = str_replace(">", "", $pb);

                        $da = $this->galvijai_model->dateDifference($pr, $pb, '%y-%m-%d');
                        $dd = explode("-", $da);
                        $mo = $dd[0] * 12 + $dd[1];

                        //reik atsifiltruoti dingimo koduka, gali buti ne tik parduota bet ir kritimas arba suvartota sau
                       $pa = $this->galvijai_model->ivykio_kodas($sk['laikymo_pabaiga']);


                        if ($mo >= 12 AND $mo < 24) {
                            $dat = array('ukininkas' => $ukininkas, 'metai' => $met, 'menesis' => $men, 'numeris' => $sk['numeris']);
                            $am = $this->galvijai_model->nuskaityti_gyvulius($dat);
                            if($am[0]['amzius']<12){
                                if($banda == '3'){
                                    if($sk['veisle'] == "Limuzinai"){
                                        $this->galvijai_model->mesiniai['buliai_12']['j_i']++;
                                        $this->galvijai_model->mesiniai['verseliai']['j_is']++;
                                    }else{
                                        $this->galvijai_model->galvijai['buliai_12']['j_i']++;
                                        $this->galvijai_model->galvijai['verseliai']['j_is']++;
                                    }
                                }else{
                                    $this->galvijai_model->galvijai['buliai_12']['j_i']++;
                                    $this->galvijai_model->galvijai['verseliai']['j_is']++;
                                }
                                //$this->galvijai_model->galvijai['verseliai']['j_is']++;
                                //$this->galvijai_model->galvijai['buliai_12']['j_i']++;
                            }

                            $this->galvijai_model->ivykio_skaiciavimas($pa, $banda, "buliai_12");
                        }
                        if ($mo >= 24) {
                            $dat = array('ukininkas' => $ukininkas, 'metai' => $met, 'menesis' => $men, 'numeris' => $sk['numeris']);
                            $am = $this->galvijai_model->nuskaityti_gyvulius($dat);
                            if($am[0]['amzius']<24){
                                if($banda == '3'){
                                    if($sk['veisle'] == "Limuzinai"){
                                        $this->galvijai_model->mesiniai['buliai_24']['j_i']++;
                                        $this->galvijai_model->mesiniai['buliai_12']['j_is']++;
                                    }else{
                                        $this->galvijai_model->galvijai['buliai_24']['j_i']++;
                                        $this->galvijai_model->galvijai['buliai_12']['j_is']++;
                                    }
                                }else{
                                    $this->galvijai_model->galvijai['buliai_24']['j_i']++;
                                    $this->galvijai_model->galvijai['buliai_12']['j_is']++;
                                }
                                //$this->galvijai_model->galvijai['buliai_12']['j_is']++;
                                //$this->galvijai_model->galvijai['buliai_24']['j_i']++;
                            }

                            $this->galvijai_model->ivykio_skaiciavimas($pa, $banda, "buliai_24");
                        }
                        if ($mo < 12) {
                            $lp = explode(".", $sk['laikymo_pradzia']);
                            $info = explode(" ",$sk['informacija']);
                            if($lp[0] == $metai AND $lp[1] == $menesis AND $info[1] == 'Gimęs'){
                                //$this->galvijai_model->galvijai['verseliai']['gimimai']++;
                                if($banda == '3'){
                                    if($sk['veisle'] == "Limuzinai"){
                                        $this->galvijai_model->mesiniai['verseliai']['gimimai']++;}else{
                                        $this->galvijai_model->galvijai['verseliai']['gimimai']++;
                                    }
                                }else{
                                    $this->galvijai_model->galvijai['verseliai']['gimimai']++;
                                }
                            }

                            $this->galvijai_model->ivykio_skaiciavimas($pa, $banda, "verseliai");
                        }



                    }
                }

                if($one[0] == "Telyčaitė"){
                    //Telycaites nuo 12 iki 24
                    if($sk['amzius']>=12 AND $sk['amzius']<24){
                        //$this->galvijai_model->galvijai['telycios_12']['pabaiga']++;
                        if($banda == '3'){
                            if($sk['veisle'] == "Limuzinai"){
                                $this->galvijai_model->mesiniai['telycios_12']['pabaiga']++;}else{
                                $this->galvijai_model->galvijai['telycios_12']['pabaiga']++;
                            }
                        }else{
                            $this->galvijai_model->galvijai['telycios_12']['pabaiga']++;
                        }

                        if($sk['amzius']>=12 AND $sk['amzius']<14) {
                            $dat = array('ukininkas' => $ukininkas, 'metai' => $met, 'menesis' => $men, 'numeris' => $sk['numeris']);
                            $am = $this->galvijai_model->nuskaityti_gyvulius($dat);
                            if(!empty($am) AND $am[0]['amzius']<12){
                                if($banda == '3'){
                                    if($sk['veisle'] == "Limuzinai"){
                                        $this->galvijai_model->mesiniai['telycios_12']['j_i']++;
                                        $this->galvijai_model->mesiniai['verseliai']['j_is']++;
                                    }else{
                                        $this->galvijai_model->galvijai['telycios_12']['j_i']++;
                                        $this->galvijai_model->galvijai['verseliai']['j_is']++;
                                    }
                                }else{
                                    $this->galvijai_model->galvijai['telycios_12']['j_i']++;
                                    $this->galvijai_model->galvijai['verseliai']['j_is']++;
                                }
                                //$this->galvijai_model->galvijai['telycios_12']['j_i']++;
                                //$this->galvijai_model->galvijai['verseliai']['j_is']++;
                            }
                        }
                        //pirkimai
                        $lk = explode(".", $sk['laikymo_pradzia']);
                        $info = explode(" ",$sk['informacija']);
                        if($lk[0] == $metai AND $lk[1] == $menesis AND $info[1] == 'Atvyko'){
                            //$this->galvijai_model->galvijai['telycios_12']['pirkimai']++;
                            if($banda == '3'){
                                if($sk['veisle'] == "Limuzinai"){
                                    $this->galvijai_model->mesiniai['telycios_12']['pirkimai']++;}else{
                                    $this->galvijai_model->galvijai['telycios_12']['pirkimai']++;
                                }
                            }else{
                                $this->galvijai_model->galvijai['telycios_12']['pirkimai']++;
                            }
                        }
                    }

                    //Telycaites virs 24
                    if ($sk['amzius'] >= 24) {
                        //$this->galvijai_model->galvijai['telycios_24']['pabaiga']++;
                        if($banda == '3'){
                            if($sk['veisle'] == "Limuzinai"){
                                $this->galvijai_model->mesiniai['telycios_24']['pabaiga']++;}else{
                                $this->galvijai_model->galvijai['telycios_24']['pabaiga']++;
                            }
                        }else{
                            $this->galvijai_model->galvijai['telycios_24']['pabaiga']++;
                        }

                        $dat = array('ukininkas' => $ukininkas, 'metai' => $met, 'menesis' => $men, 'numeris' => $sk['numeris']);
                        $am = $this->galvijai_model->nuskaityti_gyvulius($dat);
                        if(!empty($am)){
                            if($am[0]['amzius']<24){
                                if($banda == '3'){
                                    if($sk['veisle'] == "Limuzinai"){
                                        $this->galvijai_model->mesiniai['telycios_24']['j_i']++;
                                        $this->galvijai_model->mesiniai['telycios_12']['j_is']++;
                                    }else{
                                        $this->galvijai_model->galvijai['telycios_24']['j_i']++;
                                        $this->galvijai_model->galvijai['telycios_12']['j_is']++;
                                    }
                                }else{
                                    $this->galvijai_model->galvijai['telycios_24']['j_i']++;
                                    $this->galvijai_model->galvijai['telycios_12']['j_is']++;
                                }
                            //$this->galvijai_model->galvijai['telycios_24']['j_i']++;
                            //$this->galvijai_model->galvijai['telycios_12']['j_is']++;
                            }
                        }
                        //pirkimai
                        $lk = explode(".", $sk['laikymo_pradzia']);
                        $info = explode(" ",$sk['informacija']);
                        if($lk[0] == $metai AND $lk[1] == $menesis AND $info[1] == 'Atvyko'){
                            //$this->galvijai_model->galvijai['telycios_24']['pirkimai']++;
                            if($banda == '3'){
                                if($sk['veisle'] == "Limuzinai"){
                                    $this->galvijai_model->mesiniai['telycios_24']['pirkimai']++;}else{
                                    $this->galvijai_model->galvijai['telycios_24']['pirkimai']++;
                                }
                            }else{
                                $this->galvijai_model->galvijai['telycios_24']['pirkimai']++;
                            }
                        }
                    }

                    //Telycaites mazesnios negu 12
                    if ($sk['amzius']<12 AND $sk['amzius'] != "") {
                        //$this->galvijai_model->galvijai['verseliai']['pabaiga']++;
                        if($banda == '3'){
                            if($sk['veisle'] == "Limuzinai"){
                                $this->galvijai_model->mesiniai['verseliai']['pabaiga']++;}else{
                                $this->galvijai_model->galvijai['verseliai']['pabaiga']++;
                            }
                        }else{
                            $this->galvijai_model->galvijai['verseliai']['pabaiga']++;
                        }

                        $lp = explode(".", $sk['laikymo_pradzia']);
                        $info = explode(" ",$sk['informacija']);
                        if($lp[0] == $metai AND $lp[1] == $menesis AND $info[1] == 'Gimęs'){
                            //$this->galvijai_model->galvijai['verseliai']['gimimai']++;
                            if($banda == '3'){
                                if($sk['veisle'] == "Limuzinai"){
                                    $this->galvijai_model->mesiniai['verseliai']['gimimai']++;}else{
                                    $this->galvijai_model->galvijai['verseliai']['gimimai']++;
                                }
                            }else{
                                $this->galvijai_model->galvijai['verseliai']['gimimai']++;
                            }
                        }
                        //reik del gimimu dar patikrinti ar nera atgaline tvarka irasytas
                        if($lp[0] == $metai AND $lp[1] == $menesis-1 AND $info[1] == 'Gimęs') {
                            $dat = array('ukininkas' => $ukininkas, 'metai' => $met, 'menesis' => $men, 'numeris' => $sk['numeris']);
                            $am = $this->galvijai_model->nuskaityti_gyvulius($dat);
                            if(empty($am)){
                                //$this->galvijai_model->galvijai['verseliai']['gimimai']++;
                                if($banda == '3'){
                                    if($sk['veisle'] == "Limuzinai"){
                                        $this->galvijai_model->mesiniai['verseliai']['gimimai']++;}else{
                                        $this->galvijai_model->galvijai['verseliai']['gimimai']++;
                                    }
                                }else{
                                    $this->galvijai_model->galvijai['verseliai']['gimimai']++;
                                }
                            }
                        }

                        if($lp[0] == $metai AND $lp[1] == $menesis AND $info[1] == 'Atvyko'){
                            //$this->galvijai_model->galvijai['verseliai']['pirkimai']++;
                            if($banda == '3'){
                                if($sk['veisle'] == "Limuzinai"){
                                    $this->galvijai_model->mesiniai['verseliai']['pirkimai']++;}else{
                                    $this->galvijai_model->galvijai['verseliai']['pirkimai']++;
                                }
                            }else{
                                $this->galvijai_model->galvijai['verseliai']['pirkimai']++;
                            }
                        }
                    }

                    //jei yra tuscias, reikia galvijai kazkur iskeliavo, reik issiaiskintu kur
                    //reik atsifiltruoti dingimo koduka, gali buti ne tik parduota bet ir kritimas arba suvartota sau
                    ////////////////////////////////////////////
                    //pasitikrinti amziu ar pries pardavima nebuvo kitoje kategorijoje, perejo ir iskart pardave

                    if($sk['amzius']==""){
                        $pr = str_replace(".", "-", $sk['gimimo_data']);

                        if(strstr($sk['laikymo_pabaiga'], '*')){
                            $pp = explode("*", $sk['laikymo_pabaiga']);
                            $pp = explode(" ", $pp[1]);
                        }else{
                            $pp = explode(" ", $sk['laikymo_pabaiga']);
                        }
                        $pb = str_replace(".", "-", $pp[0]);
                        $pb = str_replace(">", "", $pb);

                        $da = $this->galvijai_model->dateDifference($pr, $pb, '%y-%m-%d');
                        $dd = explode("-", $da);
                        $mo = $dd[0] * 12 + $dd[1];
                        //reik atsifiltruoti dingimo koduka, gali buti ne tik parduota bet ir kritimas arba suvartota sau
                        $pa= $this->galvijai_model->ivykio_kodas($sk['laikymo_pabaiga']);

                        //tikrinama kas atsitiko gyvuliams, kur dingo?
                            if ($mo >= 12 AND $mo < 24) {
                                $dat = array('ukininkas' => $ukininkas, 'metai' => $met, 'menesis' => $men, 'numeris' => $sk['numeris']);
                                $am = $this->galvijai_model->nuskaityti_gyvulius($dat);
                                if($am[0]['amzius']<12){
                                    if($banda == '3'){
                                        if($sk['veisle'] == "Limuzinai"){
                                            $this->galvijai_model->mesiniai['telycios_12']['j_i']++;
                                            $this->galvijai_model->mesiniai['verseliai']['j_is']++;
                                        }else{
                                            $this->galvijai_model->galvijai['telycios_12']['j_i']++;
                                            $this->galvijai_model->galvijai['verseliai']['j_is']++;
                                        }
                                    }else{
                                        $this->galvijai_model->galvijai['telycios_12']['j_i']++;
                                        $this->galvijai_model->galvijai['verseliai']['j_is']++;
                                    }
                                    //$this->galvijai_model->galvijai['verseliai']['j_is']++;
                                    //$this->galvijai_model->galvijai['telycios_12']['j_i']++;
                                }

                                $this->galvijai_model->ivykio_skaiciavimas($pa, $banda, "telycios_12");
                            }
                            if ($mo >= 24) {
                                $dat = array('ukininkas' => $ukininkas, 'metai' => $met, 'menesis' => $men, 'numeris' => $sk['numeris']);
                                $am = $this->galvijai_model->nuskaityti_gyvulius($dat);
                                if($am[0]['amzius']<24){
                                    if($banda == '3'){
                                        if($sk['veisle'] == "Limuzinai"){
                                            $this->galvijai_model->mesiniai['telycios_24']['j_i']++;
                                            $this->galvijai_model->mesiniai['telycios_12']['j_is']++;
                                        }else{
                                            $this->galvijai_model->galvijai['telycios_24']['j_i']++;
                                            $this->galvijai_model->galvijai['telycios_12']['j_is']++;
                                        }
                                    }else{
                                        $this->galvijai_model->galvijai['telycios_24']['j_i']++;
                                        $this->galvijai_model->galvijai['telycios_12']['j_is']++;
                                    }
                                    //$this->galvijai_model->galvijai['telycios_12']['j_is']++;
                                    //$this->galvijai_model->galvijai['telycios_24']['j_i']++;
                                }

                                $this->galvijai_model->ivykio_skaiciavimas($pa, $banda, "telycios_24");
                            }
                            if ($mo < 12) {
                                $lp = explode(".", $sk['laikymo_pradzia']);
                                $info = explode(" ",$sk['informacija']);
                                if($lp[0] == $metai AND $lp[1] == $menesis AND $info[1] == 'Gimęs'){
                                    //$this->galvijai_model->galvijai['verseliai']['gimimai']++;
                                    if($banda == '3'){
                                        if($sk['veisle'] == "Limuzinai"){
                                            $this->galvijai_model->mesiniai['verseliai']['gimimai']++;}else{
                                            $this->galvijai_model->galvijai['verseliai']['gimimai']++;
                                        }
                                    }else{
                                        $this->galvijai_model->galvijai['verseliai']['gimimai']++;
                                    }
                                }

                                $this->galvijai_model->ivykio_skaiciavimas($pa, $banda,  "verseliai");
                            }
                    }
                }
            }
            //suskaiciuoti lenteleje, viso kiekius GYVULIAI
            $keys = array_keys($this->galvijai_model->galvijai['karves']);
            foreach($keys as $ro){
                $sumDetail = $ro;
                $this->galvijai_model->galvijai['viso'][$ro] = array_reduce($this->galvijai_model->galvijai,
                    function($runningTotal, $record) use($sumDetail) {
                        $runningTotal += $record[$sumDetail];
                        return $runningTotal;}, 0 );
            }

            //suskaiciuoti lenteleje, viso kiekius MESINIAI
            if($banda == '3') {
                $keys = array_keys($this->galvijai_model->mesiniai['karves']);
                foreach ($keys as $ro) {
                    $sumDetail = $ro;
                    $this->galvijai_model->mesiniai['viso'][$ro] = array_reduce($this->galvijai_model->mesiniai,
                        function ($runningTotal, $record) use ($sumDetail) {
                            $runningTotal += $record[$sumDetail];
                            return $runningTotal;
                        }, 0);
                }
            }

            $this->main_model->info['error']['action'] = true;
        }

        //sukeliam info, informaciniam meniu
        $this->main_model->info['txt']['meniu'] = "Galvijai";
        $this->main_model->info['txt']['info'] = "Galvijų skaičiavimas";

        $this->main_model->info['ukininkai'] = $this->ukininkai_model->ukininku_sarasas(TRUE);
        $this->load->view("main_view");

    }

}
?>
