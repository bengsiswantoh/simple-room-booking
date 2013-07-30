<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/foundation.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/fullcalendar.css">
    <link rel="stylesheet" href="css/flick/jquery-ui-1.10.3.custom.min.css">
    <link rel="stylesheet" href="css/index.css">
    <script type="text/javascript" src="js/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="js/custom.modernizr.js"></script>
    <script type="text/javascript" src="js/foundation.min.js"></script>
    <script type="text/javascript" src="js/fullcalendar.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui-1.10.3.custom.min.js"></script>
    <script type="text/javascript" src="index.js"></script>
  </head>
  <body>
    <div class="row">
      <div class="large-12 columns">
        <h1>Daftar Kamar</h1>
        <hr />
      </div>
    </div>
        
    <div class="row">     
      <div class="large-4 columns">
        <div><a href="#" onclick="open_modal_room('create')" title="Tambah Kamar"><i class="icon-plus"></i> Tambah Kamar</a></div>
        <div id="room_list"></div>
      </div>
      
      <div id="div_room" class="large-4 columns">
        <h2 id="room_label"></h2>
        <div><a href="#" onclick="open_modal_booking('create')" title="Tambah Kamar"><i class="icon-plus"></i> Booking Kamar</a></div>
        <div id="room_calendar"></div>
      </div>
      <div class="large-4 columns">        
        <form id="form_search">
          <label for="search_date">Tanggal</label>
          <input type="text" id="search_date" name="search[date]" />
          <a href="#" id="btn_search" class="small button" onclick="call_ajax('get_booking')">Lihat Kamar</a>
        </form>
        
        <div id="booking_list">
        </div>
      </div>
    </div>
    
    <div id="modal_room" class="reveal-modal small">
      <div class="row">
        <div id="ajax_message_room" class="large-8 columns">
        </div>
      </div>
      
      <form id="form_room">
        <div class="row">
          <input type="hidden" id="room_id" name="room[id]">
          
          <div class="large-10 columns">  
            <label for="room_name">Nama Kamar</label>
            <input type="text" id="room_name" name="room[name]">
          </div>
          
          <div class="large-10 columns">
            <label for="room_gender">Jenis Kelamin</label>
            <select id="room_gender" name="room[gender]">
              <option>Pria</option>
              <option>Wanita</option>
            </select>
          </div>
          
          <div class="large-10 columns">
            <a href="#" id="btn_create" class="small button" onclick="call_ajax('create_room')">Tambah Kamar</a>
            <a href="#" id="btn_update" class="small button" onclick="call_ajax('update_room')">Ubah Nama Kamar</a>
          </div>
        </div>
      </form>
      <a class="close-reveal-modal">&#215;</a>
    </div>

    <div id="modal_booking" class="reveal-modal small">
      <div class="row">
        <div id="ajax_message_book" class="large-8 columns">
        </div>
      </div>
      
      <form id="form_booking">
        <input type="hidden" id="book_id" name="book_id">
        <input type="hidden" id="book_room_id" name="book[room_id]">
        <div class="row">
          <div class="large-6 columns">
            <label for="book_date_in">Dari Tanggal</label>
            <input type="text" id="book_date_in" name="book[date_in]" readonly>
          </div>
          <div class="large-6 columns">
            <label for="book_date_out">Sampai Tanggal</label>
            <input type="text" id="book_date_out" name="book[date_out]" readonly>
          </div>
        </div>
        <div class="row">
          <div class="large-12 columns">
            <label for="book_name">Nama Penghuni</label>
            <input type="text" id="book_name" name="book[name]">
          </div>
        </div>
        <div class="row">
          <div class="large-12 columns">
            <a href="#" id="btn_book" class="small button" onclick="call_ajax('create_booking')">Booking</a>
          </div>
        </div>
      </form>
      <a class="close-reveal-modal">&#215;</a>
    </div>
    
  </body>
</html>