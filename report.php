<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/foundation.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/fullcalendar.css">
    <script type="text/javascript" src="js/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="js/vendor/custom.modernizr.js"></script>
    <script type="text/javascript" src="js/foundation.min.js"></script>
    <script type="text/javascript" src="js/jquery.stickytableheaders.min.js"></script>
    <script type="text/javascript" src="report.js"></script>
  </head>
  <body>
    <?php require_once("menu.php"); ?>
  
    <div class="row">
      <div class="large-12 columns">
        <h1>Laporan Kamar</h1>
        <hr />
        <div id="ajax_message"></div>
      </div>
    </div>
  
    <div class="row">
      <div class="large-12 columns">
        <form>
          <div class="row">
            <div class="large-6 columns">
              <div class="row">
                <div class="large-6 columns">
                  <label for="report_year" class="inline">Tahun</label>
                  <input id="report_year" name="report[year]" maxlength="4" value="<?php echo date("Y")?>" />
                </div>
                <div class="large-6 columns">
                  <label for="report_month" class="inline">Bulan</label>
                  <select id="report_month" name="report[month]">
                    <?php 
                      for ($i = 1; $i <= 12; $i++) 
                      {
                        if (strlen($i) == 1) $i = "0".$i;
                    ?>
                      <option <?php if ($i == date("m")) echo "selected"; ?>><?php echo $i; ?></option>
                    <?php 
                      } 
                    ?>
                  </select>
                </div>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="large-6 columns">
              <a href="#" id="btn_report" class="small button" onclick="call_ajax('get_report')">Lihat</a>
              <!--<a href="#" id="btn_print_pdf" class="small button" onclick="print_pdf();">Buat PDF</a>-->
            </div>
          </div>
        </form>
      </div>
      
      <div class="row">
        <div class="large-12 columns">
          <div id="div_report"></div>
        </div>
      </div>
    </div>
  </body>
</html>